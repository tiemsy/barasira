<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class MissionFilters
{
    protected Builder $query;
    protected $filters;

    public function __construct(Builder $query, $filters)
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    public function apply()
    {
        foreach ($this->filters as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $method = 'filter' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this->query;
    }

    /* -------------------------
       Filtres individuels
    ------------------------- */

    public function filterSearch($value)
    {
        $this->query->where(function ($q) use ($value) {
            $q->where('title', 'LIKE', "%$value%")
              ->orWhere('description', 'LIKE', "%$value%");
        });
    }

    public function filterStatuses($value)
    {
        $statuses = is_array($value) ? $value : explode(',', $value);
        $this->query->whereIn('status', $statuses);
    }

    public function filterDate_start($value)
    {
        $this->query->whereDate('date_start', '>=', $value);
    }

    public function filterDate_end($value)
    {
        $this->query->whereDate('date_end', '<=', $value);
    }

    public function filterPrestataire_id($value)
    {
        $this->query->where('prestataire_id', $value);
    }

    public function filterPrice_min($value)
    {
        $this->query->where('price', '>=', $value);
    }

    public function filterPrice_max($value)
    {
        $this->query->where('price', '<=', $value);
    }

    // public function filterCategory($value)
    // {
    //     $this->query->where('category', $value);
    // }

    public function filterLocation($value)
    {
        $this->query->where('address', $value);
    }

    public function filterSort($value)
    {
        switch ($value) {
            case 'date_asc':
                $this->query->orderBy('date_start', 'asc');
                break;

            case 'date_desc':
                $this->query->orderBy('date_start', 'desc');
                break;

            case 'price_asc':
                $this->query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $this->query->orderBy('price', 'desc');
                break;

            case 'distance_asc':
                if (request()->filled(['latitude', 'longitude'])) {
                    $lat = request()->lat;
                    $lng = request()->lng;

                    $this->query->selectRaw("
                        missions.*,
                        (6371 * acos(
                            cos(radians($lat)) *
                            cos(radians(latitude)) *
                            cos(radians(longitude) - radians($lng)) +
                            sin(radians($lat)) *
                            sin(radians(latitude))
                        )) AS distance
                    ")
                    ->orderBy('distance', 'asc');
                }
                break;
        }
    }
}
