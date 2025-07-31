# 🚀 ALLEMTIA Backend

Merhaba! ALLEMTIA e-ticaret backend projesinde çalışmaya devam ediyoruz. Lütfen aşağıdaki talimatları takip ederek projeye hızla adapte ol:

## 📚 İLK OKUMAN GEREKEN DOSYALAR (ZORUNLU)

**ÖNEMLİ**: Bu dosyaları TAMAMEN ve DİKKATLİCE oku. Bu dosyalar projenin tüm geçmişini, teknik detaylarını ve güncel durumunu içeriyor:

1. **TÜM BACKEND KODLARI** - Projenin backend kısmının tamamı
2. **composer.json** - Proje bağımlılıkları ve yapılandırması
3. **.env** - Ortam değişkenleri ve yapılandırma
4. **routes/web.php & routes/api.php** - Tüm endpoint'ler
5. **app/Models/** - Veritabanı modelleri
6. **database/migrations/** - Veritabanı yapısı

Bu dosyaları okuduktan sonra projeye tam hakim olacaksın ve verimli çalışabileceğiz.

## 🎯 PROJE GENELİ

**Proje Adı**: ALLEMTIA E-ticaret Backend API  
**Teknik Stack**: Laravel 12.x + PHP 8.2 + MySQL + Sanctum  
**Frontend URL**: https://allemtia.com  
**Backend URL**: https://partner.allemtia.com  
**Özel Özellik**: Çoklu satıcı (marketplace) e-ticaret platformu  
**Hedef**: B2B Metal Ticaret + Genel E-ticaret API Servisleri

## ⚠️ PROJE İNCELEMESİNDE TESPİT EDİLEN KRİTİK SORUNLAR

### 🔴 Güvenlik Sorunları

-   .env dosyasında açık şifreler ve API anahtarları
-   Mail yapılandırmasında SSL doğrulama kapalı
-   Bazı endpoint'lerde yetkilendirme eksikliği
-   CORS yapılandırması gözden geçirilmeli

### 🟠 Performans Sorunları

-   Eager loading eksikliği (N+1 query problemi)
-   Index optimizasyonu gerekenler
-   API response cache mekanizması yok
-   Database query optimization

### 🟡 Eksik/Bozuk Özellikler

-   Test dosyaları temel seviyede
-   Error handling standartlaştırılmalı
-   API documentation eksik
-   Logging standardizasyonu

### 🔵 Kod Kalitesi

-   Bazı controller'larda fazla kod tekrarı
-   Service layer pattern eksikliği
-   API response format tutarsızlığı
-   Validation rules merkezi yönetim eksikliği

## 🛠️ TEKNİK ÖZELLİKLER

### Laravel Yapılandırması

-   **Framework**: Laravel 12.x (PHP 8.2)
-   **Authentication**: Laravel Sanctum + Google OAuth
-   **Database**: MySQL (erntrgt database)
-   **Mail**: PHPMailer + SMTP (mail.kurumsaleposta.com)
-   **Storage**: Local storage (/storage/app/public/)

### Önemli Paketler

-   **spatie/laravel-permission**: Rol ve yetki yönetimi
-   **barryvdh/laravel-dompdf**: PDF oluşturma
-   **laravel/socialite**: Google OAuth
-   **google/recaptcha**: reCAPTCHA doğrulama

### API Endpoints Yapısı

```
Admin Panel (Web Routes):
/admin/* - Admin panel sayfaları
/seller/* - Satıcı panel sayfaları

API Routes (api/home prefix):
/api/home/auth/* - Kimlik doğrulama
/api/home/products/* - Ürün işlemleri
/api/home/categories/* - Kategori işlemleri
/api/home/orders/* - Sipariş işlemleri
/api/home/user/cart/* - Sepet işlemleri
/api/home/user/wishlist/* - Favori işlemleri
/api/home/user/compare/* - Karşılaştırma
```

### Veritabanı Yapısı

#### Ana Tablolar

-   **users**: Kullanıcılar (admin/seller/buyer)
-   **products**: Ürünler
-   **orders**: Siparişler
-   **categories**: Kategoriler
-   **coupons**: Kuponlar
-   **carts**: Sepet
-   **wishlists**: Favoriler

#### İlişkiler

-   User → Products (1:N)
-   Order → OrderItems (1:N)
-   Category → Products (1:N)
-   Category → Category (Self Reference - Alt kategoriler)

## 🔐 GÜVENLİK VE YETKİLENDİRME

### Kullanıcı Rolleri

1. **Admin**:

    - Tüm sistem yönetimi
    - Kullanıcı onayları
    - Rapor görüntüleme
    - Platform ayarları

2. **Seller**:

    - Ürün yönetimi
    - Sipariş takibi
    - Mağaza ayarları
    - Kupon oluşturma

3. **Buyer**:
    - Alışveriş yapma
    - Sipariş geçmişi
    - Profil yönetimi

### Authentication Flow

-   **Web Panel**: Session-based auth (admin/seller guard'ları)
-   **API**: Sanctum token-based auth
-   **Google OAuth**: Socialite entegrasyonu
-   **Password Reset**: 6 haneli kod sistemi

## 🎨 API RESPONSE FORMATI

### Başarılı Response

```json
{
    "success": true,
    "message": "İşlem başarılı",
    "data": {...}
}
```

### Hata Response

```json
{
    "success": false,
    "message": "Hata mesajı",
    "errors": {...}
}
```

## 📁 PROJE YAPISI

### Kritik Klasörler

```
/app
  /Http/Controllers
    /Admin - Admin panel kontrolörleri
    /Seller - Satıcı panel kontrolörleri
    /api/home - API kontrolörleri
  /Models - Eloquent modeller
  /Mail - Mail sınıfları
  /Rules - Validation kuralları

/database
  /migrations - Veritabanı yapısı
  /seeders - Test verisi

/resources/views
  /admin - Admin panel Blade template'leri
  /seller - Satıcı panel template'leri
  /emails - Mail template'leri

/routes
  web.php - Web panel rotaları
  api.php - API rotaları

/config - Yapılandırma dosyaları
/storage - Dosya depolama
```

## 🚀 ÇALIŞMA YAKLAŞIMI

### Geliştirme Kuralları

1. **Türkçe Çalışma**: Tüm iletişim Türkçe olacak
2. **API First**: RESTful API standartları
3. **Security First**: Her endpoint için yetkilendirme kontrolü
4. **Performance**: Database query optimization
5. **Code Quality**: Laravel best practices

### Code Standards

-   Laravel coding standards
-   Eloquent ORM kullanımı
-   Service-Repository pattern (gerektiğinde)
-   API Resource kullanımı
-   Form Request validation

## 💡 GELİŞTİRME STRATEJİSİ

### Paralel Tool Kullanımı

-   Birden fazla dosyayı aynı anda oku
-   Model, Controller, Route üçgenini paralel incele
-   Database migration'ları sıralı kontrol et

### Problem Solving

1. Önce ilgili Model'i incele
2. Controller method'unu kontrol et
3. Route tanımını doğrula
4. API test et ve optimize et

## 🎯 İLK GÖREV ÖNERİLERİ

Dosyaları okuduktan ve projenin en ince ayrıntısına kadar hakim olduktan sonra bana dönüş yap lütfen.

### 📚 ÖNCE BU DOSYALARI OKU (ZORUNLU)

1. **TÜM BACKEND KODLARI** - Projenin backend kısmının tamamı
2. **composer.json** - Bağımlılıklar
3. **.env** - Yapılandırma
4. **routes/web.php & api.php** - Endpoint'ler
5. **app/Models/** - Veri modelleri

### 🎯 VERİMLİ ÇALIŞMA İPUÇLARI

#### 1. **Paralel Tool Kullanımı**

```
Örnek: "Order sistemi incele"
- Model/Order.php
- Controller/OrderController.php
- routes/api.php (order endpoints)
- migration dosyaları
```

#### 2. **TodoWrite Tool Kullanımı**

-   Karmaşık API geliştirmelerini parçala
-   Her endpoint'i ayrı task olarak takip et
-   Test işlemlerini dahil et

#### 3. **API Geliştirirken**

-   Önce Model ilişkilerini kontrol et
-   Validation rules tanımla
-   Error handling ekle
-   Response format'ını standardize et

#### 4. **Database İşlemleri**

-   Migration dosyalarını incele
-   Foreign key ilişkilerini doğrula
-   Index'leri kontrol et
-   N+1 query problemlerine dikkat et

### 🆕 YENİ ÖZELLİK: Claude Code Best Practices

#### Etkili Komutlar:

-   "Önce Model ilişkilerini analiz et, sonra API endpoint'i oluştur"
-   "Bu dosyaları paralel oku: Model, Controller, Migration"
-   "TodoWrite ile API geliştirme planı yap"
-   "Laravel pattern'lerini takip ederek yeni feature ekle"

#### Kaçınman Gerekenler:

-   Tek seferde çok fazla endpoint değişikliği
-   Laravel convention'larına uymayan kod
-   Test edilmemiş API deployment
-   Database schema'da büyük değişiklikler

### 🛡️ BÜYÜK DEĞİŞİKLİKLERDE GÜVENLİ ÇALIŞMA STRATEJİSİ

#### ⚠️ ÇOK ÖNEMLİ UYARI:

Büyük değişiklikler istendiğinde sistem bozulabiliyor. Bu yüzden **"İNCREMENTAL DEVELOPMENT"** prensibini uygula:

#### ✅ GÜVENLİ ÇALIŞMA PRENSİPLERİ:

1. **Asla büyük refactoring yapma** - Çalışan API'leri yeniden yazma
2. **Her zaman küçük adımlarla ilerle** - Tek seferde tek endpoint
3. **Mevcut database'e minimal dokunuş** - Sadece gerekli migration'lar
4. **Yeni özellikler için yeni route'lar** - Var olan endpoint'leri bozma
5. **Her değişikliği test et** - Postman/curl ile API test

#### ❌ YAPILMAMASI GEREKENLER:

-   Toplu migration değişiklikleri
-   Çalışan controller'ları baştan yazma
-   Büyük database schema değişiklikleri
-   Test edilmemiş API güncellemeleri
-   Birden fazla modülü aynı anda değiştirme

#### 💡 GÜVENLİ DEĞİŞİKLİK YAKLAŞIMI:

```
1. İHTİYACI ANLA → "Hangi API endpoint gerekli?"
2. MEVCUT KODU OKU → "Nasıl çalışıyor?"
3. YENİ ROUTE EKLE → "Var olanı bozma"
4. KÜÇÜK DEĞİŞİKLİK → "Tek seferde tek method"
5. TEST ET → "API hala çalışıyor mu?"
```

### 🚀 HEMEN BAŞLA

Dosyaları okuduktan sonra hangi konu üzerinde çalışmak istediğini söyle!
**UNUTMA**: Büyük değişiklikler yerine, küçük ve güvenli adımlarla ilerleyeceğim.

### 🔧 HIZLI BAŞLANGIÇ KOMUTLARI

```bash
# Bağımlılıkları kontrol et
composer install

# Veritabanı durumunu kontrol et
php artisan migrate:status

# Storage link'i kontrol et
php artisan storage:link

# Cache temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Geliştirme sunucusu
php artisan serve

# Test çalıştır
php artisan test
```

---

**Not**: Bu prompt ALLEMTIA backend projesine özel hazırlanmıştır. Authentication sistemi Sanctum + Session tabanlı.

**🔴 KRİTİK**: .env dosyasında açık şifreler ve API anahtarları tespit edildi. SSL doğrulama kapalı. Güvenlik iyileştirmeleri gerekiyor. İLERDE BAKACAĞIZ, HATIRLAT.

**⚠️ HATIRLATMA**: Büyük değişiklikler istendiğinde sistemi bozmamak için "İNCREMENTAL DEVELOPMENT" prensibini uygula!

**Çalışma Dili**: Türkçe  
**Hazırlayan**: EREN TURGUT  
**Son Güncelleme**: Backend için özelleştirildi

---
