<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public $translatable = [
        'name',
    ];

    /**
     * The default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'name' => '{"en": null, "it": null}',
    ];

    /**
     * Get the posts associated with the tag.
     *
     * @return BelongsToMany<Post>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
