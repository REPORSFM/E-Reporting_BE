# ============================================================

# INSTRUKSI SETUP - Akses dari Komputer Lain

# ============================================================

## ❌ Masalah Saat Ini:

Ketika akses `http://192.168.30.70:8080/api/login` dari komputer lain, muncul error 404 Not Found dari Apache.

## ✅ Penyebab:

Apache berjalan di port 8080 tapi DocumentRoot-nya bukan ke project CodeIgniter kita.

## 🔧 SOLUSI - Setup Apache Virtual Host

### CARA CEPAT (Copy-Paste):

#### 1. Buka File Virtual Host

Buka file ini dengan Notepad sebagai Administrator:

```
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

#### 2. Tambahkan Konfigurasi Ini di AKHIR FILE:

```apache
# Query Report API
<VirtualHost *:8080>
    DocumentRoot "C:/PROJEK/API/queryreport-api/public"

    <Directory "C:/PROJEK/API/queryreport-api/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**PENTING:**

- Ganti `C:/PROJEK/API/queryreport-api` sesuai lokasi project Anda
- Gunakan forward slash `/` bukan backslash `\`
- DocumentRoot harus mengarah ke folder `public`

#### 3. Pastikan Include Virtual Host Aktif

Buka file:

```
C:\xampp\apache\conf\httpd.conf
```

Cari baris ini (pakai Ctrl+F):

```apache
Include conf/extra/httpd-vhosts.conf
```

Pastikan TIDAK ada tanda `#` di depannya. Jika ada, hapus tanda `#`.

#### 4. Restart Apache

1. Buka **XAMPP Control Panel**
2. Klik **Stop** di baris Apache
3. Tunggu sampai benar-benar stop
4. Klik **Start** lagi

#### 5. Test!

**Dari Postman di komputer yang sama:**

```
POST http://192.168.30.70:8080/api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

**Dari Postman di komputer lain di jaringan yang sama:**

```
POST http://192.168.30.70:8080/api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

---

## 🔥 Jika Masih Tidak Bisa Akses dari Komputer Lain

### Tambahkan Firewall Rule:

Buka **PowerShell as Administrator**, jalankan:

```powershell
netsh advfirewall firewall add rule name="Apache Port 8080" dir=in action=allow protocol=TCP localport=8080
```

Verifikasi rule sudah ditambah:

```powershell
netsh advfirewall firewall show rule name="Apache Port 8080"
```

---

## 🐛 Troubleshooting

### 1. Apache Tidak Bisa Start

**Kemungkinan port 8080 bentrok.**

Cek apa yang pakai port 8080:

```powershell
netstat -ano | findstr :8080
```

Jika ada process lain, matikan atau ganti port di config Apache.

### 2. Masih 404 Not Found

**Cek apakah path DocumentRoot benar.**

Buka browser, akses:

```
http://localhost:8080
```

Harus muncul halaman CodeIgniter welcome page. Jika masih 404:

- Cek path di httpd-vhosts.conf
- Pastikan folder `public` ada
- Restart Apache lagi

### 3. Error 403 Forbidden

**Permission issue.**

Pastikan di config ada:

```apache
Require all granted
```

### 4. Blank Page / Error 500

**Cek log Apache:**

```
C:\xampp\apache\logs\error.log
```

Atau cek log CodeIgniter:

```
C:\PROJEK\API\queryreport-api\writable\logs\
```

### 5. Hanya Bisa Akses dari Localhost

**Firewall Windows blokir.**

Jalankan command firewall di atas, atau:

- Control Panel → Windows Defender Firewall
- Advanced Settings
- Inbound Rules → New Rule
- Port → TCP → 8080 → Allow

---

## ✅ Verifikasi Setup Berhasil

### Cek dari Browser:

```
http://192.168.30.70:8080
```

Harus muncul halaman CodeIgniter.

### Test API Login:

```
POST http://192.168.30.70:8080/api/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

### Test dari Command Line:

```powershell
Invoke-RestMethod -Uri "http://192.168.30.70:8080/api/login" -Method POST -Headers @{"Content-Type"="application/json"} -Body '{"username":"admin","password":"admin123"}'
```

Harus return response JSON dengan data user.

---

## 📝 Setelah Berhasil

Update baseURL di file `.env`:

```
app.baseURL = 'http://192.168.30.70:8080/'
```

Restart Apache sekali lagi.

---

## 🎯 Expected Result

Setelah setup, API bisa diakses dari:

- ✅ Komputer lokal: `http://localhost:8080/api/...`
- ✅ Komputer lokal (via IP): `http://192.168.30.70:8080/api/...`
- ✅ Komputer lain di network: `http://192.168.30.70:8080/api/...`
- ✅ Postman dari mana saja di network yang sama

---

**Selamat mencoba! Jika masih ada masalah, cek log error Apache dan CodeIgniter.** 🚀
