<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
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
        // Get the current translation ID from the route parameters.
        $translationId = $this->route('translation')->id ?? null;

        return [
            'locale_id' => 'sometimes|required',
            'translation_key' => 'sometimes|required|string|unique:translations,translation_key,' . $translationId,
            'translation_content' => 'sometimes|required|string',
            'tags' => 'sometimes|array'
        ];
    }
}
