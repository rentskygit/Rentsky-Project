<?php

namespace App\Http\Controllers;

use App\Forms\QualificationSchema;
use App\Http\Requests\QualificationRequest;
use App\Models\Qualification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QualificationController extends Controller
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

    protected function isClientUser(?User $user): bool
    {
        if (!$user) return false;

        return $user->roles()->where(function ($q) {
            $q->where('roles.id', 2)
                ->orWhere('roles.slug', 'client')
                ->orWhere('roles.name', 'Client');
        })->exists();
    }

    protected function clientUsers()
    {
        return User::query()
            ->whereHas('roles', function ($q) {
                $q->where('roles.id', 2)
                    ->orWhere('roles.slug', 'client')
                    ->orWhere('roles.name', 'Client');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    protected function canView(Qualification $qualification): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($this->isAdmin()) return true;

        return $qualification->user_id !== null && (int) $qualification->user_id === (int) $user->id;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$this->isAdmin()) {
            $qualification = Qualification::with(['lastEditor'])
                ->where('user_id', $user->id)
                ->first();

            return view('qualifications.index', [
                'qualification' => $qualification,
                'isAdmin'       => false,
            ]);
        }

        $query = Qualification::with(['user', 'lastEditor'])->latest('updated_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($q2) use ($s) {
                    $q2->where('name', 'ILIKE', "%{$s}%")
                        ->orWhere('email', 'ILIKE', "%{$s}%");
                })
                    ->orWhereRaw("(data->>'name') ILIKE ?", ["%{$s}%"])
                    ->orWhereRaw("(data->>'last_name') ILIKE ?", ["%{$s}%"])
                    ->orWhereRaw("(data->>'email') ILIKE ?", ["%{$s}%"])
                    ->orWhereRaw("(data->>'phone') ILIKE ?", ["%{$s}%"]);
            });
        }

        if ($request->filled('type')) {
            if ($request->type === 'voucher') {
                $query->whereRaw("(data->>'has_voucher') = 'true'");
            } elseif ($request->type === 'standard') {
                $query->where(function ($q) {
                    $q->whereRaw("(data->>'has_voucher') IS NULL OR (data->>'has_voucher') != 'true'");
                });
            } elseif ($request->type === 'walkin') {
                $query->whereNull('user_id');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('updated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('updated_at', '<=', $request->date_to);
        }

        $qualifications = $query->paginate(20)->withQueryString();

        return view('qualifications.index', [
            'qualifications' => $qualifications,
            'isAdmin'        => true,
        ]);
    }

    public function show(Qualification $qualification)
    {
        if (!$this->canView($qualification)) {
            abort(403);
        }

        $qualification->load(['user', 'lastEditor']);

        return view('qualifications.show', [
            'qualification' => $qualification,
            'schema'        => QualificationSchema::all(),
            'isAdmin'       => $this->isAdmin(),
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $isStaff = $this->isAdmin();

        $targetUser = null;

        if ($isStaff && $request->filled('user_id')) {
            $targetUser = User::findOrFail($request->user_id);
        } elseif (!$isStaff) {
            $targetUser = $user;

            $existing = Qualification::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('qualifications.edit', $existing);
            }
        }

        return view('qualifications.form', [
            'qualification' => null,
            'schema'        => QualificationSchema::all(),
            'standardPath'  => QualificationSchema::standardPath(),
            'voucherPath'   => QualificationSchema::voucherPath(),
            'initialData'   => [],
            'targetUser'    => $targetUser,
            'users'         => $isStaff ? $this->clientUsers() : collect(),
            'isAdmin'       => $isStaff,
        ]);
    }

    public function checkUser(User $user)
    {
        $existing = Qualification::where('user_id', $user->id)->first();

        if ($existing) {
            return response()->json([
                'exists'    => true,
                'user_name' => $user->name,
                'edit_url'  => route('qualifications.edit', $existing),
            ]);
        }

        return response()->json([
            'exists'    => false,
            'user_name' => $user->name,
        ]);
    }

    public function userConflict(Qualification $qualification, User $user)
    {
        if (!$this->isAdmin()) {
            abort(403);
        }

        $existing = Qualification::where('user_id', $user->id)
            ->where('id', '!=', $qualification->id)
            ->first();

        return response()->json([
            'has_conflict'     => $existing !== null,
            'qualification_id' => $existing?->id,
            'user_name'        => $user->name,
        ]);
    }

    public function store(QualificationRequest $request)
    {
        $isStaff = $this->isAdmin();
        $targetUserId = null;

        if ($isStaff) {
            if ($request->filled('user_id')) {
                $candidate = User::find($request->user_id);

                if (!$this->isClientUser($candidate)) {
                    return back()->withInput()->withErrors([
                        'user_id' => 'The selected user must have the Client role.',
                    ]);
                }

                if (Qualification::where('user_id', $candidate->id)->exists()) {
                    return redirect()->route('qualifications.index')
                        ->with('error', 'A qualification for this user already exists.');
                }

                $targetUserId = $candidate->id;
            }
        } else {
            $targetUserId = auth()->id();

            if (Qualification::where('user_id', $targetUserId)->exists()) {
                return redirect()->route('qualifications.index')
                    ->with('error', 'A qualification for this user already exists.');
            }
        }

        $qualification = Qualification::create([
            'user_id'        => $targetUserId,
            'data'           => $this->extractData($request),
            'last_edited_by' => auth()->id(),
            'submitted_at'   => now(),
        ]);

        return redirect()->route('qualifications.show', $qualification)
            ->with('success', $targetUserId === null
                ? 'Walk-in qualification submitted successfully.'
                : 'Qualification submitted successfully.');
    }

    public function edit(Qualification $qualification)
    {
        if (!$this->canView($qualification)) {
            abort(403);
        }

        return view('qualifications.form', [
            'qualification' => $qualification,
            'schema'        => QualificationSchema::all(),
            'standardPath'  => QualificationSchema::standardPath(),
            'voucherPath'   => QualificationSchema::voucherPath(),
            'initialData'   => $qualification->data ?? [],
            'targetUser'    => $qualification->user,
            'users'         => $this->isAdmin() ? $this->clientUsers() : collect(),
            'isAdmin'       => $this->isAdmin(),
        ]);
    }

    public function update(QualificationRequest $request, Qualification $qualification)
    {
        if (!$this->canView($qualification)) {
            abort(403);
        }

        $updates = [
            'data'           => $this->extractData($request),
            'last_edited_by' => auth()->id(),
        ];

        if ($this->isAdmin()) {
            $newUserId = null;

            if ($request->filled('user_id')) {
                $candidate = User::find($request->user_id);

                if (!$this->isClientUser($candidate)) {
                    return back()->withInput()->withErrors([
                        'user_id' => 'The selected user must have the Client role.',
                    ]);
                }

                $existing = Qualification::where('user_id', $candidate->id)
                    ->where('id', '!=', $qualification->id)
                    ->first();

                if ($existing) {
                    if (!$request->boolean('confirm_overwrite')) {
                        return back()->withInput()->withErrors([
                            'user_id' => "The selected user already has a qualification (#{$existing->id}). Confirm overwrite to replace it.",
                        ]);
                    }

                    $existing->delete();
                }

                $newUserId = $candidate->id;
            }

            $updates['user_id'] = $newUserId;
        }

        $qualification->update($updates);

        return redirect()->route('qualifications.show', $qualification)
            ->with('success', 'Qualification updated successfully.');
    }

    public function pdf(Qualification $qualification)
    {
        if (!$this->canView($qualification)) {
            abort(403);
        }

        $qualification->load(['user', 'lastEditor']);

        $pdf = Pdf::loadView('qualifications.pdf', [
            'qualification' => $qualification,
            'schema'        => QualificationSchema::all(),
        ])->setPaper('letter', 'portrait');

        $filename = 'qualification-' . ($qualification->user_id ?? 'walkin-' . $qualification->id)
            . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    protected function extractData(QualificationRequest $request): array
    {
        $data = [];

        foreach (QualificationSchema::all() as $page) {
            foreach ($page['fields'] ?? [] as $key => $field) {
                if ($field['type'] === 'checkbox') {
                    $data[$key] = $request->boolean($key);
                } elseif ($field['type'] === 'multiselect') {
                    $data[$key] = $request->input($key, []) ?? [];
                } else {
                    $value = $request->input($key);
                    $data[$key] = ($value === '' || $value === null) ? null : $value;
                }
            }
        }

        return $data;
    }
}
