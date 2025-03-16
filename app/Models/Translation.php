<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    // Now we store 'locale_id' instead of a locale string.
    protected $fillable = ['locale_id', 'translation_key', 'translation_content'];

    /**
     * Relationship: A translation belongs to a locale.
     */
    public function locale()
    {
        return $this->belongsTo(Locale::class, 'locale_id');
    }

    /**
     * Relationship: A translation can have multiple tags.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_translation');
    }
}
