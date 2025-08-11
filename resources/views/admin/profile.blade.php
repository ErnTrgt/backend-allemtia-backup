@extends('layouts.admin')

@section('title', 'Profil')
@section('header-title', 'Profil Ayarları')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/profile.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Profil Ayarları</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Profil</span>
    </div>
</div>

<!-- Success/Error Messages -->
@if (session('success'))
    <div class="alert alert-success alert-glass">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-glass">
        <i class="bi bi-exclamation-circle-fill"></i>
        Lütfen formdaki hataları düzeltin.
    </div>
@endif

<div class="profile-container">
    <!-- Profile Header Card -->
    <div class="profile-header-card">
        <div class="avatar-section">
            <div class="avatar-wrapper">
                @if($admin->avatar)
                    <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Admin Avatar" class="avatar-img">
                @else
                    <img src="{{ asset('admin/src/images/user-avatar.png') }}" alt="Admin Avatar" class="avatar-img">
                @endif
                <button class="avatar-edit-btn" onclick="document.getElementById('avatarInput').click()">
                    <i class="bi bi-camera"></i>
                </button>
                <input type="file" id="avatarInput" style="display: none;" accept="image/*">
            </div>
        </div>
        
        <h2 class="profile-name">{{ $admin->name }}</h2>
        <p class="profile-role">Sistem Yöneticisi</p>
        
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-value">{{ $admin->created_at->diffInDays() }}</div>
                <div class="stat-label">Gün</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ App\Models\Product::count() }}</div>
                <div class="stat-label">Toplam Ürün</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ App\Models\User::where('role', 'seller')->count() }}</div>
                <div class="stat-label">Aktif Satıcı</div>
            </div>
        </div>
    </div>
    
    <!-- Profile Content Grid -->
    <div class="profile-content">
        <!-- Left Column - Info Cards -->
        <div class="profile-sidebar">
            <!-- Contact Info Card -->
            <div class="info-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-person-lines-fill"></i>
                        İletişim Bilgileri
                    </h3>
                </div>
                <div class="card-body">
                    <ul class="info-list">
                        <li class="info-item">
                            <span class="info-label">E-posta:</span>
                            <span class="info-value">{{ $admin->email }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Telefon:</span>
                            <span class="info-value">{{ $admin->phone ?? 'Belirtilmemiş' }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Ülke:</span>
                            <span class="info-value">{{ $admin->country ?? 'Türkiye' }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Şehir:</span>
                            <span class="info-value">{{ $admin->state ?? 'Belirtilmemiş' }}</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">Posta Kodu:</span>
                            <span class="info-value">{{ $admin->postal_code ?? 'Belirtilmemiş' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Recent Activity Card -->
            <div class="info-card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-clock-history"></i>
                        Son Aktiviteler
                    </h3>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">2 saat önce</div>
                                <div class="timeline-text">Yeni ürün eklendi</div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">5 saat önce</div>
                                <div class="timeline-text">Sipariş durumu güncellendi</div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">1 gün önce</div>
                                <div class="timeline-text">Yeni satıcı onaylandı</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Edit Forms -->
        <div class="profile-main">
            <!-- Personal Info Form -->
            <div class="info-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-person-gear"></i>
                        Kişisel Bilgileri Düzenle
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Avatar Upload (Hidden) -->
                        <input type="file" name="avatar" id="avatarFormInput" style="display: none;" accept="image/*">
                        
                        <!-- Basic Info Section -->
                        <div class="form-section">
                            <h4 class="form-section-title">Temel Bilgiler</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $admin->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">E-posta Adresi</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $admin->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Telefon Numarası</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $admin->phone) }}" placeholder="+90 5XX XXX XX XX">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Section -->
                        <div class="form-section">
                            <h4 class="form-section-title">Adres Bilgileri</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Ülke</label>
                                    <select name="country" class="form-control">
                                        <option value="Türkiye" {{ old('country', $admin->country) == 'Türkiye' ? 'selected' : '' }}>Türkiye</option>
                                        <option value="Other" {{ old('country', $admin->country) == 'Other' ? 'selected' : '' }}>Diğer</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Şehir</label>
                                    <input type="text" name="state" class="form-control" 
                                           value="{{ old('state', $admin->state) }}" placeholder="İstanbul">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Posta Kodu</label>
                                    <input type="text" name="postal_code" class="form-control" 
                                           value="{{ old('postal_code', $admin->postal_code) }}" placeholder="34000">
                                </div>
                                
                                <div class="form-group full-width">
                                    <label class="form-label">Tam Adres</label>
                                    <textarea name="address" class="form-control" rows="3" 
                                              placeholder="Mahalle, sokak, bina no...">{{ old('address', $admin->address) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="location.reload()">
                                <i class="bi bi-x-lg me-1"></i>
                                İptal
                            </button>
                            <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                                <i class="bi bi-check-lg me-1"></i>
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Password Change Section -->
            <div class="info-card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-shield-lock"></i>
                        Şifre Değiştir
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="password_update" value="1">
                        
                        <div class="password-section">
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label class="form-label">Mevcut Şifre</label>
                                    <div class="password-toggle">
                                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" 
                                               id="currentPassword">
                                        <button type="button" class="password-toggle-btn" onclick="togglePassword('currentPassword')">
                                            <i class="bi bi-eye" id="currentPasswordIcon"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Yeni Şifre</label>
                                    <div class="password-toggle">
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="newPassword">
                                        <button type="button" class="password-toggle-btn" onclick="togglePassword('newPassword')">
                                            <i class="bi bi-eye" id="newPasswordIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Yeni Şifre (Tekrar)</label>
                                    <div class="password-toggle">
                                        <input type="password" name="password_confirmation" class="form-control" 
                                               id="confirmPassword">
                                        <button type="button" class="password-toggle-btn" onclick="togglePassword('confirmPassword')">
                                            <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info alert-glass mt-3">
                                <i class="bi bi-info-circle"></i>
                                Şifreniz en az 8 karakter uzunluğunda olmalı ve harf ile rakam içermelidir.
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                                <i class="bi bi-shield-check me-1"></i>
                                Şifreyi Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div class="info-card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-gear-wide-connected"></i>
                        Güvenlik Ayarları
                    </h3>
                </div>
                <div class="card-body">
                    <div class="security-item">
                        <div class="security-info">
                            <h4 class="security-title">İki Faktörlü Doğrulama</h4>
                            <p class="security-desc">Hesabınıza ekstra güvenlik katmanı ekleyin</p>
                        </div>
                        <div class="security-action">
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="security-item">
                        <div class="security-info">
                            <h4 class="security-title">Oturum Bildirimleri</h4>
                            <p class="security-desc">Yeni cihazdan giriş yapıldığında e-posta al</p>
                        </div>
                        <div class="security-action">
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="security-item">
                        <div class="security-info">
                            <h4 class="security-title">API Erişimi</h4>
                            <p class="security-desc">Üçüncü parti uygulamaların erişimini yönet</p>
                        </div>
                        <div class="security-action">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-key"></i>
                                API Anahtarları
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + 'Icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Avatar upload preview
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.avatar-img').src = e.target.result;
            // Copy file to form input
            const formInput = document.getElementById('avatarFormInput');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            formInput.files = dataTransfer.files;
            
            // Show info message
            if (typeof AdminPanel !== 'undefined' && AdminPanel.showToast) {
                AdminPanel.showToast('Avatar seçildi. Formu kaydedin!', 'info');
            }
        };
        reader.readAsDataURL(file);
    }
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>
@endpush