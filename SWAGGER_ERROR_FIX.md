# ❌ SOLUSI - Swagger Error "Failed to fetch" / CORS

## Masalah:

Ketika buka `api-documentation.html` langsung dari file explorer (file://), muncul error:

```
Failed to fetch.
Possible Reasons:
- CORS
- Network Failure
- URL scheme must be "http" or "https" for CORS request
```

## Penyebab:

Browser tidak mengizinkan Swagger UI load file JSON dari file system (`file://`) karena CORS policy. Harus diakses via HTTP server.

## ✅ SOLUSI (3 Cara):

### **Solusi 1: Akses via Apache/HTTP Server** ⭐ RECOMMENDED

File sudah di-copy ke folder `public/`. Sekarang akses via browser:

#### **Local (dari komputer yang sama):**

```
http://localhost:8080/api-documentation.html
```

#### **Network (dari komputer lain):**

```
http://192.168.30.70:8080/api-documentation.html
```

#### **Alternatif ReDoc (tampilan lebih modern):**

```
http://localhost:8080/api-documentation-redoc.html
http://192.168.30.70:8080/api-documentation-redoc.html
```

---

### **Solusi 2: Gunakan PHP Built-in Server**

Jika Apache tidak berjalan:

```powershell
cd C:\PROJEK\API\queryreport-api\public
php -S localhost:8080
```

Lalu akses:

```
http://localhost:8080/api-documentation.html
```

---

### **Solusi 3: Import JSON ke Swagger Editor Online**

1. **Buka:** https://editor.swagger.io/

2. **File → Import File**

3. **Pilih:** `C:\PROJEK\API\queryreport-api\swagger-api-docs.json`

4. Dokumentasi akan ditampilkan di editor online

5. Bisa edit dan export sesuai kebutuhan

---

## 📱 Test Dokumentasi:

### **1. Buka Swagger UI:**

```
http://localhost:8080/api-documentation.html
```

### **2. Try It Out:**

- Expand endpoint `POST /api/login`
- Klik **"Try it out"**
- Edit request body:
  ```json
  {
    "username": "admin",
    "password": "admin123"
  }
  ```
- Klik **"Execute"**
- Lihat response di bawah

### **3. Test Endpoint Lain:**

- Expand section **Query Report**
- Test endpoint **POST /api/queryreport/getall**
- Klik "Try it out"
- Edit body atau kosongkan: `{}`
- Execute dan lihat hasil

---

## 🔗 URL yang Bisa Diakses:

Setelah file di-copy ke `public/`:

| Dokumentasi    | URL Local                                          | URL Network                                            |
| -------------- | -------------------------------------------------- | ------------------------------------------------------ |
| **Swagger UI** | http://localhost:8080/api-documentation.html       | http://192.168.30.70:8080/api-documentation.html       |
| **ReDoc**      | http://localhost:8080/api-documentation-redoc.html | http://192.168.30.70:8080/api-documentation-redoc.html |
| **JSON Spec**  | http://localhost:8080/swagger-api-docs.json        | http://192.168.30.70:8080/swagger-api-docs.json        |

---

## 🐛 Troubleshooting:

### **1. Masih error "Failed to fetch"**

**Penyebab:** Server tidak jalan

**Solusi:**

```powershell
# Cek apakah Apache/server berjalan
netstat -ano | findstr :8080

# Jika tidak ada hasil, start Apache dari XAMPP Control Panel
# Atau jalankan PHP server:
cd C:\PROJEK\API\queryreport-api\public
php -S localhost:8080
```

### **2. 404 Not Found**

**Penyebab:** File belum di-copy ke public/

**Solusi:**

```powershell
cd C:\PROJEK\API\queryreport-api
Copy-Item api-documentation.html public/
Copy-Item api-documentation-redoc.html public/
Copy-Item swagger-api-docs.json public/
```

### **3. JSON Error / Cannot read**

**Penyebab:** Path JSON salah di HTML

**Solusi:** Sudah diperbaiki, semua file di folder yang sama (`public/`)

### **4. Tidak bisa akses dari komputer lain**

**Penyebab:** Firewall atau Apache config

**Solusi:**

- Setup Apache Virtual Host (lihat `SETUP_INSTRUKSI.md`)
- Atau jalankan firewall rule:
  ```powershell
  netsh advfirewall firewall add rule name="Apache 8080" dir=in action=allow protocol=TCP localport=8080
  ```

---

## 🎯 Kenapa Postman Bisa?

**Postman tidak terpengaruh CORS** karena:

- Postman adalah native app, bukan browser
- Tidak enforce same-origin policy
- Bisa akses file system langsung
- Bisa akses API tanpa header CORS

**Browser (Swagger UI) butuh HTTP server** karena:

- Enforce CORS policy untuk security
- File system (`file://`) dianggap berbeda origin dengan fetch request
- Harus akses via `http://` atau `https://`

---

## ✅ Status Saat Ini:

✅ File sudah di-copy ke `public/`  
✅ Apache berjalan di port 8080  
✅ Dokumentasi bisa diakses via:

- http://localhost:8080/api-documentation.html
- http://192.168.30.70:8080/api-documentation.html

---

## 📸 Screenshot Test:

1. **Buka browser**
2. **Akses:** http://localhost:8080/api-documentation.html
3. **Lihat:** Dokumentasi Swagger tampil lengkap
4. **Try:** Test endpoint Login
5. **Success:** Response muncul

---

**Sekarang buka browser dan akses dokumentasi!** 🚀

```
http://localhost:8080/api-documentation.html
```
