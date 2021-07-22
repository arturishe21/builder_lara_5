<?php

namespace Vis\Builder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Gloudemans\Shoppingcart\Facades\Cart;

class Login extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|max:50',
            'password' => 'required|min:6|max:20',
        ];
    }
}
