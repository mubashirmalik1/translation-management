<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(
 *      title="Translation Management API",
 *      version="1.0.0",
 *      description="API documentation for the Translation Management Service",
 *      @OA\Contact(
 *          email="malikmubashir601@gmail.com"
 *      )
 * )
 */
class TranslationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/translations",
     *     summary="Get a paginated list of translations",
     *     tags={"Translation"},
     *     @OA\Response(
     *         response=200,
     *         description="Translation retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $translations = Translation::query()->with('tags', 'locale')->paginate(50);

        return response()->success($translations, 'Translation retrieved successfully.', 200);

    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Store a new translation",
     *     tags={"Translation"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass translation data",
     *         @OA\JsonContent(
     *             required={"locale_id","translation_key","translation_content"},
     *             @OA\Property(property="locale_id", type="integer", example="1"),
     *             @OA\Property(property="translation_key", type="string", example="welcome.message"),
     *             @OA\Property(property="translation_content", type="string", example="Welcome to our site!"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string", example="greeting")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreTranslationRequest $request)
    {
        $data = $request->validated();

        $translation = Translation::create($data);

        if (!empty($request->tags)) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $translation->tags()->sync($tagIds);
        }

        return response()->success($translation, 'Translation Create successfully.', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/{translation}",
     *     summary="Get a single translation",
     *     tags={"Translation"},
     *     @OA\Parameter(
     *         name="translation",
     *         in="path",
     *         required=true,
     *         description="ID of translation to return",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function show(Translation $translation)
    {
        return response()->success($translation, 'Translation Retrieve successfully.', 200);
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{translation}",
     *     summary="Update an existing translation",
     *     tags={"Translation"},
     *     @OA\Parameter(
     *         name="translation",
     *         in="path",
     *         required=true,
     *         description="ID of translation to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass updated translation data",
     *         @OA\JsonContent(
     *             @OA\Property(property="translation_key", type="string", example="hello.updated"),
     *             @OA\Property(property="translation_content", type="string", example="Hello Updated"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string", example="greeting")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateTranslationRequest $request, Translation $translation)
    {
        $data = $request->validated();

        $translation->update($data);

        if (isset($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $translation->tags()->sync($tagIds);
        }

        return response()->success($translation, 'Translation update successfully.', 200);

    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{translation}",
     *     summary="Delete a translation",
     *     tags={"Translation"},
     *     @OA\Parameter(
     *         name="translation",
     *         in="path",
     *         required=true,
     *         description="ID of translation to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();
        return response()->success([], 'Translation delete successfully.', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/search",
     *     summary="Search translations",
     *     tags={"Translation"},
     *     @OA\Parameter(
     *         name="translation_key",
     *         in="query",
     *         description="Search by translation key",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="translation_content",
     *         in="query",
     *         description="Search by translation content",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Search by tag name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation retrieve successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */

    public function search(Request $request)
    {
        $query = Translation::query()->with('tags');

        if ($request->filled('translation_key')) {
            $query->where('translation_key', 'like', '%' . $request->translation_key . '%');
        }

        if ($request->filled('translation_content')) {
            $query->where('translation_content', 'like', '%' . $request->translation_content . '%');
        }

        if ($request->filled('tag')) {
            $query = $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }
        $translations = $query->paginate(20);

        return response()->success($translations, 'Translation retrieve successfully.', 200);

    }

    /**
     * @OA\Get(
     *     path="/api/translations/export",
     *     summary="Export translations as JSON",
     *     tags={"Translation"},
     *     @OA\Response(
     *         response=200,
     *         description="Translation retrieve successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function export()
    {
        $translations = [];
        DB::table('translations')
            ->select('id','translation_key','translation_content')
            ->orderBy('id')
            ->chunk(1000, function ($chunk) use (&$translations) {
            foreach ($chunk as $translation) {
                $translations[] = [
                    'id' => $translation->id,
                    'translation_key' => $translation->translation_key,
                    'translation_content' => $translation->translation_content,
                ];
            }
        });

        return response()->success($translations, 'Translation retrieve successfully.', 200);
    }
}
