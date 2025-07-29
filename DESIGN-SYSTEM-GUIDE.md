# 🎨 Seller Panel Tasarım Sistemi Rehberi

## 📌 Genel Bakış

Bu döküman, Seller Panel için oluşturulmuş modern tasarım sisteminin kullanım rehberidir. Tasarım sistemi, tutarlı ve profesyonel bir kullanıcı deneyimi sağlamak için geliştirilmiştir.

---

## 🎨 Renk Paleti

### Ana Renkler

```css
/* Primary Colors */
--color-primary: #2B2D42;        /* Gunmetal - Ana renk */
--color-primary-dark: #0B090A;   /* Rich Black - Koyu varyant */
--color-primary-light: #8D99AE;  /* Cool Gray - Açık varyant */

/* Accent Colors */
--color-accent: #EF233C;         /* Imperial Red - Vurgu rengi */
--color-accent-dark: #D90429;    /* Crimson - Koyu vurgu */

/* Neutral Colors */
--color-background: #EDF2F4;     /* Anti-Flash White - Arka plan */
--color-surface: #FFFFFF;        /* Beyaz - Yüzey rengi */
--color-surface-alt: #F8F9FA;    /* Alternatif yüzey */
```

### Kullanım Örnekleri

- **Primary**: Ana butonlar, linkler, önemli UI elemanları
- **Accent**: CTA butonları, bildirimler, önemli vurgular
- **Neutral**: Arka planlar, kartlar, form elemanları

---

## 📝 Typography

### Font Ailesi

```css
--font-family-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
--font-family-heading: 'Poppins', var(--font-family-primary);
--font-family-mono: 'JetBrains Mono', 'Consolas', monospace;
```

### Font Boyutları (Major Third Scale - 1.25)

```css
--font-size-xs: 0.64rem;     /* 10.24px */
--font-size-sm: 0.8rem;      /* 12.8px */
--font-size-base: 1rem;      /* 16px */
--font-size-md: 1.25rem;     /* 20px */
--font-size-lg: 1.563rem;    /* 25px */
--font-size-xl: 1.953rem;    /* 31.25px */
--font-size-2xl: 2.441rem;   /* 39px */
--font-size-3xl: 3.052rem;   /* 48.8px */
```

### Kullanım

```html
<h1 class="h1">Ana Başlık</h1>
<h2 class="h2">Alt Başlık</h2>
<p class="lead">Öne çıkan paragraf</p>
<p>Normal paragraf metni</p>
<span class="text-sm text-muted">Küçük yardımcı metin</span>
```

---

## 📐 Spacing Sistemi

8px tabanlı spacing sistemi:

```css
--space-0: 0;         /* 0px */
--space-1: 0.25rem;   /* 4px */
--space-2: 0.5rem;    /* 8px */
--space-3: 0.75rem;   /* 12px */
--space-4: 1rem;      /* 16px */
--space-5: 1.25rem;   /* 20px */
--space-6: 1.5rem;    /* 24px */
--space-8: 2rem;      /* 32px */
--space-10: 2.5rem;   /* 40px */
--space-12: 3rem;     /* 48px */
--space-16: 4rem;     /* 64px */
--space-20: 5rem;     /* 80px */
--space-24: 6rem;     /* 96px */
```

### Kullanım

```html
<div class="p-4 m-2">Padding 16px, Margin 8px</div>
<div class="pt-6 mb-4">Padding-top 24px, Margin-bottom 16px</div>
<div class="px-8 my-6">Padding yatay 32px, Margin dikey 24px</div>
```

---

## 🔘 Butonlar

### Temel Buton Sınıfları

```html
<!-- Primary Buton -->
<button class="btn btn-primary">Primary Button</button>

<!-- Accent Buton -->
<button class="btn btn-accent">Accent Button</button>

<!-- Outline Buton -->
<button class="btn btn-outline">Outline Button</button>

<!-- Ghost Buton -->
<button class="btn btn-ghost">Ghost Button</button>

<!-- Boyutlar -->
<button class="btn btn-sm">Small</button>
<button class="btn">Default</button>
<button class="btn btn-lg">Large</button>

<!-- Disabled State -->
<button class="btn btn-primary" disabled>Disabled</button>
```

---

## 📋 Form Elemanları

### Input

```html
<div class="form-group">
  <label class="form-label">E-posta Adresi</label>
  <input type="email" class="form-control" placeholder="ornek@email.com">
</div>
```

### Select

```html
<div class="form-group">
  <label class="form-label">Kategori</label>
  <select class="form-control form-select">
    <option>Seçiniz...</option>
    <option>Kategori 1</option>
    <option>Kategori 2</option>
  </select>
</div>
```

### Toggle Switch

```html
<label class="toggle-switch">
  <input type="checkbox">
  <span class="toggle-switch-slider"></span>
</label>
```

---

## 🃏 Kartlar

### Temel Kart

```html
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Kart Başlığı</h3>
  </div>
  <div class="card-body">
    <p>Kart içeriği buraya gelir.</p>
  </div>
  <div class="card-footer">
    <button class="btn btn-primary">Aksiyon</button>
  </div>
</div>
```

### Stat Kartı

```html
<div class="stat-card">
  <div class="stat-card-header">
    <h6 class="stat-card-title">Toplam Satış</h6>
    <div class="stat-card-icon primary">
      <i class="icon-chart"></i>
    </div>
  </div>
  <div class="stat-card-value">₺45,250</div>
  <div class="stat-card-trend up">
    <i class="icon-arrow-up"></i>
    <span>12.5%</span>
  </div>
  <div class="progress">
    <div class="progress-bar" style="width: 75%"></div>
  </div>
</div>
```

---

## 📊 Tablolar

```html
<div class="data-table-container">
  <div class="data-table-header">
    <h3 class="data-table-title">Ürünler</h3>
    <div class="data-table-actions">
      <button class="btn btn-primary btn-sm">Yeni Ekle</button>
    </div>
  </div>
  
  <table class="data-table">
    <thead>
      <tr>
        <th>Ürün Adı</th>
        <th>Fiyat</th>
        <th>Stok</th>
        <th>İşlemler</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Ürün 1</td>
        <td>₺150</td>
        <td>25</td>
        <td>
          <button class="btn btn-ghost btn-sm">Düzenle</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

---

## 🔔 Bildirimler ve Uyarılar

### Alert

```html
<div class="alert alert-success">
  <i class="icon-check"></i>
  İşlem başarıyla tamamlandı!
</div>

<div class="alert alert-error">
  <i class="icon-x"></i>
  Bir hata oluştu!
</div>
```

### Badge

```html
<span class="badge badge-primary">Yeni</span>
<span class="badge badge-accent">İndirim</span>
<span class="badge badge-success">Aktif</span>
```

---

## 📱 Responsive Breakpoints

```css
/* Mobile First Yaklaşım */
- Base: 0px+
- sm: 640px+
- md: 768px+
- lg: 1024px+
- xl: 1280px+
- 2xl: 1536px+
```

### Kullanım

```html
<!-- Mobilde gizle, tablet'te göster -->
<div class="d-none md:d-block">
  Tablet ve üzerinde görünür
</div>

<!-- Responsive Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

---

## 🎭 Animasyonlar

### Hazır Animasyonlar

```html
<!-- Fade In -->
<div class="animate-fadeIn">
  Fade in animasyonu
</div>

<!-- Slide In -->
<div class="animate-slideInLeft">
  Soldan kayarak gelen
</div>

<!-- Pulse -->
<div class="animate-pulse">
  Nabız efekti
</div>
```

### Transition Süreleri

```css
--transition-fast: 150ms ease-in-out;
--transition-base: 250ms ease-in-out;
--transition-slow: 350ms ease-in-out;
```

---

## 🔧 Utility Sınıfları

### Display

```html
<div class="d-none">Gizli</div>
<div class="d-block">Block</div>
<div class="d-flex">Flex</div>
<div class="d-grid">Grid</div>
```

### Flexbox

```html
<div class="flex items-center justify-between">
  <span>Sol</span>
  <span>Sağ</span>
</div>
```

### Text

```html
<p class="text-primary">Primary metin</p>
<p class="text-accent">Accent metin</p>
<p class="text-muted">Soluk metin</p>
<p class="font-bold">Kalın metin</p>
```

### Shadow

```html
<div class="shadow-sm">Küçük gölge</div>
<div class="shadow">Normal gölge</div>
<div class="shadow-lg">Büyük gölge</div>
```

---

## 💡 Best Practices

1. **Tutarlılık**: Her zaman tanımlı değişkenleri kullanın
2. **Semantic HTML**: Doğru HTML elemanlarını kullanın
3. **Accessibility**: ARIA label'ları ekleyin
4. **Mobile First**: Önce mobil tasarım yapın
5. **Performance**: Gereksiz animasyon ve efektlerden kaçının

---

## 🚀 Hızlı Başlangıç

```html
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Panel</title>
  
  <!-- Design System CSS -->
  <link rel="stylesheet" href="/css/seller-design-system.css">
  <link rel="stylesheet" href="/css/seller-components.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
  <!-- İçerik -->
</body>
</html>
```

---

## 📚 Kaynaklar

- [Figma Tasarım Dosyası](#)
- [Component Library](#)
- [Icon Library](#)
- [Color Palette Generator](#)

---

*Bu döküman sürekli güncellenmektedir. Son güncelleme: {{ Tarih }}*