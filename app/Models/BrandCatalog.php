<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandCatalog extends Model
{
    protected $fillable = ['brand_id', 'title', 'notes', 'status', 'link', 'file_path'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
