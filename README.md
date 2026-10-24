# UTS Konstruksi dan Evolusi Perangkat Lunak (KEPL) 2026

**KopiCraft Roastery Inventory Management System**  
Aplikasi manajemen pasokan dan katalog biji kopi nusantara (*Specialty Coffee & Roastery Inventory*) berbasis arsitektur *Decoupled Full-Stack* (Laravel RESTful API + Vue 3 SPA) yang diorkestrasi secara modern menggunakan Docker Compose dan FrankenPHP.

---

## 👤 Identitas Pengembang
- **Nama Mahasiswa**: Akmal Manggala Putra
- **NIM**: 24/536182/SV/24402
- **Kelas Praktikum**: PLBB
- **Program Studi**: D-IV Teknologi Rekayasa Perangkat Lunak
- **Departemen**: Teknik Elektro dan Informatika (DTETI)
- **Institusi**: Sekolah Vokasi, Universitas Gadjah Mada
- **Dosen Pengampu**: Galih Malela Damaraji, S.Pd., M.Eng. (`@alesana001`)

---

## 🏗️ Arsitektur Sistem
Proyek ini mengimplementasikan konsep monorepo dengan pemisahan tanggung jawab yang ketat:
- **`backend/`**: Laravel RESTful API dengan otentikasi token (Sanctum), validasi request presisi (HTTP 201, 422, 401), pengujian otomatis PHPUnit, serta disajikan oleh **FrankenPHP Application Server** (+10 Bonus CPMK).
- **`frontend/`**: Vue 3 Single Page Application (Vite, Vue Router, Axios Interceptor) dengan tampilan modern berbasis metrik inventaris dan katalog kopi ber-badge.
- **`docker-compose.yml`**: Orkestrasi terpadu multi-container (`db`, `backend`, `frontend`) dengan persistensi data *named volume*.
- **`.github/workflows/ci.yml`**: Pipeline CI/CD otomatis untuk linting, pengujian unit, pembuktian proteksi branch, dan penerbitan image ke GitHub Container Registry (`ghcr.io`).

---

## 🚀 Panduan Menjalankan Sistem Lokal

### Prasyarat
- Docker Engine & Docker Compose (v28+)

### Langkah Cepat
1. Salin berkas lingkungan:
   ```bash
   cp .env.example .env
   ```
2. Jalankan seluruh container dalam satu perintah:
   ```bash
   docker compose up -d --build
   ```
3. Akses aplikasi:
   - **Frontend Vue 3**: `http://localhost:3000`
   - **Backend API (FrankenPHP)**: `http://localhost:8000/api`
   - **Kredensial Default Admin**:
     - Email: `admin@kopicraft.id`
     - Password: `password123`
