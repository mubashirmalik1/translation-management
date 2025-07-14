<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationService
{
    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAllTranslations(Request $request): LengthAwarePaginator
    {
        return Translation::query()->with('tags', 'locale')->paginate(50);
    }

    /**
     * @param array $data
     * @return Translation
     */
    public function createTranslation(array $data): Translation
    {
        $translation = Translation::create($data);

        if (!empty($data['tags'])) {
            $this->syncTags($translation, $data['tags']);
        }

        return $translation;
    }

    /**
     * @param Translation $translation
     * @param array $data
     * @return Translation
     */
    public function updateTranslation(Translation $translation, array $data): Translation
    {
        $translation->update($data);

        if (isset($data['tags'])) {
            $this->syncTags($translation, $data['tags']);
        }

        return $translation;
    }

    /**
     * @param Translation $translation
     * @return bool|null
     */
    public function deleteTranslation(Translation $translation): ?bool
    {
        return $translation->delete();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function searchTranslations(Request $request): LengthAwarePaginator
    {
        $query = Translation::query()->with('tags');

        if ($request->filled('translation_key')) {
            $query->where('translation_key', 'like', '%' . $request->translation_key . '%');
        }

        if ($request->filled('translation_content')) {
            $query->where('translation_content', 'like', '%' . $request->translation_content . '%');
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->tag . '%');
            });
        }

        return $query->paginate(50);
    } 

    /**
     * @param Translation $translation
     * @param array $tags
     */
    private function syncTags(Translation $translation, array $tags): void
    {
        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }
        $translation->tags()->sync($tagIds);
    }
} 