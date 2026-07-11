# Zaishin Warehouse (WMS)

Zaishin Warehouse adalah sistem manajemen gudang (Warehouse Management System) berbasis web yang dirancang untuk mengelola inventaris, memvisualisasikan kapasitas tata letak rak secara interaktif, serta melacak arus masuk dan keluar barang menggunakan pemindaian QR code dan asisten kecerdasan buatan (AI).

## Fitur Utama

- **Peta Gudang 2D Interaktif**: Visualisasi denah gudang dan tata letak rak secara real-time. Kapasitas slot rak dapat disesuaikan secara fleksibel (1 hingga 24 slot) dengan proteksi pemindahan stok otomatis.
- **Dynamic Role-Based Access Control (RBAC)**: Sistem perizinan staf gudang yang dapat dikonfigurasi secara dinamis per tab fitur melalui database, dilengkapi dengan pengaman pencegah lockout admin secara tidak sengaja.
- **Pemrosesan Inbound & Outbound**: Alur penerimaan (putaway) dan pengeluaran (picking) barang terintegrasi dengan pemindaian QR code.
- **Stock Opname & Relokasi**: Sesi audit stok fisik gudang (blind count) dan fitur pemindahan lokasi barang antar slot rak secara aman.
- **Asisten AI (Gemini Chatbot)**: Integrasi dengan Gemini API untuk membantu administrasi mencari barang, menanyakan slot kosong, menganalisis riwayat transaksi, dan membuat rangkuman laporan.
- **Fitur Notifikasi Cerdas**: Pusat pemberitahuan untuk alarm stok menipis, sesi opname baru, atau pengajuan restock barang.

## Teknologi Stack

- **Backend**: PHP (Native PDO)
- **Database**: MySQL / MariaDB
- **Frontend**: Vanilla HTML5, CSS3, dan JavaScript Modern (PWA Ready)
- **AI Engine**: Google Gemini API

## Langkah Instalasi

### 1. Kloning Repositori
Kloning repositori ini ke direktori server lokal Anda (misal `htdocs` pada XAMPP):
```bash
git clone https://github.com/Jailbirdss/ZaishinWarehouse.git
```

### 2. Konfigurasi Database
1. Aktifkan Apache dan MySQL di XAMPP Control Panel.
2. Buka `http://localhost/phpmyadmin` dan buat database baru bernama `db_wms`.
3. Impor berkas database yang berada di direktori proyek: `database/db_wms.sql`.

### 3. Konfigurasi Environment (`.env`)
Salin atau buat berkas `.env` pada direktori root proyek dan sesuaikan nilainya:
```env
DB_HOST=localhost
DB_NAME=db_wms
DB_USER=root
DB_PASS=
APP_SECRET=zaishin-wms-secure-key-189f36f9a0c
GEMINI_API_KEY=YOUR_GEMINI_API_KEY_HERE
```
*Ganti `YOUR_GEMINI_API_KEY_HERE` dengan kunci API Gemini Anda.*

### 4. Menjalankan Aplikasi
Akses aplikasi melalui browser Anda pada alamat berikut:
```
http://localhost/wms-psi
```

## Akun Demo Default

Berikut adalah daftar akun demo bawaan sistem. Seluruh akun menggunakan kata sandi yang sama dengan username masing-masing:

| No | Nama | Username | Kata Sandi | Peran (Role) | Hak Akses Utama |
| :---: | :--- | :---: | :---: | :--- | :--- |
| 1 | Etmin Datang | `admin` | `admin` | Admin Gudang | Manajemen User, Kelola Peran, Peta Gudang, Master Barang |
| 2 | Budi Santoso | `budi` | `budi` | Petugas Gudang | Penerimaan Barang, Pengeluaran Barang, Stock Opname |
| 3 | Citra Dewi | `citra` | `citra` | Divisi Penjualan | Manajemen Sales Order (SO), Lihat Stok |
| 4 | Dani Prasetyo | `dani` | `dani` | Divisi Pembelian | Ajukan Permintaan Restock (PO), Lihat Stok |
| 5 | Eko Manajer | `eko` | `eko` | Manajemen | Laporan, Persetujuan Restock, Monitoring Opname |
| 6 | Fandy Ahmad | `fandy` | `fandy` | Admin Gudang (Alt) | Manajemen User, Kelola Peran, Peta Gudang, Master Barang |
