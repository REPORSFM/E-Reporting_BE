# 🚀 Quick Start Guide - Query Report API

## Langkah-Langkah Setup Cepat

### ✅ Step 1: Import Database

1. Buka **phpMyAdmin** atau **MySQL Command Line**
2. Import file `database_schema.sql`

**Melalui Command Line:**

```bash
mysql -u root -p < database_schema.sql
```

**Melalui phpMyAdmin:**

- Login ke phpMyAdmin
- Klik tab "Import"
- Pilih file `database_schema.sql`
- Klik "Go"

### ✅ Step 2: Cek Konfigurasi

File `.env` sudah dikonfigurasi dengan setting berikut:

```
database.default.hostname = localhost
database.default.database = queryreport_db
database.default.username = root
database.default.password =
database.default.port = 3306
```

**Jika perlu ubah**, edit file `.env` sesuai dengan setup MySQL Anda.

### ✅ Step 3: Jalankan Server

Buka terminal/command prompt di folder project, lalu jalankan:

```bash
php spark serve
```

Server akan berjalan di: **http://localhost:8080**

### ✅ Step 4: Test dengan Postman

#### Import Collection ke Postman:

1. Buka **Postman**
2. Klik **Import**
3. Pilih file `postman_collection.json`
4. Collection "Query Report API" akan muncul

#### Test Login:

```
POST http://localhost:8080/api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

**Expected Response:**

```json
{
  "response": {
    "ID": "PTG001",
    "UID": "16156151515",
    "Nama": "Muhammad Nur Amin",
    "Organisasi": 787878,
    "HakAkses": {
      "reporting": [{ "AdminReporting": true }, { "Reporting": true }]
    }
  },
  "metadata": {
    "message": "Ok",
    "code": 200
  }
}
```

#### Test Get All Reports:

```
POST http://localhost:8080/api/queryreport/getall
Content-Type: application/json

{}
```

**Expected Response:**

```json
{
  "response": [
    {
      "kode": "REP20251127001001",
      "namaReport": "Laporan Semua Report",
      "querySql": "SELECT ...",
      "parameter": null,
      "catatan": "...",
      "idOrganisasi": "ORG001",
      "stAktif": 1,
      "createdAt": "2025-11-27 ...",
      "createdBy": "SYSTEM",
      "updatedAt": null,
      "updatedBy": null
    },
    ...
  ],
  "metadata": {
    "message": "OK",
    "code": 200
  }
}
```

#### Test Execute Report:

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

**Expected Response:**

```json
{
  "response": {
    "reportId": "REP20251127001003",
    "nama": "Laporan Penjualan per Tanggal",
    "data": [
      {
        "tanggal": "2025-11-01",
        "produk": "Laptop Dell",
        "jumlah": 2,
        "harga": "15000000.00",
        "total": "30000000.00",
        "customer": "PT ABC"
      }
    ]
  },
  "metadata": {
    "message": "OK",
    "code": 200
  }
}
```

## 🎯 Checklist Testing

- [ ] Database `queryreport_db` sudah dibuat dan ada 4 sample report
- [ ] Server PHP berjalan di `http://localhost:8080`
- [ ] Bisa login dengan username `admin` / password `admin123`
- [ ] Bisa get all reports
- [ ] Bisa get report by kode
- [ ] Bisa create report baru
- [ ] Bisa execute report dengan parameter
- [ ] Bisa update report
- [ ] Bisa delete report (soft delete)

## 🐛 Troubleshooting Cepat

### 1. "Database connection failed"

**Solusi:**

- Pastikan MySQL service berjalan
- Cek kredensial di file `.env`
- Pastikan port 3306 (bukan 8080)

### 2. "404 Not Found" di Postman

**Solusi:**

- Pastikan server berjalan: `php spark serve`
- Cek URL: harus `http://localhost:8080/api/...`
- Pastikan tidak ada `index.php` di URL

### 3. "CORS Error"

**Solusi:**

- ✅ Sudah diperbaiki dengan CORS filter
- Restart server jika masih error

### 4. "Table not found"

**Solusi:**

- Import ulang `database_schema.sql`
- Cek apakah database `queryreport_db` ada
- Cek apakah tabel `TQueryReport` dan `TSales` ada

### 5. "CSRF Token Mismatch"

**Solusi:**

- ✅ CSRF sudah di-disable untuk API
- Tidak perlu CSRF token di Postman

### 6. "Declaration must be compatible with ResourceController"

**Solusi:**

- ✅ Sudah diperbaiki - method `delete()` sudah memiliki parameter `$id = null`
- Restart server jika masih muncul error

## 📚 Next Steps

1. ✅ **Testing Lengkap**: Test semua endpoint dengan Postman collection
2. 📖 **Dokumentasi**: Buka `http://localhost:8080/api/docs` untuk Swagger UI
3. 🔧 **Customize**: Sesuaikan report dan parameter sesuai kebutuhan
4. 🗄️ **Database**: Tambah tabel dan data sesuai kebutuhan bisnis
5. 🔐 **Security**: Implementasi JWT atau OAuth untuk production

## 📞 Support

Jika ada masalah:

1. Cek file `writable/logs/log-*.log` untuk error detail
2. Pastikan PHP version >= 7.4
3. Pastikan extension `mysqli` enabled di PHP
4. Lihat `README_SETUP.md` untuk dokumentasi lengkap

---

**Happy Coding! 🎉**
