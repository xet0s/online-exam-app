# 🎓 Online Exam App

> Sınav programlama, sınıf yönetimi ve kullanıcı onay süreçlerini tek çatı altında toplayan modern bir Laravel tabanlı sınav yönetim sistemi.

---

## 📋 İçindekiler

- [Özellikler](#-özellikler)
- [Teknoloji Yığını](#-teknoloji-yığını)
- [Sistem Gereksinimleri](#-sistem-gereksinimleri)
- [Kurulum](#-kurulum)
- [Ortam Değişkenleri](#-ortam-değişkenleri)
- [Kullanım](#-kullanım)
- [Roller ve Yetkiler](#-roller-ve-yetkiler)
- [API & Rotalar](#-api--rotalar)
- [Testler](#-testler)
- [Katkıda Bulunma](#-katkıda-bulunma)
- [Lisans](#-lisans)

---

## ✨ Özellikler

| Özellik | Açıklama |
|---|---|
| 🔐 **Kimlik Doğrulama** | Kayıt, giriş ve onay bekleyen durum sayfaları |
| 👥 **Kullanıcı Yönetimi** | Admin panelinden kullanıcı oluşturma, düzenleme, silme |
| ✅ **Onay Sistemi** | Yeni kullanıcılar admin onayından geçmeden sisteme erişemez |
| 🏫 **Sınıf Yönetimi** | Derslik/sınıf oluşturma, düzenleme ve listeleme |
| 📝 **Sınav Yönetimi** | Sınav oluşturma, bölüm başkanı ve dekan onayı akışı |
| 📊 **Otomatik Atama** | Sınavları sınıflara otomatik olarak yerleştiren algoritma |
| 📄 **PDF Dışa Aktarma** | Sınav programını PDF olarak indirme |
| 🗄️ **phpMyAdmin Proxy** | Admin panelinden veritabanına doğrudan erişim |

---

## 🛠 Teknoloji Yığını

- **Backend:** [Laravel 13](https://laravel.com) (PHP 8.3+)
- **Frontend:** Blade şablonları, Vite
- **Veritabanı:** SQLite (varsayılan) / MySQL
- **PDF:** [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf)
- **Test:** PHPUnit 12
- **Geliştirme Araçları:** Laravel Sail, Laravel Pint, Laravel Pail

---

## 📦 Sistem Gereksinimleri

- PHP **8.3** veya üzeri
- Composer **2.x**
- Node.js **18+** ve npm
- SQLite **3.x** (veya MySQL 8+)

---

## 🚀 Kurulum

### 1. Repoyu Klonlayın

```bash
git clone https://github.com/kullanici-adi/online-exam-app.git
cd online-exam-app
```

### 2. Tek Komutla Kurulum (Önerilen)

```bash
composer run setup
```

Bu komut sırasıyla şunları yapar:
1. PHP bağımlılıklarını yükler (`composer install`)
2. `.env` dosyasını oluşturur
3. Uygulama anahtarını üretir
4. Veritabanı migrasyonlarını çalıştırır
5. Node.js bağımlılıklarını yükler ve frontend'i derler

### 3. Manuel Kurulum

```bash
# PHP bağımlılıklarını yükle
composer install

# Ortam dosyasını oluştur
cp .env.example .env

# Uygulama anahtarı üret
php artisan key:generate

# Veritabanını hazırla
php artisan migrate

# Node.js bağımlılıklarını yükle ve derle
npm install
npm run build
```

### 4. Geliştirme Sunucusunu Başlatın

```bash
composer run dev
```

Uygulama şu adreste çalışacaktır: **http://localhost:8000**

---

## ⚙️ Ortam Değişkenleri

`.env.example` dosyasını `.env` olarak kopyalayıp aşağıdaki değişkenleri düzenleyin:

```env
APP_NAME="Online Exam App"
APP_ENV=local
APP_URL=http://localhost

# Veritabanı (varsayılan: SQLite)
DB_CONNECTION=sqlite

# MySQL kullanmak için:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=exam_app
# DB_USERNAME=root
# DB_PASSWORD=

# Kuyruk & Oturum
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

---

## 🖥 Kullanım

Uygulamayı başlattıktan sonra:

1. **`/register`** adresine giderek hesap oluşturun.
2. Hesabınız bir admin tarafından **onaylanana** kadar bekleyin.
3. Onaylandıktan sonra **`/dashboard`** üzerinden sistemi kullanmaya başlayın.

---

## 👤 Roller ve Yetkiler

| Rol | Açıklama |
|---|---|
| **Admin** | Tüm kullanıcıları, sınıfları, sınavları ve onay süreçlerini yönetebilir |
| **Bölüm Başkanı** | Kendi bölümüne ait sınavları onaylayabilir |
| **Dekan** | Bölüm başkanının onayladığı sınavları nihai olarak onaylar |
| **Öğretim Üyesi** | Sınav ekleyebilir, sınıf ve sınav listelerini görebilir |

---

## 🗺 API & Rotalar

| Metod | Rota | Açıklama |
|---|---|---|
| `GET` | `/` | Karşılama sayfası |
| `GET/POST` | `/login` | Giriş |
| `GET/POST` | `/register` | Kayıt |
| `GET` | `/pending-approval` | Onay bekleme sayfası |
| `GET` | `/dashboard` | Ana panel |
| `RESOURCE` | `/classrooms` | Sınıf CRUD işlemleri |
| `RESOURCE` | `/exams` | Sınav CRUD işlemleri |
| `POST` | `/exams/approve-chair/{exam}` | Bölüm başkanı onayı |
| `POST` | `/exams/approve-dean/{exam}` | Dekan onayı |
| `RESOURCE` | `/users` | Kullanıcı yönetimi |
| `GET` | `/approvals` | Onay bekleyen kullanıcılar |
| `POST` | `/allocation/run` | Otomatik sınıf atama |
| `POST` | `/pdf/export` | PDF oluştur |
| `GET` | `/pdf/download` | PDF indir |
| `ANY` | `/admin/phpmyadmin/{path?}` | phpMyAdmin proxy |

---

## 🧪 Testler

```bash
# Tüm testleri çalıştır
composer run test

# Sadece PHPUnit ile çalıştır
php artisan test

# Belirli bir test dosyasını çalıştır
php artisan test tests/Feature/UserManagementTest.php
```

### Test Kapsamı

- `tests/Feature/ExampleTest.php` — Temel uygulama testi
- `tests/Feature/PhpMyAdminAccessTest.php` — Yetkilendirme kontrolleri
- `tests/Feature/UserManagementTest.php` — Kullanıcı yönetimi akışları
- `tests/Unit/ExamAllocationTest.php` — Otomatik atama algoritması
- `tests/Unit/ExampleTest.php` — Temel birim testi

---

## 🤝 Katkıda Bulunma

1. Bu repoyu fork'layın
2. Yeni bir branch oluşturun: `git checkout -b feature/yeni-ozellik`
3. Değişikliklerinizi commit'leyin: `git commit -m 'feat: yeni özellik ekle'`
4. Branch'inizi push'layın: `git push origin feature/yeni-ozellik`
5. Pull Request açın

---

## 📄 Lisans

Bu proje [MIT Lisansı](https://opensource.org/licenses/MIT) ile lisanslanmıştır.

---

<p align="center">Laravel 13 · PHP 8.3 · Vite · phpMyAdmin · dompdf</p>
