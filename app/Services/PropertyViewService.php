<?php
namespace App\Services;

use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PropertyViewService
{
    public function track(Property $property, ?User $user = null): bool
    {
        if ($user && $user->isSuperAdmin()) {
            return false;
        }

        $ip = request()->ip();

        $recent = $property->views()
            ->where('viewed_at', '>=', now()->subMinutes(30))
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(!$user, fn($q) => $q->where('ip_address', $ip)->whereNull('user_id'))
            ->exists();

        if ($recent) return false;

        $property->views()->create([
            'user_id'    => $user?->id,
            'ip_address' => $ip,
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'viewed_at'  => now(),
        ]);

        $property->increment('views_count');
        return true;
    }

    public function getStats(Property $property): array
    {
        $byUser = $property->views()
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user:id,name,email')
            ->get();

        return [
            'total'         => (int) $property->views_count,
            'unique_users'  => $property->views()->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'unique_ips'    => $property->views()->distinct('ip_address')->count('ip_address'),
            'by_user'       => $byUser,
        ];
    }
}