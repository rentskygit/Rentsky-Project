<?php

namespace App\Http\Controllers;

use App\Forms\ApplicationSchema;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    protected function isAdmin(): bool
    {
        $u = auth()->user();
        if (!$u) return false;
        if ($u->isSuperAdmin()) return true;

        return $u->roles()->where(function ($q) {
            $q->where('roles.id', 1)
              ->orWhereIn('roles.slug', ['admin', 'adviser', 'advisor'])
              ->orWhereIn('roles.name', ['Admin', 'Adviser', 'Advisor']);
        })->exists();
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$this->isAdmin()) {
            $application = Application::with(['lastEditor'])
                ->where('user_id', $user->id)
                ->first();

            return view('applications.index', [
                'application' => $application,
                'isAdmin'     => false,
            ]);
        }

        $query = Application::with(['user', 'lastEditor'])->latest('updated_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($q2) use ($s) {
                    $q2->where('name', 'ILIKE', "%{$s}%")
                       ->orWhere('email', 'ILIKE', "%{$s}%");
                })
                ->orWhereRaw("(data->>'legal_first_name') ILIKE ?", ["%{$s}%"])
                ->orWhereRaw("(data->>'last_name') ILIKE ?", ["%{$s}%"])
                ->orWhereRaw("(data->>'email') ILIKE ?", ["%{$s}%"])
                ->orWhereRaw("(data->>'phone') ILIKE ?", ["%{$s}%"]);
            });
        }

        if ($request->filled('type')) {
            if (in_array($request->type, ['tenant', 'guarantor'], true)) {
                $query->whereRaw("(data->>'applicant_type') = ?", [$request->type]);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('updated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('updated_at', '<=', $request->date_to);
        }

        $applications = $query->paginate(20)->withQueryString();

        return view('applications.index', [
            'applications' => $applications,
            'isAdmin'      => true,
        ]);
    }

    public function show(Application $application)
    {
        $user = auth()->user();

        if (!$this->isAdmin() && $application->user_id !== $user->id) {
            abort(403);
        }

        $application->load(['user', 'lastEditor']);

        return view('applications.show', [
            'application' => $application,
            'schema'      => ApplicationSchema::all(),
            'isAdmin'     => $this->isAdmin(),
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $targetUser = null;
        if ($this->isAdmin() && $request->filled('user_id')) {
            $targetUser = User::findOrFail($request->user_id);
        } elseif (!$this->isAdmin()) {
            $targetUser = $user;

            $existing = Application::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('applications.edit', $existing);
            }
        }

        return view('applications.form', [
            'application' => null,
            'schema'      => ApplicationSchema::all(),
            'path'        => ApplicationSchema::path(),
            'initialData' => [],
            'targetUser'  => $targetUser,
            'users'       => $this->isAdmin() ? User::orderBy('name')->get() : collect(),
            'isAdmin'     => $this->isAdmin(),
        ]);
    }

    public function checkUser(User $user)
    {
        $existing = Application::where('user_id', $user->id)->first();

        if ($existing) {
            return response()->json([
                'exists'    => true,
                'user_name' => $user->name,
                'edit_url'  => route('applications.edit', $existing),
            ]);
        }

        return response()->json([
            'exists'    => false,
            'user_name' => $user->name,
        ]);
    }

    public function store(ApplicationRequest $request)
    {
        $targetUserId = $this->isAdmin() && $request->filled('user_id')
            ? (int) $request->user_id
            : auth()->id();

        if (Application::where('user_id', $targetUserId)->exists()) {
            return redirect()->route('applications.index')
                ->with('error', 'An application for this user already exists.');
        }

        $application = Application::create([
            'user_id'        => $targetUserId,
            'data'           => $this->extractData($request),
            'last_edited_by' => auth()->id(),
            'submitted_at'   => now(),
        ]);

        return redirect()->route('applications.show', $application)
            ->with('success', 'Application submitted successfully.');
    }

    public function edit(Application $application)
    {
        $user = auth()->user();

        if (!$this->isAdmin() && $application->user_id !== $user->id) {
            abort(403);
        }

        return view('applications.form', [
            'application' => $application,
            'schema'      => ApplicationSchema::all(),
            'path'        => ApplicationSchema::path(),
            'initialData' => $application->data ?? [],
            'targetUser'  => $application->user,
            'users'       => $this->isAdmin() ? User::orderBy('name')->get() : collect(),
            'isAdmin'     => $this->isAdmin(),
        ]);
    }

    public function update(ApplicationRequest $request, Application $application)
    {
        $user = auth()->user();

        if (!$this->isAdmin() && $application->user_id !== $user->id) {
            abort(403);
        }

        $application->update([
            'data'           => $this->extractData($request),
            'last_edited_by' => auth()->id(),
        ]);

        return redirect()->route('applications.show', $application)
            ->with('success', 'Application updated successfully.');
    }

    public function pdf(Application $application)
    {
        $user = auth()->user();

        if (!$this->isAdmin() && $application->user_id !== $user->id) {
            abort(403);
        }

        $application->load(['user', 'lastEditor']);

        $pdf = Pdf::loadView('applications.pdf', [
            'application' => $application,
            'schema'      => ApplicationSchema::all(),
        ])->setPaper('letter', 'portrait');

        $filename = 'application-' . $application->user_id . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    protected function extractData(ApplicationRequest $request): array
    {
        $input = $request->all();
        $result = [];

        $showPrev = ApplicationSchema::shouldShowPreviousAddresses($input);

        foreach (ApplicationSchema::all() as $pageKey => $page) {
            if ($pageKey === 'previous_addresses' && !$showPrev) continue;

            foreach ($page['fields'] ?? [] as $key => $field) {
                if ($field['type'] === 'heading') continue;
                if ($field['type'] === 'subheading') continue;
                if (!ApplicationSchema::isFieldVisible($key, $input)) continue;

                if ($field['type'] === 'checkbox') {
                    $result[$key] = $request->boolean($key);
                } else {
                    $value = $request->input($key);
                    $result[$key] = ($value === '' || $value === null) ? null : $value;
                }
            }
        }

        return $result;
    }
}