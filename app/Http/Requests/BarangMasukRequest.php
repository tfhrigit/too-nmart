<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|numeric|min:0.01',
            'unit_name' => 'required|string',
            'harga_beli' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi',
            'supplier_id.required' => 'Supplier wajib dipilih',
            'barang_id.required' => 'Barang wajib dipilih',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'unit_name.required' => 'Satuan wajib dipilih',
            'harga_beli.required' => 'Harga beli wajib diisi',
        ];
    }
}
