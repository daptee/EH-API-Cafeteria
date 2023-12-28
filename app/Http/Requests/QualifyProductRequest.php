<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QualifyProductRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'publication_id' => 'required|integer',
            'user_id' => 'required|integer',
            'qualification' => 'required|integer|between:1,5',
            'comment' => 'required',
        ];
    }

}
