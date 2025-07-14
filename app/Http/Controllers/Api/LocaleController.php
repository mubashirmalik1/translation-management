<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocaleRequest;
use App\Http\Requests\UpdateLocaleRequest;
use App\Models\Locale;
use App\Services\LocaleService;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * @OA\Get(
     *     path="/api/locales",
     *     summary="Get a paginated list of locales",
     *     tags={"Locale"},
     *     @OA\Response(
     *         response=200,
     *         description="Language retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $locales = $this->localeService->getAllLocales();
        return response()->success($locales, 'language retrieve successfully.', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/locales",
     *     summary="Store a new locale",
     *     tags={"Locale"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass locale data",
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", example="en"),
     *             @OA\Property(property="name", type="string", example="English")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Language created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreLocaleRequest $request)
    {
        $data = $request->validated();

        $locale = $this->localeService->createLocale($data);

        return response()->success($locale, 'Language created successfully.', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/locales/{locale}",
     *     summary="Get a single locale",
     *     tags={"Locale"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="path",
     *         required=true,
     *         description="ID of locale to return",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Language retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function show(Locale $locale)
    {
        return response()->success($locale, 'Language retrieved successfully.', 200);
    }

    /**
     * @OA\Put(
     *     path="/api/locales/{locale}",
     *     summary="Update an existing locale",
     *     tags={"Locale"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="path",
     *         required=true,
     *         description="ID of locale to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass updated locale data",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", example="fr"),
     *             @OA\Property(property="name", type="string", example="French")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Language updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateLocaleRequest $request, Locale $locale)
    {
        $data = $request->validated();

        $this->localeService->updateLocale($locale, $data);

        return response()->success($locale, 'Language updated successfully.', 200);

    }

    /**
     * @OA\Delete(
     *     path="/api/locales/{locale}",
     *     summary="Delete a locale",
     *     tags={"Locale"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="path",
     *         required=true,
     *         description="ID of locale to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Language deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function destroy(Locale $locale)
    {
        $this->localeService->deleteLocale($locale);
        return response()->success([], 'language delete successfully.', 204);
    }
}
