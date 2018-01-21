<?php

namespace App\Http\Requests;

use App\Search;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_limited' => 'nullable|boolean',
            'status' => ['nullable', Rule::in([
                Search::STATUS_FINISHED,
                Search::STATUS_PAUSED,
            ])]
        ];
    }
}
