<?php

namespace Vis\Builder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditorImage extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'  => 'required|image'
        ];
    }
}
