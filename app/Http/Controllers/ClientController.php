<?php

namespace App\Http\Controllers;

use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->whereHas('roles', fn($q) => $q->where('roles.id', 2))
            ->with([
                'roles',
                'assignedAdviser:id,name,email,is_super_admin',
                'assignedAdviser.roles:id,name,slug',
                'propertyOfInterest:id,address,unit_type,city,state,slug',
                'qualification',
                'application',
            ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'ILIKE', "%{$s}%")
                  ->orWhere('email', 'ILIKE', "%{$s}%")
                  ->orWhere('assigned_adviser_id', '!=', null);
            });
        }

        if ($request->filled('status')) {
            $this->applyStatusFilter($query, $request->status);
        }

        if ($request->filled('agent')) {
            if ($request->agent === 'unassigned') {
                $query->whereNull('assigned_adviser_id');
            } else {
                $query->where('assigned_adviser_id', $request->agent);
            }
        }

        if ($request->filled('property')) {
            if ($request->property === 'unassigned') {
                $query->whereNull('property_of_interest_id');
            } else {
                $query->where('property_of_interest_id', $request->property);
            }
        }

        $clients = $query->orderBy('name')->paginate(20)->withQueryString();

        $agents = User::query()
            ->where(function ($q) {
                $q->where('is_super_admin', true)
                  ->orWhereHas('roles', function ($r) {
                      $r->where('roles.id', 1)
                        ->orWhereIn('roles.slug', ['admin', 'adviser', 'advisor'])
                        ->orWhereIn('roles.name', ['Admin', 'Adviser', 'Advisor']);
                  });
            })
            ->with(['roles:id,name,slug'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_super_admin']);

        $properties = Property::active()
            ->orderBy('address')
            ->get(['id', 'address', 'unit_type', 'city', 'state']);

        return view('clients.index', compact('clients', 'agents', 'properties'));
    }

    public function assign(Request $request, User $user)
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Only administrators can assign agents or properties.');

        $validated = $request->validate([
            'assigned_adviser_id'     => ['nullable', 'integer', 'exists:users,id'],
            'property_of_interest_id' => ['nullable', 'integer', 'exists:properties,id'],
        ]);

        if (!empty($validated['assigned_adviser_id'])) {
            $agent = User::find($validated['assigned_adviser_id']);

            if (!$agent || !$agent->canBeAssignedAsAgent()) {
                return back()->with('error', 'The selected user cannot be assigned as an agent.');
            }

            if ($agent->isClient() && !$agent->isAdmin() && !$agent->isAdviser()) {
                return back()->with('error', 'A client cannot be assigned as an agent.');
            }
        }

        $user->update([
            'assigned_adviser_id'     => $validated['assigned_adviser_id'] ?? null,
            'property_of_interest_id' => $validated['property_of_interest_id'] ?? null,
        ]);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client assignment updated successfully.');
    }

    protected function applyStatusFilter($query, string $status): void
    {
        switch ($status) {
            case 'application':
                $query->whereHas('application');
                break;

            case 'showing':
                $query->whereDoesntHave('application')
                      ->whereHas('qualification', function ($q) {
                          $q->whereRaw("(qualifications.data->>'schedule_now') = 'Yes'");
                      });
                break;

            case 'qualified':
                $query->whereDoesntHave('application')
                      ->whereHas('qualification')
                      ->whereDoesntHave('qualification', function ($q) {
                          $q->whereRaw("(qualifications.data->>'schedule_now') = 'Yes'");
                      });
                break;

            case 'new':
                $query->whereDoesntHave('application')
                      ->whereDoesntHave('qualification');
                break;
        }
    }
}