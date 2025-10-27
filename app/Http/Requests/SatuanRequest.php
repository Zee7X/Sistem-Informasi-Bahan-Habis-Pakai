<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SatuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:50|unique:satuan,nama,' . $this->id,
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama satuan wajib diisi.',
            'nama.unique' => 'Nama satuan sudah digunakan.',
        ];
    }
}
