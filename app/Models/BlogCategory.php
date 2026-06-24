<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

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
