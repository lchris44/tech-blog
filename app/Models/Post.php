<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'short_description',
        'cover',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public $translatable = [
        'title',
        'content',
        'short_description',
    ];

    /**
     * The default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'title' => '{"en": null, "it": null}',
        'content' => '{"en": null, "it": null}',
        'short_description' => '{"en": null, "it": null}',
        'cover' => null,
    ];

    /**
     * Get the user that owns the post.
     *
     * @return BelongsTo<User, Post>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags associated with the post.
     *
     * @return BelongsToMany<Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
