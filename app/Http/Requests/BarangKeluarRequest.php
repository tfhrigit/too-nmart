<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangKeluarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|numeric|min:0.01',
            'unit_name' => 'required|string',
            'harga_jual' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi',
            'barang_id.required' => 'Barang wajib dipilih',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'unit_name.required' => 'Satuan wajib dipilih',
            'harga_jual.required' => 'Harga jual wajib diisi',
        ];
    }
}
