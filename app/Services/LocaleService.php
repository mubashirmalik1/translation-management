<?php

namespace App\Services;

use App\Models\Locale;
use Illuminate\Pagination\LengthAwarePaginator;

class LocaleService
{
    /**
     * @return LengthAwarePaginator
     */
    public function getAllLocales(): LengthAwarePaginator
    {
        return Locale::paginate(10);
    }

    /**
     * @param array $data
     * @return Locale
     */
    public function createLocale(array $data): Locale
    {
        return Locale::create($data);
    }

    /**
     * @param Locale $locale
     * @param array $data
     * @return bool
     */
    public function updateLocale(Locale $locale, array $data): bool
    {
        return $locale->update($data);
    }

    /**
     * @param Locale $locale
     * @return bool|null
     */
    public function deleteLocale(Locale $locale): ?bool
    {
        return $locale->delete();
    }
} 