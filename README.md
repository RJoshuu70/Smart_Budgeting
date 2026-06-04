# Smart Budgeting

**Aplikasi Pengelolaan Keuangan Mahasiswa Berbasis Web**

Dikembangkan sebagai bagian dari penelitian pada Mata Kuliah Metode Penelitian,
Program Studi S1 Sistem Informasi, Fakultas Ilmu Komputer,
Universitas Pembangunan Nasional "Veteran" Jakarta.

---

## Tentang Aplikasi

Smart Budgeting adalah aplikasi web responsif yang dirancang khusus untuk membantu mahasiswa
dalam mengelola keuangan pribadi secara efektif. Aplikasi ini dikembangkan menggunakan metode
**Extreme Programming (XP)** dan diuji menggunakan **Black Box Testing** serta **System Usability Scale (SUS)**.

### Fitur Utama
- **Pencatatan Transaksi** — Catat pemasukan dan pengeluaran harian dengan kategorisasi
- **Anggaran Mingguan** — Atur budget per minggu (Senin–Minggu) sesuai pola keuangan mahasiswa
- **Dashboard Real-time** — Pantau kondisi keuangan dalam satu tampilan
- **Laporan & Visualisasi** — Grafik pengeluaran harian dan per kategori
- **Kategori Relevan** — Kategori disesuaikan kebutuhan mahasiswa (Makan, Transport, Ngopi, Blind Box, dll.)

---

## Tim Pengembang

| Nama | NIM |
|------|-----|
| Rapolo Joshua Napitupulu | 2410512001 |
| Alvita Dita Azzahra | 2410512004 |
| Rafi Fauzi Alfariz | 2410512015 |
| Varisha Aira Dalimunthe | 2410512027 |

**Dosen Pengampu:** Dr. Hengki Tamando Sihotang, S.Kom., M.Kom

---

## Teknologi yang Digunakan

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.3 + Laravel 11 |
| Frontend | Blade Template + Tailwind CSS + Vite |
| Database | MySQL 8 |
| Ikon | Bootstrap Icons |
| Grafik | Chart.js |
| Deployment | Railway |
| Version Control | Git + GitHub |

---

## Akses Aplikasi

Aplikasi dapat diakses secara online melalui:

```
[URL akan diupdate setelah deploy ke Railway]
```

---

## Cara Menjalankan Secara Lokal

### Prasyarat

Pastikan perangkat sudah terinstall:
- [Laragon](https://laragon.org/) (bundling PHP 8.3+, MySQL, Apache)
- [Node.js](https://nodejs.org/) (v18+) & NPM
- [Git](https://git-scm.com/)
- [Composer](https://getcomposer.org/) *(sudah termasuk dalam Laragon)*

### Langkah Instalasi

**1. Clone Repository**
```bash
git clone https://github.com/RJoshuu70/Smart_Budgeting.git
cd Smart_Budgeting
```

**2. Install Dependency PHP**
```bash
composer install
```

**3. Install Dependency Node.js**
```bash
npm install
```

**4. Konfigurasi Environment**
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`, sesuaikan konfigurasi database:
```env
APP_NAME="Smart Budgeting"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_budgeting
DB_USERNAME=root
DB_PASSWORD=
```

**5. Buat Database**

Buka HeidiSQL (sudah termasuk dalam Laragon), lalu jalankan:
```sql
CREATE DATABASE smart_budgeting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**6. Jalankan Migration dan Seeder**
```bash
php artisan migrate --seed
```

### Menjalankan Aplikasi

Buka **dua terminal** secara bersamaan:

**Terminal 1 — Compile Asset (CSS/JS)**
```bash
npm run dev
```

**Terminal 2 — Jalankan Server**
```bash
php artisan serve
```

Akses aplikasi di browser:
```
http://localhost:8000
```

---

## Deploy ke Railway

### Prasyarat
- Akun [Railway](https://railway.app) (login dengan GitHub)
- Repository sudah ter-push ke GitHub

### Langkah Deploy

**1. Buat Project Baru di Railway**
- Login ke railway.app
- Klik **New Project** → **Deploy from GitHub repo**
- Pilih repo `Smart_Budgeting`

**2. Tambah Database MySQL**
- Di dashboard project, klik **+ New** → **Database** → **MySQL**
- Catat credentials yang diberikan Railway

**3. Set Environment Variables**

Di service Laravel → tab **Variables**, tambahkan:
```
APP_NAME=Smart Budgeting
APP_ENV=production
APP_KEY=base64:xxxx (generate dengan php artisan key:generate --show)
APP_DEBUG=false
APP_URL=https://subdomain-kalian.up.railway.app

DB_CONNECTION=mysql
DB_HOST=(dari Railway MySQL)
DB_PORT=(dari Railway MySQL)
DB_DATABASE=(dari Railway MySQL)
DB_USERNAME=(dari Railway MySQL)
DB_PASSWORD=(dari Railway MySQL)

SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

**4. Generate Domain**
- Klik service Laravel → **Settings** → **Networking** → **Generate Domain**
- Salin URL yang diberikan untuk disebarkan ke responden

---

## Tampilan Aplikasi

Aplikasi dirancang **mobile-first** dan dapat diakses melalui browser di perangkat apapun tanpa instalasi tambahan.

| Halaman | Deskripsi |
|---------|-----------|
| Login / Register | Autentikasi pengguna |
| Dashboard | Ringkasan keuangan minggu ini |
| Transaksi | Catat & kelola pemasukan/pengeluaran |
| Anggaran | Set & monitor budget mingguan |
| Laporan | Grafik pengeluaran harian & per kategori |

---

## Pengujian

Aplikasi diuji menggunakan dua metode:

**1. Black Box Testing**
Memvalidasi fungsionalitas sistem melalui 10 test case (TC-01 s.d. TC-10) yang mencakup autentikasi, manajemen transaksi, pengelolaan anggaran, dan visualisasi data. Hasil: 10/10 test case Pass (100%).

**2. System Usability Scale (SUS)**
Mengukur tingkat usabilitas aplikasi dengan melibatkan 30–50 mahasiswa aktif sebagai responden melalui kuesioner 10 pernyataan skala Likert 1–5.

---

## Struktur Proyek

```
Smart_Budgeting/
├── app/
│   ├── Http/Controllers/    # AuthController, DashboardController, dll.
│   ├── Models/              # User, Transaction, Budget, Category
│   └── Services/            # BudgetService (logika kalkulasi budget)
├── database/
│   ├── migrations/          # Skema tabel database
│   └── seeders/             # Data kategori default
├── resources/
│   └── views/
│       ├── auth/            # Login, Register
│       ├── layouts/         # Template utama
│       ├── transactions/    # Halaman transaksi
│       ├── budgets/         # Halaman anggaran
│       ├── reports/         # Halaman laporan & grafik
│       └── dashboard.blade.php
├── routes/
│   └── web.php              # Definisi routing aplikasi
├── nixpacks.toml            # Konfigurasi build Railway
└── Procfile                 # Konfigurasi web server Railway
```

---

## Lisensi

Proyek ini dikembangkan untuk keperluan akademik pada Mata Kuliah Metode Penelitian,
Universitas Pembangunan Nasional "Veteran" Jakarta — 2026.