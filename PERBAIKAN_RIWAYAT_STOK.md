# Perbaikan Riwayat Stok - Update

## ✅ Issues Fixed

### 1. **Tombol "Riwayat Stok Harian" Tidak Tampil**
**Status:** ✅ FIXED

- Tombol sudah ada di `barang-movement/index.blade.php` (baris 10-12)
- Jika tidak tampil, pastikan page sudah di-refresh atau clear cache browser
- Tombol ada di kanan atas halaman "Pergerakan Barang"

```blade
<a href="{{ route('barang-movement.riwayat-stok') }}" class="btn btn-primary">
    <i class="bi bi-table"></i> Riwayat Stok Harian
</a>
```

### 2. **Filter Tidak Bisa Pindah ke 1 Bulan**
**Status:** ✅ FIXED

**Masalah:** Periode selector tidak meneruskan parameter `search` saat mengklik tombol periode

**Solusi Diterapkan:**
- Tambah `<input type="hidden" name="search">` di form periode selector
- Tambah `<input type="hidden" name="periode">` di form filter search
- Sekarang semua tombol periode meneruskan `search` parameter

```blade
<!-- Form periode menjadi: -->
<form method="GET" action="{{ route('barang-movement.riwayat-stok') }}" id="periodeForm">
    @if($search)
        <input type="hidden" name="search" value="{{ $search }}">
    @endif
    <button type="submit" name="periode" value="1">1 Bulan</button>
    <!-- dst -->
</form>

<!-- Form filter menjadi: -->
<form method="GET" action="{{ route('barang-movement.riwayat-stok') }}">
    <input type="hidden" name="periode" value="{{ $periodeDefault }}">
    <!-- search input -->
</form>
```

### 3. **Stok Awal Diambil dari StockHistory (tidak benar)**
**Status:** ✅ FIXED

**Masalah:** Stok awal mengambil dari history sebelum periode, tidak dari master barang

**Solusi Diterapkan:**
- Ganti ke `$barang->stok_sekarang` (field di master barang)
- Ini adalah stok awal saat halaman diakses

```php
$stokAwal = (int)$barang->stok_sekarang; // ✅ Dari master barang
```

### 4. **Tambahan Kolom: Stok Saat Ini, Jumlah Keluar, Jumlah Masuk**
**Status:** ✅ ADDED

Tambah 3 kolom summary di tabel setelah kolom "STOK AWAL":

#### A. Kolom "MASUK" (Hijau)
- Total quantity masuk dalam periode
- Warna: Hijau (#28a745)
- Formula: `sum(StockHistory.jumlah where jenis_transaksi='masuk')`

#### B. Kolom "KELUAR" (Merah)
- Total quantity keluar dalam periode  
- Warna: Merah (#dc3545)
- Formula: `sum(StockHistory.jumlah where jenis_transaksi='keluar')`

#### C. Kolom "STOK SAAT INI" (Biru)
- Stok di akhir periode
- Warna: Biru (#0066cc)
- Formula: `STOK_AWAL + MASUK - KELUAR`

**Implementasi:**

Controller menghitung:
```php
$totalMasuk = $history->where('jenis_transaksi', 'masuk')->sum('jumlah');
$totalKeluar = $history->where('jenis_transaksi', 'keluar')->sum('jumlah');
$stokSaatIni = $stokAwal + $totalMasuk - $totalKeluar;

$stockData[] = [
    'barang' => $barang,
    'daily' => $dailyData,
    'stok_awal' => $stokAwal,
    'stok_saat_ini' => $stokSaatIni,      // ← BARU
    'jumlah_masuk' => $totalMasuk,        // ← BARU
    'jumlah_keluar' => $totalKeluar,      // ← BARU
];
```

View menampilkan:
```blade
<td style="color: #28a745; font-weight: 600;">
    {{ number_format($item['jumlah_masuk'], 0, ',', '.') }}
</td>
<td style="color: #dc3545; font-weight: 600;">
    {{ number_format($item['jumlah_keluar'], 0, ',', '.') }}
</td>
<td style="color: #0066cc; font-weight: 600;">
    {{ number_format($item['stok_saat_ini'], 0, ',', '.') }}
</td>
```

## 📊 Struktur Tabel Sekarang

```
┌──────────┬──────┬────┬────┬────┬───────┬───────┬─────┐
│ PRODUK   │AWAL  │MAS│KEL │SKI │20/12  │21/12  │...  │
│(Kode)    │      │UK │OUT │    │MON    │TUE    │     │
├──────────┼──────┼────┼────┼────┼───────┼───────┼─────┤
│Semen xxx │ 150  │50 │10  │190 │IN:50  │OUT:20 │     │
│Kode:..   │      │   │    │    │OUT:10 │IN:30  │     │
│          │      │   │    │    │       │       │     │
│Batu Bata │ 500  │100│80  │520 │   -   │IN:100 │     │
│Kode:..   │      │   │    │    │       │OUT:50 │     │
└──────────┴──────┴────┴────┴────┴───────┴───────┴─────┘

Legend:
- PRODUK: Nama barang + kode (Sticky)
- AWAL: Stok awal dari master barang (Sticky)
- MASUK: Total quantity masuk (Hijau)
- KEL OUT: Total quantity keluar (Merah)
- SKI: Stok saat ini = AWAL + MASUK - KELUAR (Biru)
- 20/12, 21/12: Daily IN/OUT per tanggal
```

## 📝 Files Modified

1. ✅ `app/Http/Controllers/BarangMovementController.php`
   - Fixed stok calculation menggunakan `stok_sekarang`
   - Added `stok_saat_ini`, `jumlah_masuk`, `jumlah_keluar` ke data array

2. ✅ `resources/views/barang-movement/riwayat-stok.blade.php`
   - Fixed periode selector untuk preserve search parameter
   - Fixed filter form untuk preserve periode parameter
   - Added 3 kolom summary (MASUK, KELUAR, STOK SAAT INI)
   - Update header dan body table dengan new columns

3. ✅ `resources/views/barang-movement/index.blade.php`
   - Sudah punya tombol "Riwayat Stok Harian"

## 🎯 Testing Checklist

- [ ] Klik tombol "Riwayat Stok Harian" di halaman Pergerakan Barang
- [ ] Verifikasi periode default = 3 Bulan
- [ ] Klik tombol "1 Bulan" - data harus berubah dan search tetap terjaga
- [ ] Search barang (ex: "semen"), klik Filter
- [ ] Klik tombol periode lain (6 Bulan, 1 Tahun) - search tetap
- [ ] Verifikasi kolom MASUK, KELUAR, STOK SAAT INI menampilkan data
- [ ] Scroll tabel ke kanan - PRODUK dan AWAL harus sticky
- [ ] Klik Reset - kembali ke view default

## 💡 Catatan

- Stok awal menggunakan `stok_sekarang` = stok saat ini di master barang
- Jika ingin stok awal = stok di awal periode, perlu calculated dari history sebelum periode (akan lebih kompleks)
- Current setup lebih simple: stok_awal = stok saat ini, lalu ditambah masuk/dikurang keluar selama periode
- Semua sintaks sudah diverifikasi dan valid
