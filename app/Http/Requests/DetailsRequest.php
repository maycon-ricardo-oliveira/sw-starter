<?php

namespace App\Http\Requests;

use App\Enums\SearchTypeEnum;
use Illuminate\Validation\Rule;

class DetailsRequest extends BaseRequest
{

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->route('type'),
            'id'   => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(SearchTypeEnum::values())],
            'id' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Search type is required',
            'type.in'       => 'Invalid search type',
            'id.required'   => 'Resource id is required',
        ];
    }
}
