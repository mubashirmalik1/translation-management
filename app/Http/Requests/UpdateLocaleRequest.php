<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the locale ID from the route.
        $localeId = $this->route('locale')->id;

        return [
            'code' => 'required|string|max:5|unique:locales,code,' . $localeId,
            'name' => 'nullable|string|max:255',
        ];
    }
}
