# 📚 Dokumentasi Swagger API

## File Dokumentasi yang Tersedia

### 1. **swagger-api-docs.json**

File JSON berisi dokumentasi lengkap API dalam format OpenAPI 3.0.

**Lokasi:** `c:\PROJEK\API\queryreport-api\swagger-api-docs.json`

**Isi:**

- ✅ Semua endpoint Authentication (login, logout, profile)
- ✅ Semua endpoint Query Report (CRUD + execute)
- ✅ Request body schema dengan contoh
- ✅ Response schema untuk setiap status code
- ✅ Error responses
- ✅ Component schemas (Metadata, ErrorResponse, UserProfile, QueryReport)

### 2. **api-documentation.html**

File HTML untuk melihat dokumentasi menggunakan Swagger UI.

**Lokasi:** `c:\PROJEK\API\queryreport-api\api-documentation.html`

## 🚀 Cara Menggunakan

### Opsi 1: Buka File HTML Langsung

1. **Buka file HTML di browser:**

   ```
   c:\PROJEK\API\queryreport-api\api-documentation.html
   ```

2. Swagger UI akan otomatis load dan menampilkan dokumentasi

3. Anda bisa:
   - ✅ Melihat semua endpoint
   - ✅ Melihat request/response schema
   - ✅ Try out API langsung dari browser
   - ✅ Download OpenAPI JSON

### Opsi 2: Via Server (Lebih Bagus)

Jika server Apache/PHP sedang berjalan:

1. **Copy file ke folder public:**

   ```powershell
   Copy-Item api-documentation.html public/
   Copy-Item swagger-api-docs.json public/
   ```

2. **Akses via browser:**

   ```
   http://localhost:8080/api-documentation.html
   ```

   Atau dari network:

   ```
   http://192.168.30.70:8080/api-documentation.html
   ```

### Opsi 3: Import ke Postman

1. **Buka Postman**
2. **Import → Upload Files**
3. **Pilih:** `swagger-api-docs.json`
4. Postman akan otomatis generate collection dari OpenAPI spec

### Opsi 4: Import ke Swagger Editor Online

1. **Buka:** https://editor.swagger.io/
2. **File → Import File**
3. **Pilih:** `swagger-api-docs.json`
4. Edit dokumentasi jika perlu
5. Export dalam berbagai format (HTML, PDF, Client SDK, dll)

## 📋 Struktur Dokumentasi

### Tags/Sections:

#### 1. **Authentication**

- POST `/api/login` - Login pengguna
- POST `/api/logout` - Logout pengguna
- GET `/api/profile` - Get profile user yang login

#### 2. **Query Report**

- POST `/api/queryreport/create` - Buat report baru
- POST `/api/queryreport/getall` - Get semua report (dengan filter)
- POST `/api/queryreport/getbykode` - Get report by kode
- POST `/api/queryreport/getbyorganisasi` - Get report by organisasi
- PUT `/api/queryreport/update` - Update report
- DELETE `/api/queryreport/delete` - Delete report (soft delete)
- POST `/api/queryreport/execute` - Execute report dengan parameter

### Schemas/Models:

1. **Metadata** - Standard metadata response
2. **ErrorResponse** - Format error response
3. **UserProfile** - Data profil user
4. **QueryReport** - Data query report lengkap

## 🎯 Contoh Request & Response

Semua contoh sudah include di dokumentasi:

### Login Example:

```json
POST /api/login
{
  "username": "admin",
  "password": "admin123"
}

Response 200:
{
  "response": {
    "ID": "PTG001",
    "UID": "16156151515",
    "Nama": "Muhammad Nur Amin",
    "Organisasi": 787878
  },
  "metadata": {
    "message": "Ok",
    "code": 200
  }
}
```

### Create Report Example:

```json
POST /api/queryreport/create
{
  "namaReport": "Laporan Penjualan",
  "querySql": "SELECT * FROM sales WHERE date = :tanggal",
  "catatan": "Catatan laporan",
  "idOrganisasi": "ORG001",
  "parameter": [
    {
      "name": "tanggal",
      "type": "date",
      "label": "Tanggal"
    }
  ]
}

Response 200:
{
  "response": {
    "kode": "REP20251127001001",
    "message": "Report berhasil dibuat"
  },
  "metadata": {
    "message": "OK",
    "code": 200
  }
}
```

### Execute Report Example:

```json
POST /api/queryreport/execute
{
  "kode": "REP20251127001003",
  "paramValues": {
    "tanggal": "2025-11-01",
    "organisasi": "ORG001"
  }
}

Response 200:
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
        "total": "30000000.00"
      }
    ]
  },
  "metadata": {
    "message": "OK",
    "code": 200
  }
}
```

## 🛠️ Customize Dokumentasi

### Edit Informasi API:

Buka `swagger-api-docs.json`, edit bagian `info`:

```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Query Report Builder API",
    "description": "Deskripsi API Anda",
    "version": "1.0.0",
    "contact": {
      "name": "Tim Support",
      "email": "support@company.com"
    }
  }
}
```

### Tambah Server URL:

Edit bagian `servers`:

```json
"servers": [
  {
    "url": "http://localhost:8080",
    "description": "Development Server"
  },
  {
    "url": "http://192.168.30.70:8080",
    "description": "Network Server"
  },
  {
    "url": "https://api.production.com",
    "description": "Production Server"
  }
]
```

### Tambah Endpoint Baru:

Tambahkan di bagian `paths`:

```json
"/api/new-endpoint": {
  "post": {
    "tags": ["Tag Name"],
    "summary": "Endpoint summary",
    "description": "Detail description",
    "requestBody": { ... },
    "responses": { ... }
  }
}
```

## 📦 Export Documentation

### Generate ke berbagai format:

1. **HTML Static:**

   - Via Swagger Codegen: `swagger-codegen generate -i swagger-api-docs.json -l html2`

2. **PDF:**

   - Via widdershins + pandoc
   - Via Swagger2PDF online tools

3. **Markdown:**

   - Via widdershins: `widdershins swagger-api-docs.json -o API.md`

4. **Client SDK:**
   - JavaScript: `swagger-codegen generate -i swagger-api-docs.json -l javascript`
   - PHP: `swagger-codegen generate -i swagger-api-docs.json -l php`
   - Java, Python, dll

## 🔗 Tools yang Support OpenAPI 3.0

- ✅ Swagger UI
- ✅ Swagger Editor
- ✅ Postman
- ✅ Insomnia
- ✅ Stoplight Studio
- ✅ ReDoc
- ✅ SwaggerHub
- ✅ API Blueprint
- ✅ VS Code Extensions (OpenAPI, Swagger Viewer)

## 📝 Validasi Dokumentasi

### Online Validators:

1. **Swagger Editor:**

   - https://editor.swagger.io/
   - Paste JSON, lihat error jika ada

2. **OpenAPI Tools:**

   - https://openapi.tools/
   - Berbagai validator dan tools

3. **API Tools:**
   - https://apitools.dev/swagger-parser/
   - https://inspector.swagger.io/builder

### Command Line:

```bash
# Install swagger-cli
npm install -g @apidevtools/swagger-cli

# Validate
swagger-cli validate swagger-api-docs.json
```

## 🎨 Alternatif Swagger UI - ReDoc

Jika ingin tampilan lebih modern, gunakan ReDoc:

```html
<!DOCTYPE html>
<html>
  <head>
    <title>API Documentation</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700|Roboto:300,400,700"
      rel="stylesheet"
    />
    <style>
      body {
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <redoc spec-url="swagger-api-docs.json"></redoc>
    <script src="https://cdn.redoc.ly/redoc/latest/bundles/redoc.standalone.js"></script>
  </body>
</html>
```

## 📄 License

API Documentation - OpenAPI 3.0 Format  
Created: 27 November 2025

---

**Selamat menggunakan dokumentasi Swagger API!** 🚀

Untuk pertanyaan atau update dokumentasi, silakan edit file `swagger-api-docs.json`.
