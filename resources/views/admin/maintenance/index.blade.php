@extends('layouts.admin-modern')

@section('title', 'Bakım Modu Yönetimi')

@section('content')
<div class="maintenance-management-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">Bakım Modu Yönetimi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bakım Modu</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- Maintenance Status Card -->
        <div class="col-lg-4 mb-4">
            <div class="glass-card maintenance-status-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-tools me-2"></i>Bakım Durumu
                    </h5>
                    
                    <div class="maintenance-toggle text-center mb-4">
                        <div class="form-check form-switch form-switch-lg d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="maintenanceSwitch" 
                                {{ $maintenance && $maintenance->is_active ? 'checked' : '' }}>
                            <label class="form-check-label ms-3" for="maintenanceSwitch">
                                <span class="status-text">
                                    {{ $maintenance && $maintenance->is_active ? 'Bakım Modu Aktif' : 'Bakım Modu Kapalı' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    @if($maintenance && $maintenance->is_active)
                        <div class="maintenance-info">
                            <div class="info-item mb-3">
                                <small class="text-muted">Başlatıldı:</small>
                                <p class="mb-0">{{ $maintenance->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            @if($maintenance->estimated_end_time)
                                <div class="info-item mb-3">
                                    <small class="text-muted">Tahmini Bitiş:</small>
                                    <p class="mb-0">{{ $maintenance->estimated_end_time->format('d.m.Y H:i') }}</p>
                                </div>
                            @endif
                            <div class="info-item">
                                <small class="text-muted">Başlatan:</small>
                                <p class="mb-0">{{ $maintenance->creator->name ?? 'Sistem' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="glass-card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-graph-up me-2"></i>İstatistikler
                    </h5>
                    <div class="quick-stats">
                        <div class="stat-item d-flex justify-content-between mb-2">
                            <span>İzinli IP Sayısı:</span>
                            <strong>{{ $maintenance ? count($maintenance->allowed_ips ?? []) : 0 }}</strong>
                        </div>
                        <div class="stat-item d-flex justify-content-between">
                            <span>Bildirim E-postaları:</span>
                            <strong>{{ $maintenance ? count($maintenance->notify_emails ?? []) : 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Settings Form -->
        <div class="col-lg-8 mb-4">
            <div class="glass-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-gear me-2"></i>Bakım Ayarları
                    </h5>

                    <form id="maintenanceForm" action="{{ route('admin.maintenance.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Başlık</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                value="{{ $maintenance->title ?? 'Site Bakımda' }}" required>
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Mesaj</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required>{{ $maintenance->message ?? 'Sitemiz şu anda bakım çalışması nedeniyle geçici olarak hizmet verememektedir. En kısa sürede geri döneceğiz.' }}</textarea>
                        </div>

                        <!-- Template Type -->
                        <div class="mb-3">
                            <label for="template_type" class="form-label">Şablon Tipi</label>
                            <select class="form-select" id="template_type" name="template_type">
                                <option value="simple" {{ ($maintenance->template_type ?? 'simple') == 'simple' ? 'selected' : '' }}>
                                    Basit
                                </option>
                                <option value="detailed" {{ ($maintenance->template_type ?? '') == 'detailed' ? 'selected' : '' }}>
                                    Detaylı
                                </option>
                                <option value="custom" {{ ($maintenance->template_type ?? '') == 'custom' ? 'selected' : '' }}>
                                    Özel
                                </option>
                            </select>
                        </div>

                        <!-- Estimated End Time -->
                        <div class="mb-3">
                            <label for="estimated_end_time" class="form-label">Tahmini Bitiş Zamanı (Opsiyonel)</label>
                            <input type="datetime-local" class="form-control" id="estimated_end_time" 
                                name="estimated_end_time" 
                                value="{{ $maintenance && $maintenance->estimated_end_time ? $maintenance->estimated_end_time->format('Y-m-d\TH:i') : '' }}">
                        </div>

                        <!-- Allowed IPs -->
                        <div class="mb-3">
                            <label for="allowed_ips" class="form-label">İzinli IP Adresleri</label>
                            <div id="ipContainer">
                                @if($maintenance && $maintenance->allowed_ips)
                                    @foreach($maintenance->allowed_ips as $ip)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="allowed_ips[]" 
                                                value="{{ $ip }}" placeholder="192.168.1.1">
                                            <button class="btn btn-outline-danger" type="button" onclick="removeField(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addIpField()">
                                <i class="bi bi-plus"></i> IP Ekle
                            </button>
                        </div>

                        <!-- Notify Emails -->
                        <div class="mb-4">
                            <label for="notify_emails" class="form-label">Bildirim E-postaları</label>
                            <div id="emailContainer">
                                @if($maintenance && $maintenance->notify_emails)
                                    @foreach($maintenance->notify_emails as $email)
                                        <div class="input-group mb-2">
                                            <input type="email" class="form-control" name="notify_emails[]" 
                                                value="{{ $email }}" placeholder="email@example.com">
                                            <button class="btn btn-outline-danger" type="button" onclick="removeField(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEmailField()">
                                <i class="bi bi-plus"></i> E-posta Ekle
                            </button>
                        </div>

                        <!-- Save Button -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" {{ !$maintenance || !$maintenance->is_active ? 'disabled' : '' }}>
                                <i class="bi bi-save me-2"></i>Ayarları Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.maintenance-management-container {
    max-width: 1200px;
    margin: 0 auto;
}

.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
}

.maintenance-status-card {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
}

.form-switch-lg .form-check-input {
    width: 3em;
    height: 1.5em;
}

.status-text {
    font-weight: 600;
    font-size: 1.1rem;
}

.maintenance-info {
    background: rgba(255, 255, 255, 0.5);
    padding: 1rem;
    border-radius: var(--radius-md);
}

.info-item small {
    font-size: 0.875rem;
}

.quick-stats {
    background: rgba(255, 255, 255, 0.5);
    padding: 1rem;
    border-radius: var(--radius-md);
}

.stat-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.stat-item:last-child {
    border-bottom: none;
}
</style>

<script>
// Toggle maintenance mode
document.getElementById('maintenanceSwitch').addEventListener('change', function() {
    const isActive = this.checked;
    const statusText = document.querySelector('.status-text');
    const saveButton = document.querySelector('button[type="submit"]');
    
    // Update status text
    statusText.textContent = isActive ? 'İşleniyor...' : 'İşleniyor...';
    this.disabled = true;
    
    // Send request
    fetch('{{ route("admin.maintenance.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            is_active: isActive
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusText.textContent = isActive ? 'Bakım Modu Aktif' : 'Bakım Modu Kapalı';
            saveButton.disabled = !isActive;
            
            if (!isActive) {
                // Clear form if deactivating
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            alert(data.message || 'Bir hata oluştu');
            this.checked = !isActive;
            statusText.textContent = !isActive ? 'Bakım Modu Aktif' : 'Bakım Modu Kapalı';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
        this.checked = !isActive;
        statusText.textContent = !isActive ? 'Bakım Modu Aktif' : 'Bakım Modu Kapalı';
    })
    .finally(() => {
        this.disabled = false;
    });
});

// Add IP field
function addIpField() {
    const container = document.getElementById('ipContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="allowed_ips[]" placeholder="192.168.1.1">
        <button class="btn btn-outline-danger" type="button" onclick="removeField(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Add Email field
function addEmailField() {
    const container = document.getElementById('emailContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="email" class="form-control" name="notify_emails[]" placeholder="email@example.com">
        <button class="btn btn-outline-danger" type="button" onclick="removeField(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Remove field
function removeField(button) {
    button.closest('.input-group').remove();
}
</script>
@endsection