<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',          // nom de la catégorie : Plomberie, Électricité, Jardinage…
        'description',   // description optionnelle
        'icon',          // icône ou pictogramme
        'is_active',     // statut d’activation
    ];

    /**
     * Services appartenant à cette catégorie
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
