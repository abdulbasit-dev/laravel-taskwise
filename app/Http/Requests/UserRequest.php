<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserRequest extends FormRequest
{

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "result" => false,
            "status" => Response::HTTP_UNPROCESSABLE_ENTITY,
            "message" => "The given data was invalid.",
            "errors" => $validator->errors()->all()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        if (request()->isMethod("POST")) {
            $checkUniqueEmail = "unique:users,email";
            $passwordRule = ['required', 'min:8'];
        } elseif (request()->isMethod("PUT") || request()->isMethod("PATCH")) {
            $checkUniqueEmail = "unique:users,email, " . $this->user->id;
            $passwordRule = [];
        }

        return [
            "name"              => ['required'],
            "email"             => ['required', $checkUniqueEmail],
            "password"          => $passwordRule,
            'role'              => ['required'],
        ];
    }
}
