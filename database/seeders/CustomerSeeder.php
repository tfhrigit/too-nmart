<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'kode_customer' => 'CST-00001',
                'nama_customer' => 'Budi Santoso',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Mawar No. 10, Jakarta',
            ],
            [
                'kode_customer' => 'CST-00002',
                'nama_customer' => 'PT Konstruksi Jaya',
                'no_hp' => '081298765432',
                'alamat' => 'Jl. Pembangunan No. 25, Tangerang',
            ],
            [
                'kode_customer' => 'CST-00003',
                'nama_customer' => 'Ani Kusuma',
                'no_hp' => '081311223344',
                'alamat' => 'Jl. Melati No. 15, Depok',
            ],
            [
                'kode_customer' => 'CST-00004',
                'nama_customer' => 'CV Bangun Rumah',
                'no_hp' => '081455667788',
                'alamat' => 'Jl. Properti No. 30, Bogor',
            ],
            [
                'kode_customer' => 'CST-00005',
                'nama_customer' => 'Rudi Hermawan',
                'no_hp' => '081599887766',
                'alamat' => 'Jl. Anggrek No. 5, Bekasi',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
