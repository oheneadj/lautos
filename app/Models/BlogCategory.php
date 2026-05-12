<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
