<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'description', 'classification', 'logo'];

    public function catalogs(): HasMany
    {
        return $this->hasMany(BrandCatalog::class);
    }
}
