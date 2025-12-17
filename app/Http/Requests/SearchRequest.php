<?php

namespace App\Http\Requests;

use App\Enums\SearchTypeEnum;
use Illuminate\Validation\Rule;

class SearchRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->route('type'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in(SearchTypeEnum::values())],
            'term' => ['required', 'string', 'min:2', 'max:50'],
        ];
    }
    public function messages(): array
    {
        return [
            'type.required' => 'Search type is required',
            'type.in'       => 'Invalid search type',
            'term.required' => 'Search term is required',
            'term.min'      => 'Search term must have at least 2 characters',
        ];
    }
}
