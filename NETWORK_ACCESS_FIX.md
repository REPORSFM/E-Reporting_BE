# ============================================================

# SOLUSI CEPAT - Akses dari Komputer Lain

# ============================================================

## Masalah:

Error 404 saat akses http://192.168.30.70:8080/api/login dari komputer lain.

## Penyebab:

Apache berjalan di port 8080 tapi DocumentRoot tidak mengarah ke project CodeIgniter.

## SOLUSI 1: Gunakan PHP Built-in Server (TERCEPAT) ✅

### Stop Apache Dulu:

1. Buka XAMPP Control Panel
2. Klik Stop di Apache

### Jalankan PHP Server:

```powershell
cd C:\PROJEK\API\queryreport-api
php -S 0.0.0.0:8080 -t public
```

### ATAU gunakan spark:

```powershell
cd C:\PROJEK\API\queryreport-api
php spark serve --host=0.0.0.0 --port=8080
```

### Test dari Komputer Lain:

```
POST http://192.168.30.70:8080/api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

---

## SOLUSI 2: Konfigurasi Apache Virtual Host (PRODUCTION)

### Langkah 1: Edit httpd-vhosts.conf

File: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Tambahkan di akhir file:

```apache
<VirtualHost *:8080>
    DocumentRoot "C:/PROJEK/API/queryreport-api/public"

    <Directory "C:/PROJEK/API/queryreport-api/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Langkah 2: Pastikan Include Virtual Host

File: `C:\xampp\apache\conf\httpd.conf`

Cari dan pastikan TIDAK ada tanda `#`:

```apache
Include conf/extra/httpd-vhosts.conf
```

### Langkah 3: Restart Apache

1. XAMPP Control Panel
2. Stop Apache
3. Start Apache

### Test:

```
http://192.168.30.70:8080/api/login
```

---

## SOLUSI 3: Copy ke htdocs (ALTERNATIF)

### Copy Project:

```powershell
xcopy "C:\PROJEK\API\queryreport-api" "C:\xampp\htdocs\queryreport-api\" /E /I /Y
```

### Akses:

```
http://192.168.30.70:8080/queryreport-api/public/api/login
```

### Update .env:

```
app.baseURL = 'http://192.168.30.70:8080/queryreport-api/public/'
```

---

## Firewall (Jika Masih Tidak Bisa Akses dari Komputer Lain)

Jalankan PowerShell sebagai Administrator:

```powershell
# Allow port 8080
netsh advfirewall firewall add rule name="CodeIgniter API Port 8080" dir=in action=allow protocol=TCP localport=8080

# Cek rule
netsh advfirewall firewall show rule name="CodeIgniter API Port 8080"
```

---

## Quick Test Commands

### Cek Port 8080:

```powershell
netstat -ano | findstr :8080
```

### Cek Apache Status:

```powershell
tasklist | findstr httpd
```

### Test dari Command Line:

```powershell
curl http://192.168.30.70:8080/api/login -Method POST -Headers @{"Content-Type"="application/json"} -Body '{"username":"admin","password":"admin123"}'
```

---

## REKOMENDASI ⭐

**Untuk Development/Testing Cepat:**

- Gunakan SOLUSI 1 (PHP Built-in Server)
- Stop Apache
- Run: `php -S 0.0.0.0:8080 -t public`

**Untuk Production/Team Development:**

- Gunakan SOLUSI 2 (Apache Virtual Host)
- Lebih stabil dan banyak fitur

---

## Setelah Setup, Test dengan Postman:

### Import Postman Collection:

File: `postman_collection.json`

### Update Base URL di Postman Environment:

- Variable: `base_url`
- Value: `http://192.168.30.70:8080`

### Test Endpoints:

```
POST {{base_url}}/api/login
POST {{base_url}}/api/queryreport/getall
POST {{base_url}}/api/queryreport/execute
```

---

**Pilih salah satu solusi dan test sekarang!** 🚀
