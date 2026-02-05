<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\ItemUnit;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Semen
        $semen = Barang::create([
            'kode_barang' => 'BRG-00001',
            'nama_barang' => 'Semen Tiga Roda',
            'base_unit' => 'kg',
            'stok_sekarang' => 0,
            'stok_minimum' => 500,
            'deskripsi' => 'Semen Portland Type I',
        ]);

        ItemUnit::create([
            'barang_id' => $semen->id,
            'unit_name' => 'kg',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $semen->id,
            'unit_name' => 'sak',
            'multiplier' => 50, // 1 sak = 50 kg
            'is_base' => false,
        ]);

        // 2. Besi Beton
        $besi = Barang::create([
            'kode_barang' => 'BRG-00002',
            'nama_barang' => 'Besi Beton Diameter 12mm',
            'base_unit' => 'kg',
            'stok_sekarang' => 0,
            'stok_minimum' => 200,
            'deskripsi' => 'Besi beton polos diameter 12mm',
        ]);

        ItemUnit::create([
            'barang_id' => $besi->id,
            'unit_name' => 'kg',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $besi->id,
            'unit_name' => 'batang',
            'multiplier' => 11, // 1 batang (12m) ≈ 11 kg
            'is_base' => false,
        ]);

        // 3. Cat Tembok
        $cat = Barang::create([
            'kode_barang' => 'BRG-00003',
            'nama_barang' => 'Cat Tembok Avitex',
            'base_unit' => 'liter',
            'stok_sekarang' => 0,
            'stok_minimum' => 50,
            'deskripsi' => 'Cat tembok interior warna putih',
        ]);

        ItemUnit::create([
            'barang_id' => $cat->id,
            'unit_name' => 'liter',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $cat->id,
            'unit_name' => 'kaleng',
            'multiplier' => 5, // 1 kaleng = 5 liter
            'is_base' => false,
        ]);

        ItemUnit::create([
            'barang_id' => $cat->id,
            'unit_name' => 'galon',
            'multiplier' => 20, // 1 galon = 20 liter
            'is_base' => false,
        ]);

        // 4. Pipa PVC
        $pipa = Barang::create([
            'kode_barang' => 'BRG-00004',
            'nama_barang' => 'Pipa PVC 3 inch',
            'base_unit' => 'meter',
            'stok_sekarang' => 0,
            'stok_minimum' => 100,
            'deskripsi' => 'Pipa PVC diameter 3 inch untuk air bersih',
        ]);

        ItemUnit::create([
            'barang_id' => $pipa->id,
            'unit_name' => 'meter',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $pipa->id,
            'unit_name' => 'batang',
            'multiplier' => 4, // 1 batang = 4 meter
            'is_base' => false,
        ]);

        // 5. Pasir
        $pasir = Barang::create([
            'kode_barang' => 'BRG-00005',
            'nama_barang' => 'Pasir Cor',
            'base_unit' => 'kg',
            'stok_sekarang' => 0,
            'stok_minimum' => 1000,
            'deskripsi' => 'Pasir cor halus untuk adukan',
        ]);

        ItemUnit::create([
            'barang_id' => $pasir->id,
            'unit_name' => 'kg',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $pasir->id,
            'unit_name' => 'm3',
            'multiplier' => 1400, // 1 m3 ≈ 1400 kg
            'is_base' => false,
        ]);

        ItemUnit::create([
            'barang_id' => $pasir->id,
            'unit_name' => 'truk',
            'multiplier' => 7000, // 1 truk ≈ 5 m3 = 7000 kg
            'is_base' => false,
        ]);

        // 6. Keramik
        $keramik = Barang::create([
            'kode_barang' => 'BRG-00006',
            'nama_barang' => 'Keramik 40x40 cm',
            'base_unit' => 'pcs',
            'stok_sekarang' => 0,
            'stok_minimum' => 100,
            'deskripsi' => 'Keramik lantai ukuran 40x40 cm motif marmer',
        ]);

        ItemUnit::create([
            'barang_id' => $keramik->id,
            'unit_name' => 'pcs',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $keramik->id,
            'unit_name' => 'dus',
            'multiplier' => 6, // 1 dus = 6 pcs
            'is_base' => false,
        ]);

        ItemUnit::create([
            'barang_id' => $keramik->id,
            'unit_name' => 'box',
            'multiplier' => 12, // 1 box = 12 pcs
            'is_base' => false,
        ]);

        // 7. Genteng
        $genteng = Barang::create([
            'kode_barang' => 'BRG-00007',
            'nama_barang' => 'Genteng Beton',
            'base_unit' => 'pcs',
            'stok_sekarang' => 0,
            'stok_minimum' => 200,
            'deskripsi' => 'Genteng beton flat warna coklat',
        ]);

        ItemUnit::create([
            'barang_id' => $genteng->id,
            'unit_name' => 'pcs',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $genteng->id,
            'unit_name' => 'pallet',
            'multiplier' => 240, // 1 pallet = 240 pcs
            'is_base' => false,
        ]);

        // 8. Triplek
        $triplek = Barang::create([
            'kode_barang' => 'BRG-00008',
            'nama_barang' => 'Triplek 4mm',
            'base_unit' => 'lembar',
            'stok_sekarang' => 0,
            'stok_minimum' => 50,
            'deskripsi' => 'Triplek kayu ketebalan 4mm ukuran 122x244 cm',
        ]);

        ItemUnit::create([
            'barang_id' => $triplek->id,
            'unit_name' => 'lembar',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        // 9. Kawat
        $kawat = Barang::create([
            'kode_barang' => 'BRG-00009',
            'nama_barang' => 'Kawat Bendrat',
            'base_unit' => 'kg',
            'stok_sekarang' => 0,
            'stok_minimum' => 30,
            'deskripsi' => 'Kawat bendrat untuk pengikat besi',
        ]);

        ItemUnit::create([
            'barang_id' => $kawat->id,
            'unit_name' => 'kg',
            'multiplier' => 1,
            'is_base' => true,
        ]);

        ItemUnit::create([
            'barang_id' => $kawat->id,
            'unit_name' => 'roll',
            'multiplier' => 25, // 1 roll = 25 kg
            'is_base' => false,
        ]);

        // 10. Paku
        $paku = Barang::create([
            'kode_barang' => 'BRG-00010',
            'nama_barang' => 'Paku 5 cm',
            'base_unit' => 'kg',
            'stok_sekarang' => 0,
            'stok_minimum' => 20,
            'deskripsi' => 'Paku biasa ukuran 5 cm',
        ]);

        ItemUnit::create([
            'barang_id' => $paku->id,
            'unit_name' => 'kg',
            'multiplier' => 1,
            'is_base' => true,
        ]);
    }
}
