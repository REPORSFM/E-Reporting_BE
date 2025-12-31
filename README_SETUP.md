# Query Report API - CodeIgniter 4

API untuk Query Report Builder menggunakan CodeIgniter 4.

## Perbaikan yang Telah Dilakukan

### 1. **Konfigurasi Database**

- ✅ Diperbaiki hostname database dari `localhost:8080` menjadi `localhost`
- ✅ Port disesuaikan ke `3306` (MySQL default)
- ✅ Charset diubah ke `utf8mb4` untuk support emoji dan karakter internasional

### 2. **File Environment (.env)**

- ✅ Diaktifkan konfigurasi `app.baseURL`
- ✅ Set `app.indexPage` ke string kosong untuk URL yang lebih clean
- ✅ Konfigurasi database disesuaikan dengan environment lokal

### 3. **CORS (Cross-Origin Resource Sharing)**

- ✅ Dibuat filter CORS baru (`app/Filters/Cors.php`)
- ✅ Filter CORS didaftarkan di `app/Config/Filters.php`
- ✅ CORS diaktifkan secara global untuk semua request
- ✅ Mendukung method: GET, POST, PUT, DELETE, OPTIONS

### 4. **Database Schema**

- ✅ Dibuat file `database_schema.sql` dengan:
  - Tabel `TQueryReport` untuk menyimpan definisi report
  - Tabel `TSales` untuk demo data penjualan
  - Sample data untuk testing
  - 4 sample report siap pakai

## Cara Setup

### 1. Import Database

```bash
# Login ke MySQL
mysql -u root -p

# Import schema
mysql -u root -p < database_schema.sql
```

Atau gunakan phpMyAdmin:

1. Buka phpMyAdmin
2. Klik tab "Import"
3. Pilih file `database_schema.sql`
4. Klik "Go"

### 2. Konfigurasi Database

Edit file `.env` jika perlu mengubah kredensial database:

```
database.default.hostname = localhost
database.default.database = queryreport_db
database.default.username = root
database.default.password =
database.default.port = 3306
```

### 3. Jalankan Server

```bash
# Jalankan built-in server CodeIgniter
php spark serve
```

Server akan berjalan di `http://localhost:8080`

## Testing dengan Postman

### 1. Login

```
POST http://localhost:8080/api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

### 2. Get All Reports

```
POST http://localhost:8080/api/queryreport/getall
Content-Type: application/json

{
  "idOrganisasi": "ORG001",
  "stAktif": 1
}
```

### 3. Get Report by Kode

```
POST http://localhost:8080/api/queryreport/getbykode
Content-Type: application/json

{
  "kode": "REP20251127001001"
}
```

### 4. Create New Report

```
POST http://localhost:8080/api/queryreport/create
Content-Type: application/json

{
  "namaReport": "Laporan Test",
  "querySql": "SELECT * FROM TSales WHERE organisasi = :org",
  "catatan": "Laporan untuk testing",
  "idOrganisasi": "ORG001",
  "parameter": [
    {
      "name": "org",
      "type": "string",
      "label": "Organisasi"
    }
  ]
}
```

### 5. Execute Report

```
POST http://localhost:8080/api/queryreport/execute
Content-Type: application/json

{
  "kode": "REP20251127001003",
  "paramValues": {
    "tanggal": "2025-11-01",
    "organisasi": "ORG001"
  }
}
```

### 6. Update Report

```
PUT http://localhost:8080/api/queryreport/update
Content-Type: application/json

{
  "kode": "REP20251127001001",
  "namaReport": "Laporan Semua Report (Updated)",
  "catatan": "Updated description"
}
```

### 7. Delete Report (Soft Delete)

```
DELETE http://localhost:8080/api/queryreport/delete
Content-Type: application/json

{
  "kode": "REP20251127001001"
}
```

## User Credentials untuk Testing

### Admin User

- Username: `admin`
- Password: `admin123`

### Regular User

- Username: `user`
- Password: `user123`

## Endpoints Available

### Authentication

- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/profile` - Get user profile

### Query Report

- `POST /api/queryreport/create` - Create new report
- `POST /api/queryreport/getall` - Get all reports
- `POST /api/queryreport/getbykode` - Get report by code
- `POST /api/queryreport/getbyorganisasi` - Get reports by organization
- `PUT /api/queryreport/update` - Update report
- `DELETE /api/queryreport/delete` - Delete report (soft delete)
- `POST /api/queryreport/execute` - Execute report with parameters

### Documentation

- `GET /api/docs` - Swagger UI
- `GET /api/docs/swagger.json` - Swagger JSON

## Troubleshooting

### API tidak bisa diakses dari Postman

- ✅ **SUDAH DIPERBAIKI**: CORS filter sudah ditambahkan
- Pastikan server berjalan di `http://localhost:8080`

### Database connection error

- Pastikan MySQL service berjalan
- Cek kredensial di file `.env`
- Pastikan database `queryreport_db` sudah dibuat
- Gunakan port 3306 untuk MySQL (bukan 8080)

### URL mengandung index.php

- ✅ **SUDAH DIPERBAIKI**: `app.indexPage` sudah di-set ke empty string
- Jika masih muncul, pastikan file `.env` sudah benar

### CSRF Token Error

- CSRF protection sudah di-disable untuk API
- Jika masih error, pastikan tidak ada filter CSRF yang aktif

## File Structure

```
app/
├── Config/
│   ├── App.php          (baseURL, indexPage config)
│   ├── Database.php     (database config)
│   ├── Filters.php      (CORS filter registration)
│   └── Routes.php       (API routes)
├── Controllers/
│   ├── Auth.php         (authentication)
│   └── QueryReport.php  (query report CRUD)
├── Filters/
│   └── Cors.php         (CORS filter - BARU)
└── Models/
    ├── QueryReportModel.php
    └── UserModel.php
.env                     (environment config - UPDATED)
database_schema.sql      (database schema - BARU)
```

## Notes

- Semua endpoint menggunakan format response yang konsisten dengan struktur:
  ```json
  {
    "response": { ... },
    "metadata": {
      "message": "OK",
      "code": 200
    }
  }
  ```
- Query SQL yang diperbolehkan hanya SELECT query untuk keamanan
- Soft delete digunakan (set `StAktif = 0`) daripada hard delete
- Parameter dalam format JSON array untuk fleksibilitas
