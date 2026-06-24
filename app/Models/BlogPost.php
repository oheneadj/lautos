<?php

/**
 * @author Ohene Adjei
 */

namespace App\Models;

use App\Enums\BlogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'uuid',
        'blog_category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image_path',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => BlogStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * The public blog route looks posts up by slug manually, but admin
     * (Filament) resolves the record via route-model-binding — this is
     * what actually makes that use uuid instead of the integer id.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $post) {
            if (empty($post->uuid)) {
                $post->uuid = (string) Str::uuid();
            }

            $post->slug = static::uniqueSlug($post->slug ?: Str::slug($post->title));
        });

        // I re-check slug uniqueness on update in case the title changed and the slug was regenerated.
        static::updating(function (self $post) {
            if ($post->isDirty('slug')) {
                $post->slug = static::uniqueSlug($post->slug, $post->id);
            }
        });
    }

    // I append an incrementing suffix (-2, -3 …) until the slug is unique.
    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug      = $base;
        $suffix    = 2;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Estimated reading time in whole minutes, based on a 200 words-per-minute
     * pace — I strip tags first so HTML markup doesn't inflate the word count.
     */
    public function getReadTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags((string) $this->body));

        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * The full public URL for the cover image — cover_image_path only stores
     * the relative disk path, so every view needs this instead of the raw column.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path ? Storage::url($this->cover_image_path) : null;
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', BlogStatus::Published)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
