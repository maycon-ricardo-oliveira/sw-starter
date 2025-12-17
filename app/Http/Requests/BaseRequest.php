<?php

namespace App\Http\Requests;

use App\Utils\HttpCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Determines custom messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => "Field required",
        ];
    }

    /**
     * Exception if request is faled
     * @param  Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['formErrors' => $validator->messages()], HttpCode::UNPROCESSABLE_ENTITY)
        );
    }
}
