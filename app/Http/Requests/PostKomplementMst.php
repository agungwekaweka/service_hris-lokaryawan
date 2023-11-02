<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostKomplementMst extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_komplement'   => 'required|min:4|max:6',
            'tahun' => 'required|min:4',
            'id_karyawan'   => 'image|mimes:jpg,jpeg,png|max:2048',
            'tipe_komplement'   => 'image|mimes:jpg,jpeg,png|max:2048',
            'sisa_komplement'   => 'image|mimes:jpg,jpeg,png|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'id_komplement.required'   => 'ID Komplemen is required',
            'id_komplement.min'        => 'ID Komplemen must be at least 4 characters',
            'id_komplement.max'        => 'ID Komplement may not be greater than 6 characters',
            'tahun.required' => 'Tahun is required',
            'tahun.min'      => 'Tahun must be at least 4 characters',           
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data'    => $validator->errors()
        ], 422));
    }
}