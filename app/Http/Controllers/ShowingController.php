<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShowingRequest;
use App\Http\Requests\UpdateShowingRequest;
use App\Models\Property\Property;
use App\Models\Showing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShowingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isClient() && !$user->canManageShowings()) {
            $showings = Showing::with('property.primaryImage')
                ->where('user_id', $user->id)
                ->orderByDesc('showing_date')
                ->orderByDesc('showing_time')
                ->paginate(15);

            return view('showings.index', compact('showings'));
        }

        if ($user->canManageShowings()) {
            return $this->adminList($user, $request);
        }

        abort(403);
    }

    protected function adminList(User $user, Request $request)
    {
        $isAdmin   = $user->isAdmin();
        $isManager = $user->isManager();
        $canManage = $user->canManageShowings();

        $query = User::query()
            ->whereHas('roles', fn($q) => $q->where('roles.id', 2))
            ->whereHas('showings')
            ->withCount('showings')
            ->withMax('showings', 'created_at')
            ->with(['assignedAdviser:id,name', 'qualification', 'application']);

        if (!$isAdmin && !$isManager && $user->isAdviser()) {
            $query->where('assigned_adviser_id', $user->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'ILIKE', "%{$s}%")
                  ->orWhere('email', 'ILIKE', "%{$s}%");
            });
        }

        if ($request->filled('adviser')) {
            if ($request->adviser === 'unassigned') {
                $query->whereNull('assigned_adviser_id');
            } else {
                $query->where('assigned_adviser_id', $request->adviser);
            }
        }

        if ($request->filled('min_showings') && is_numeric($request->min_showings)) {
            $query->has('showings', '>=', (int) $request->min_showings);
        }

        switch ($request->get('sort', 'latest')) {
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'showings_desc':
                $query->orderByDesc('showings_count');
                break;
            case 'latest':
            default:
                $query->orderByDesc('showings_max_created_at');
        }

        $clients = $query->paginate(20)->withQueryString();

        $showableClients = User::query()
            ->whereHas('roles', fn($q) => $q->where('roles.id', 2))
            ->when(!$isAdmin && !$isManager && $user->isAdviser(), function ($q) use ($user) {
                $q->where('assigned_adviser_id', $user->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $properties = Property::active()
            ->orderBy('address')
            ->get(['id', 'address', 'unit_type', 'city', 'state']);

        $agents = User::query()
            ->where(function ($q) {
                $q->where('is_super_admin', true)
                  ->orWhereHas('roles', function ($r) {
                      $r->where('roles.id', 1)
                        ->orWhereIn('roles.slug', ['admin', 'adviser', 'advisor'])
                        ->orWhereIn('roles.name', ['Admin', 'Adviser', 'Advisor']);
                  });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('showings.admin', compact(
            'clients',
            'isAdmin',
            'agents',
            'showableClients',
            'properties',
            'canManage'
        ));
    }

    public function showUser(Request $request, User $user)
    {
        $current = auth()->user();

        if (!$current->canManageShowings()) {
            abort(403);
        }

        if (!$user->isClient()) {
            abort(404);
        }

        if (!$current->isAdmin() && !$current->isManager() && $current->isAdviser()) {
            if ((int) $user->assigned_adviser_id !== (int) $current->id) {
                abort(403, 'You can only view showings for your assigned clients.');
            }
        }

        $query = Showing::with('property.primaryImage')
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('showing_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('showing_date', '<=', $request->date_to);
        }

        if ($request->filled('property')) {
            $query->where('property_id', $request->property);
        }

        switch ($request->get('sort', 'date_desc')) {
            case 'date_asc':
                $query->orderBy('showing_date')->orderBy('showing_time');
                break;
            case 'date_desc':
            default:
                $query->orderByDesc('showing_date')->orderByDesc('showing_time');
        }

        $showings = $query->get();

        $properties = Property::active()
            ->orderBy('address')
            ->get(['id', 'address', 'unit_type', 'city', 'state']);

        $clientFullDates = Showing::where('user_id', $user->id)
            ->selectRaw('showing_date, COUNT(*) as cnt')
            ->groupBy('showing_date')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('showing_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->values()
            ->toArray();

        return view('showings.user', [
            'client'          => $user,
            'showings'        => $showings,
            'isAdmin'         => $current->isAdmin(),
            'properties'      => $properties,
            'clientFullDates' => $clientFullDates,
            'canManage'       => $current->canManageShowings(),
        ]);
    }

    public function userFullDates(User $user)
    {
        $current = auth()->user();

        if (!$current || !$current->canManageShowings()) {
            abort(403);
        }

        if (!$current->isAdmin() && !$current->isManager() && $current->isAdviser()) {
            if ((int) $user->assigned_adviser_id !== (int) $current->id) {
                abort(403);
            }
        }

        $dates = Showing::where('user_id', $user->id)
            ->selectRaw('showing_date, COUNT(*) as cnt')
            ->groupBy('showing_date')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('showing_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->values()
            ->toArray();

        return response()->json(['full_dates' => $dates]);
    }

    public function eligibility(int $propertyId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['authenticated' => false]);
        }

        $datesWithThree = Showing::where('user_id', $user->id)
            ->selectRaw('showing_date, COUNT(*) as cnt')
            ->groupBy('showing_date')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('showing_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->values()
            ->toArray();

        return response()->json([
            'authenticated'     => true,
            'has_qualification' => $user->has_qualification,
            'qualification_url' => route('qualifications.index'),
            'full_dates'        => $datesWithThree,
        ]);
    }

    public function store(StoreShowingRequest $request)
    {
        $data = $request->validated();

        $showing = Showing::create([
            'user_id'      => auth()->id(),
            'property_id'  => $data['property_id'],
            'showing_date' => $data['showing_date'],
            'showing_time' => $data['showing_time'],
            'status'       => 'pending',
        ]);

        return redirect()
            ->route('apartments.show', $showing->property->slug)
            ->with('success', 'Showing scheduled successfully for ' . $showing->formatted_date_time . '.');
    }

    public function quickStore(Request $request)
    {
        $current = auth()->user();

        if (!$current || !$current->canManageShowings()) {
            abort(403, 'You do not have permission to create showings for other users.');
        }

        $validator = Validator::make($request->all(), [
            'user_id'      => ['required', 'integer', 'exists:users,id'],
            'property_id'  => ['required', 'integer', 'exists:properties,id'],
            'showing_date' => ['required', 'date', 'after_or_equal:today'],
            'showing_time' => ['required', Rule::in([
                '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
                '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM',
                '6:00 PM', '7:00 PM', '8:00 PM',
            ])],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
        ]);

        $validator->after(function ($v) use ($request, $current) {
            $userId = (int) $request->input('user_id');
            $target = User::find($userId);

            if (!$target || !$target->isClient()) {
                $v->errors()->add('user_id', 'Please select a valid client.');
                return;
            }

            if (!$current->isAdmin() && !$current->isManager() && $current->isAdviser()) {
                if ((int) $target->assigned_adviser_id !== (int) $current->id) {
                    $v->errors()->add('user_id', 'You can only create showings for your assigned clients.');
                    return;
                }
            }

            $date = $request->input('showing_date');
            if ($date) {
                $count = Showing::where('user_id', $userId)
                    ->where('showing_date', $date)
                    ->count();

                if ($count >= 3) {
                    $v->errors()->add('showing_date', 'This client already has 3 showings on that day.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        Showing::create([
            'user_id'      => $data['user_id'],
            'property_id'  => $data['property_id'],
            'showing_date' => $data['showing_date'],
            'showing_time' => $data['showing_time'],
            'status'       => $data['status'] ?? 'pending',
        ]);

        $target = User::find($data['user_id']);

        return redirect()
            ->route('showings.index')
            ->with('success', 'Showing created successfully for ' . ($target->name ?? 'client') . '.');
    }

    public function storeForUser(Request $request, User $user)
    {
        $current = auth()->user();

        if (!$current->canManageShowings()) {
            abort(403, 'You do not have permission to create showings for other users.');
        }

        if (!$current->isAdmin() && !$current->isManager() && $current->isAdviser()) {
            if ((int) $user->assigned_adviser_id !== (int) $current->id) {
                abort(403, 'You can only create showings for your assigned clients.');
            }
        }

        if (!$user->isClient()) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'property_id'  => ['required', 'integer', 'exists:properties,id'],
            'showing_date' => ['required', 'date', 'after_or_equal:today'],
            'showing_time' => ['required', Rule::in([
                '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
                '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM',
                '6:00 PM', '7:00 PM', '8:00 PM',
            ])],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
        ]);

        $validator->after(function ($v) use ($request, $user) {
            $date = $request->input('showing_date');
            if ($date) {
                $count = Showing::where('user_id', $user->id)
                    ->where('showing_date', $date)
                    ->count();

                if ($count >= 3) {
                    $v->errors()->add('showing_date', 'This client already has 3 showings on that day.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        Showing::create([
            'user_id'      => $user->id,
            'property_id'  => $data['property_id'],
            'showing_date' => $data['showing_date'],
            'showing_time' => $data['showing_time'],
            'status'       => $data['status'] ?? 'pending',
        ]);

        return redirect()
            ->route('showings.user', $user)
            ->with('success', 'Showing created successfully for ' . $user->name . '.');
    }

    public function update(UpdateShowingRequest $request, Showing $showing)
    {
        $user = auth()->user();
        $isOwner = (int) $showing->user_id === (int) $user->id;

        $hasAccess = $isOwner || $user->isAdmin() || $user->isManager();

        if (!$hasAccess && $user->isAdviser()) {
            $owner = $showing->user;
            if ($owner && (int) $owner->assigned_adviser_id === (int) $user->id) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            abort(403);
        }

        $showing->update($request->validated());

        return back()->with('success', 'Showing updated successfully.');
    }

    public function updateStatus(Request $request, Showing $showing)
    {
        $user = auth()->user();

        if (!$user->canManageShowings()) {
            abort(403);
        }

        if (!$user->isAdmin() && !$user->isManager() && $user->isAdviser()) {
            $owner = $showing->user;
            if ((int) $owner->assigned_adviser_id !== (int) $user->id) {
                abort(403, 'You can only update showings for your assigned clients.');
            }
        }

        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
        ]);

        $showing->update(['status' => $data['status']]);

        return back()->with('success', 'Showing status updated to ' . $showing->status_label . '.');
    }

    public function destroy(Showing $showing)
    {
        $user = auth()->user();
        $isOwner = (int) $showing->user_id === (int) $user->id;

        if (!$isOwner && !$user->isAdmin() && !$user->isManager()) {
            abort(403);
        }

        $showing->delete();

        return back()->with('success', 'Showing deleted successfully.');
    }
}