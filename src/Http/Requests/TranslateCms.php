<?php

namespace Vis\Builder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslateCms extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phrase' => 'required|unique:translations_phrases_cms',
        ];
    }
}
