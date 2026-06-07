# Arunika Ryokan - Sistem Reservasi Hotel

Arunika Ryokan adalah sebuah aplikasi pemesanan hotel (sistem reservasi) berbasis web yang dirancang dengan antarmuka bernuansa kemewahan Jepang (Japanese Luxury & Zen). Aplikasi ini menyediakan pengalaman pemesanan kamar yang elegan dan intuitif, baik untuk tamu hotel maupun pengelola (Admin).

---

## Fitur Utama

### Untuk Tamu (User)
- Katalog Kamar Premium: Menampilkan daftar kamar dengan tampilan kartu (card) bergaya Shoji (pintu geser Jepang) beserta fasilitas lengkap.
- Promo & Diskon Eksklusif: Sistem promo dinamis dengan animasi visual yang menarik saat harga dicoret (slash animation).
- Pemilihan Tanggal Dinamis (Date Picker): Mencegah pemesanan pada tanggal yang sudah penuh (fully booked), menggunakan kalender perhotelan standar (Check-in 14:00, Check-out 12:00).
- Proses Booking & Checkout Cepat: Ringkasan biaya otomatis (Kamar + Pajak 11% + Layanan 5%).
- Integrasi Pembayaran (Midtrans): Pembayaran yang aman dan langsung terkonfirmasi menggunakan Midtrans Payment Gateway.
- Google OAuth: Login yang mudah dan cepat menggunakan akun Google.
- Notifikasi Email: Pengiriman detail pemesanan ke email tamu.

### Untuk Pengelola (Admin)
- Manajemen Kamar (CRUD): Menambah, mengubah, dan menghapus data kamar, harga, tipe, dan kapasitas.
- Pengaturan Promo: Admin dapat dengan mudah mengatur diskon (%) untuk kamar-kamar tertentu untuk mendongkrak penjualan.
- Dashboard Ringkas: UI pengelolaan yang cantik dengan dukungan Toast Notification dan Modal Confirm.

---

## Tech Stack (Teknologi yang Digunakan)

- Backend: Laravel (PHP 8.2)
- Frontend: Vue.js 3 + Inertia.js
- Styling: Vanilla CSS (CSS Variables) & Tailwind CSS
- Database: MySQL (Database: Arunika)
- Third-party Services: 
  - Midtrans (Payment Gateway)
  - Google Cloud Console (OAuth 2.0)
  - SMTP Gmail (Notifikasi Email)

---

## Cara Instalasi & Menjalankan Aplikasi Lokal

Pastikan Anda sudah menginstal PHP (>= 8.2), Composer, Node.js, dan MySQL (misalnya menggunakan Laragon/XAMPP).

### 1. Kloning Repositori
```bash
git clone <url-repo-anda>
cd Hotel
```

### 2. Instalasi Dependensi
Jalankan perintah berikut untuk menginstal package backend (PHP) dan frontend (Node.js):
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (.env)
Gandakan file .env.example menjadi .env lalu sesuaikan pengaturannya:
```bash
cp .env.example .env
php artisan key:generate
```
Pastikan untuk mengatur konfigurasi berikut di dalam .env:
- Database: DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Mail (SMTP): MAIL_USERNAME, MAIL_PASSWORD (Gunakan App Password Gmail)
- Google OAuth: GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET
- Midtrans: MIDTRANS_MERCHANT_ID, MIDTRANS_CLIENT_KEY, MIDTRANS_SERVER_KEY

### 4. Migrasi Database
Jalankan migrasi untuk membuat tabel-tabel yang diperlukan ke dalam database:
```bash
php artisan migrate
```
(Opsional: Jika ada seeder, Anda bisa menjalankan `php artisan migrate --seed`)

### 5. Jalankan Server Development
Buka dua terminal terpisah dan jalankan perintah berikut:

Terminal 1 (Menjalankan Vite untuk Frontend Vue):
```bash
npm run dev
```

Terminal 2 (Menjalankan Server Laravel):
```bash
php artisan serve
```

Aplikasi sekarang dapat diakses melalui browser pada http://localhost:8000.

---

## Catatan Kebijakan Pemesanan
- Waktu standar Check-in mulai pukul 14:00.
- Waktu standar Check-out maksimal pukul 12:00.
- Tamu lain dapat check-in pada hari yang sama dengan jadwal check-out tamu sebelumnya di kamar yang sama.

---
Dibuat untuk memberikan pengalaman reservasi yang modern, aman, dan memanjakan mata.
