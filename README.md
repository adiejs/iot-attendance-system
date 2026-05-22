# 🏥 Smart IoT Attendance System (Laravel + ESP32)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-EAB308?style=for-the-badge&logo=filament&logoColor=white)
![Vue.js](https://img.shields.io/badge/Reverb_WebSockets-4B32C3?style=for-the-badge&logo=laravel&logoColor=white)
![ESP32](https://img.shields.io/badge/ESP32-E7352C?style=for-the-badge&logo=espressif&logoColor=white)

Sistem Manajemen Absensi dan Kepegawaian modern berbasis Internet of Things (IoT). Dibangun menggunakan ekosistem Laravel terpadu untuk menerima data dari perangkat keras ESP32, mengirimkan notifikasi kehadiran secara *real-time*, dan dikelola melalui antarmuka admin yang elegan.

Sistem ini sangat ideal diimplementasikan pada instansi kesehatan, rumah sakit, perkantoran, atau kampus yang membutuhkan rekapitulasi kehadiran fisik yang cepat, akurat, dan terpusat.

---

## 🚀 Fitur Utama

* **Single Point of Entry (Auto-Discovery):** Alat keras hanya menembak ke satu *endpoint* API (`/api/attend`). 
  * Jika kartu **baru** di-*tap*, sistem otomatis mendaftarkan UID tanpa nama (menunggu admin untuk melengkapi data).
  * Jika kartu **sudah terdaftar** dan lengkap, sistem langsung memproses kehadiran.
* **Real-time Monitoring:** Menggunakan **Laravel Reverb (WebSockets)**. Data absen dan foto pegawai dari ESP32 langsung ter-*update* di layar Admin tanpa perlu *refresh* halaman.
* **Tangkapan Foto Bukti:** Menerima pengiriman data gambar (*Base64*) dari modul kamera ESP32 dan menyimpannya secara efisien di *local storage*.
* **Tunneling Ready:** Dioptimalkan untuk berjalan di atas **Cloudflare Tunnel**, memungkinkan *testing* perangkat IoT dari jaringan luar tanpa IP Publik statis.
* **Modern Admin Panel:** Dibangun di atas **Filament v3** untuk manajemen data pegawai, penugasan kartu, dan pelaporan riwayat absensi yang responsif.

## 🛠️ Stack Teknologi

* **Backend:** Laravel 13
* **Admin Panel:** FilamentPHP v3
* **WebSockets:** Laravel Reverb
* **Frontend Build:** Vite (Node.js)
* **Hardware Client:** ESP32 (Modul WiFi & Kamera) + Modul RFID RC522

## ⚙️ Persyaratan Sistem

Pastikan sistem atau server Anda memiliki perangkat lunak berikut:
* PHP >= 8.2
* Composer
* Node.js & NPM
* Database (MySQL / MariaDB)

---

## 📦 Panduan Instalasi (Development)

**1. Clone Repository & Masuk ke Folder**
```bash
git clone [https://github.com/adiejs/iot-attendance-system.git](https://github.com/adiejs/iot-attendance-system.git)
cd iot-attendance
```

**2. Install Dependensi Backend & Frontend**
```bash
composer install
npm install
```

**3. Konfigurasi Environment (.env)**
Salin file konfigurasi bawaan dan hasilkan *Application Key*:
```bash
cp .env.example .env
php artisan key:generate
```
*(Jangan lupa atur koneksi database MySQL Anda di dalam file `.env`)*

**4. Konfigurasi WebSockets & Cloudflare Tunnel (Opsional)**
Tambahkan dan sesuaikan parameter berikut pada file `.env` Anda agar komunikasi antara ESP32, Browser, dan Server dapat berjalan:
```env
APP_URL=[https://domain-cloudflare-anda.trycloudflare.com](https://domain-cloudflare-anda.trycloudflare.com)

REVERB_APP_ID=514536
REVERB_APP_KEY=my-secret-key
REVERB_APP_SECRET=my-secret-password

# Konfigurasi Publik (Untuk Browser & Payload ESP32)
REVERB_HOST="domain-cloudflare-anda.trycloudflare.com"
REVERB_PORT=443
REVERB_SCHEME=https

# Konfigurasi Server Lokal (Untuk mesin Laravel)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

**5. Import Database & Build Aset**
Jika Anda memiliki file *backup* `.sql`, silakan *import* menggunakan *database manager* (seperti HeidiSQL/phpMyAdmin). Jika tidak, jalankan migrasi tabel kosong:
```bash
php artisan migrate
npm run build
```

**6. Tautkan Storage Gambar (Sangat Penting)**
Agar foto dari perangkat kamera ESP32 dapat diakses oleh Filament panel, jalankan perintah ini (Bagi pengguna Windows, buka Terminal/CMD sebagai **Administrator**):
```bash
php artisan storage:link
php artisan optimize:clear
```

---

## 🏃‍♂️ Cara Menjalankan Aplikasi

Untuk menjalankan sistem secara utuh beserta fitur *real-time*, buka 3 tab terminal yang berbeda dan jalankan perintah berikut secara bersamaan:

**Terminal 1 (Web Server):**
```bash
php artisan serve
```

**Terminal 2 (Reverb WebSockets Server):**
```bash
php artisan reverb:start
```

**Terminal 3 (Cloudflare Tunnel - Opsional untuk ESP32): (Opsional)**
```bash
cloudflared tunnel --url [http://127.0.0.1:8000](http://127.0.0.1:8000)
```

---

## 📡 Dokumentasi API untuk Hardware (ESP32)

Perangkat keras hanya perlu melakukan request `HTTP POST` ke satu *endpoint* utama.

* **Endpoint:** `POST /api/attend`
* **Headers:** `Content-Type: application/json`
* **Payload Format:**
```json
{
    "uid": "A1B2C3D4",
    "foto": "string_base64_gambar_opsional_tanpa_header_data_uri"
}
```
* **Skenario Respons:**
  * `201 Created` - Kartu baru berhasil didaftarkan (Nama masih kosong).
  * `200 OK` (PENDING) - Kartu sudah ada, namun admin belum melengkapi nama pegawai. Absen ditunda.
  * `200 OK` (SUCCESS) - Absen berhasil diproses dan foto disimpan. WebSockets memicu pembaruan tabel otomatis.

---
*Dibuat untuk kebutuhan operasional sistem cerdas yang cepat, efisien, dan modern.*
```