<?php

namespace Vis\Builder\Http\Requests;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Sentinel::getUser()->hasAccess(['admin.access']);
    }

    public function rules(): array
    {
        return [
            'file'  => ['required', 'max:2040'],
            'path_model' => ['required', 'string'],
            'ident' => ['required', 'string'],
        ];
    }
}
