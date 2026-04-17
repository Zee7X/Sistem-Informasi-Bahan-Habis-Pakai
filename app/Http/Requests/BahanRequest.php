<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_bahan'     => 'required|string|max:100|unique:bahan,kode_bahan,' . ($this->bahan?->id ?? $this->bahan),
            'nama_bahan'     => 'required|string|max:200',
            'spesifikasi'    => 'nullable|string',
            'satuan_id'      => 'required|exists:satuan,id',
            'minimal_stok'   => 'required|integer|min:0',
            'lokasi'         => 'nullable|string|max:255',
            'keterangan'     => 'nullable|string',
        ];
    }
}
