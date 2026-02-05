<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_barang'   => 'required|string|min:3|max:255',
            'base_unit'     => 'required|string|max:50',
            'stok_minimum'  => 'required|numeric|min:0',
            'stok_awal'     => 'nullable|numeric|min:0',
            'deskripsi'     => 'nullable|string|max:1000',

            'harga_beli'    => 'nullable|numeric|min:0|max:9999999999.99',
            'harga_jual'    => 'nullable|numeric|min:0|max:9999999999.99',

            'units'                 => 'nullable|array',
            'units.*.name'          => 'nullable|string|min:1|max:50',
            'units.*.multiplier'    => 'nullable|numeric|min:0.01|max:999999',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama barang wajib diisi',
            'nama_barang.min'      => 'Nama barang minimal 3 karakter',
            'nama_barang.max'      => 'Nama barang maksimal 255 karakter',

            'base_unit.required'   => 'Satuan dasar wajib dipilih',

            'stok_minimum.required'=> 'Stok minimum wajib diisi',
            'stok_minimum.min'     => 'Stok minimum tidak boleh negatif',

            'stok_awal.min'        => 'Stok awal tidak boleh negatif',

            'harga_beli.min'       => 'Harga beli tidak boleh negatif',
            'harga_beli.max'       => 'Harga beli terlalu besar',

            'harga_jual.min'       => 'Harga jual tidak boleh negatif',
            'harga_jual.max'       => 'Harga jual terlalu besar',

            'units.*.name.min'     => 'Nama satuan tambahan tidak boleh kosong',
            'units.*.name.max'     => 'Nama satuan maksimal 50 karakter',

            'units.*.multiplier.min'=> 'Multiplier harus lebih besar dari 0',
            'units.*.multiplier.max'=> 'Multiplier terlalu besar',
        ];
    }
}
