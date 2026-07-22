<?php

namespace App\Repositories\Eloquent;

use App\Models\Mission;
use App\Models\Service;
use App\Models\User;
use App\Repositories\Interface\MissionRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class MissionRepositoryEloquent extends BaseRepositoryEloquent implements MissionRepositoryInterface
{
    public function __construct(Mission $model)
    {
        parent::__construct($model);
    }

    public function homeMissions()
    {
        return $this->model
            ->limit(4)
            ->get();
    }


    public function userMissions(User $user, array $filters = [])
    {
        $query = $this->model
            ->newQuery()
            ->when($user->role === 'client', fn ($query) => $query->where('client_id', $user->id))
            ->when($user->role === 'prestataire', function ($query) use ($user) {
                $serviceIds = Service::query()->activeForProvider($user)->select('id');

                $query->where(function ($query) use ($user, $serviceIds) {
                    $query->where('prestataire_id', $user->id)
                        ->orWhere(function ($query) use ($serviceIds) {
                            $query->whereNull('prestataire_id')
                                ->where('status', 'pending')
                                ->whereIn('service_id', $serviceIds);
                        });
                });
            })
            ->with([
                'client:id,first_name,last_name',
                'prestataire:id,first_name,last_name',
                'service:id,name,service_category_id',
                'reviews' => fn ($query) => $query
                    ->where('reviewer_id', $user->id)
                    ->select(['id', 'mission_id', 'reviewer_id', 'rating', 'comment', 'edit_count']),
            ]);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['statuses'])) {
            $statuses = is_array($filters['statuses'])
                ? $filters['statuses']
                : [$filters['statuses']];

            $query->whereIn('status', $statuses);
        }

        if (!empty($filters['prestataire_id'])) {
            $query->where(
                'prestataire_id',
                $filters['prestataire_id']
            );
        }

        if (!empty($filters['date_start'])) {
            $query->whereDate(
                'date_start',
                '>=',
                $filters['date_start']
            );
        }

        if (!empty($filters['date_end'])) {
            $query->whereDate(
                'date_end',
                '<=',
                $filters['date_end']
            );
        }

        if (
            isset($filters['price_min'])
            && $filters['price_min'] !== ''
            && $filters['price_min'] !== null
        ) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (
            isset($filters['price_max'])
            && $filters['price_max'] !== ''
            && $filters['price_max'] !== null
        ) {
            $query->where('price', '<=', $filters['price_max']);
        }

        match ($filters['sort'] ?? 'date_desc') {
            'date_asc' => $query->orderBy('date_start'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->orderByDesc('date_start'),
        };

        return $query
            ->paginate(12)
            ->withQueryString();
    }
}
