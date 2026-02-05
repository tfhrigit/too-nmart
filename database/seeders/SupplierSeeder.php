<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'kode_supplier' => 'SUP-00001',
                'nama_supplier' => 'PT Semen Indonesia',
                'alamat' => 'Jl. Industri No. 123, Jakarta',
                'no_hp' => '021-12345678',
                'email' => 'sales@semenindonesia.com',
            ],
            [
                'kode_supplier' => 'SUP-00002',
                'nama_supplier' => 'CV Baja Sejahtera',
                'alamat' => 'Jl. Logam No. 45, Surabaya',
                'no_hp' => '031-98765432',
                'email' => 'info@bajasejahtera.com',
            ],
            [
                'kode_supplier' => 'SUP-00003',
                'nama_supplier' => 'UD Kayu Jati Makmur',
                'alamat' => 'Jl. Hutan No. 67, Semarang',
                'no_hp' => '024-11223344',
                'email' => 'kayujati@gmail.com',
            ],
            [
                'kode_supplier' => 'SUP-00004',
                'nama_supplier' => 'Toko Cat Warna Indah',
                'alamat' => 'Jl. Warna No. 89, Bandung',
                'no_hp' => '022-55667788',
                'email' => 'catwarnaindah@yahoo.com',
            ],
            [
                'kode_supplier' => 'SUP-00005',
                'nama_supplier' => 'PT Pipa Pratama',
                'alamat' => 'Jl. Saluran No. 12, Bekasi',
                'no_hp' => '021-99887766',
                'email' => 'pipapratama@outlook.com',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
