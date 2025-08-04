@extends('layouts.admin')

@section('title', 'Hakkımızda Yönetimi')
@section('header-title', 'Hakkımızda Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/about.css') }}">
@endpush

@section('content')
<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Bölüm ara..." id="sectionSearch">
        </div>
    </div>
    
    <!-- Add New Section -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal" 
            style="background: var(--primary-red); border-color: var(--primary-red);">
        <i class="bi bi-plus-circle"></i>
        Yeni Bölüm Ekle
    </button>
</div>

<!-- About Stats -->
<div class="about-stats">
    <!-- Total Sections -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-collection"></i>
        </div>
        <div class="stat-value">{{ $sections->total() }}</div>
        <div class="stat-label">Toplam Bölüm</div>
    </div>
    
    <!-- Active Sections -->
    <div class="stat-card active">
        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-value">{{ $sections->filter(function($s) { return $s->status == true; })->count() }}</div>
        <div class="stat-label">Aktif Bölüm</div>
    </div>
    
    <!-- Inactive Sections -->
    <div class="stat-card inactive">
        <div class="stat-icon">
            <i class="bi bi-x-circle"></i>
        </div>
        <div class="stat-value">{{ $sections->filter(function($s) { return $s->status == false; })->count() }}</div>
        <div class="stat-label">Pasif Bölüm</div>
    </div>
    
    <!-- With Images -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-image"></i>
        </div>
        <div class="stat-value">{{ $sections->filter(function($s) { return $s->image != null; })->count() }}</div>
        <div class="stat-label">Görselli Bölüm</div>
    </div>
</div>

<!-- Sections Grid -->
<div class="sections-grid">
    @forelse($sections as $section)
        <div class="section-card" data-key="{{ strtolower($section->section_key) }}" 
             data-title="{{ strtolower($section->title) }}">
            <!-- Section Header -->
            <div class="section-header">
                <span class="section-key">
                    <i class="bi bi-key"></i>
                    {{ $section->section_key }}
                </span>
                <h3 class="section-title">{{ $section->title ?: 'Başlıksız Bölüm' }}</h3>
            </div>
            
            <!-- Section Image -->
            @if($section->image)
                <div class="section-image">
                    <img src="{{ asset('storage/' . $section->image) }}" alt="{{ $section->title }}">
                    <span class="section-status {{ $section->status ? 'active' : 'inactive' }}">
                        <i class="bi bi-{{ $section->status ? 'check' : 'x' }}-circle"></i>
                        {{ $section->status ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>
            @endif
            
            <!-- Section Content -->
            <div class="section-content">
                <p class="section-text">
                    {{ Str::limit(strip_tags($section->content), 200) ?: 'İçerik bulunmuyor...' }}
                </p>
                
                <!-- Actions -->
                <div class="section-actions">
                    <button class="action-btn edit" data-bs-toggle="modal" 
                            data-bs-target="#editSectionModal{{ $section->id }}">
                        <i class="bi bi-pencil"></i>
                        Düzenle
                    </button>
                    
                    <form action="{{ route('admin.about.toggleStatus', $section->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="action-btn toggle">
                            <i class="bi bi-{{ $section->status ? 'pause' : 'play' }}"></i>
                            {{ $section->status ? 'Pasifleştir' : 'Aktifleştir' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.about.delete', $section->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Bu bölümü silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete">
                            <i class="bi bi-trash"></i>
                            Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-info-square empty-icon"></i>
            <h3 class="empty-title">Henüz Bölüm Yok</h3>
            <p class="empty-text">İlk bölümünüzü oluşturmak için yukarıdaki butonu kullanın.</p>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($sections instanceof \Illuminate\Pagination\LengthAwarePaginator && $sections->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $sections->links('components.admin-pagination') }}
</div>
@endif

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-plus-circle me-2" style="color: #A90000;"></i>
                    Yeni Bölüm Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.about.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <!-- Basic Info -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-info-circle" style="color: #A90000;"></i>
                            Bölüm Bilgileri
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">
                                Bölüm Anahtarı
                                <small class="text-muted">(Örn: heading, area, features)</small>
                            </label>
                            <input type="text" name="section_key" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Bölüm için benzersiz anahtar">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Başlık</label>
                            <input type="text" name="title" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Bölüm başlığı (opsiyonel)">
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-text-paragraph" style="color: #A90000;"></i>
                            İçerik
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Bölüm İçeriği</label>
                            <textarea name="content" class="form-control" rows="5" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="Bölüm içeriğini girin..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Image -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-image" style="color: #A90000;"></i>
                            Görsel
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Bölüm Görseli</label>
                            <div class="image-upload-area" id="imageUploadArea" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" name="image" id="sectionImage" accept="image/*" style="display: none;">
                                <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                <div class="upload-text" style="color: #374151; font-weight: 500; margin-bottom: 4px;">Görsel yüklemek için tıklayın veya sürükleyin</div>
                                <div class="upload-hint" style="color: #6b7280; font-size: 13px;">JPG, PNG veya GIF (Maks. 2MB)</div>
                                <div class="image-preview" style="display: none; position: relative; margin-top: 20px;">
                                    <img id="previewImage" src="" alt="" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                    <button type="button" class="remove-image" onclick="removeImage()" style="position: absolute; top: -10px; right: -10px; background: #EF4444; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-message" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px;">
                        <i class="bi bi-info-circle-fill" style="color: #3B82F6; font-size: 20px; flex-shrink: 0;"></i>
                        <div class="info-message-content" style="flex: 1;">
                            <div class="info-message-title" style="font-weight: 600; color: #1f2937; margin-bottom: 2px;">İpucu</div>
                            <div class="info-message-text" style="color: #4b5563; font-size: 14px;">
                                Bölüm anahtarı, bu içeriği web sitesinin belirli bir alanında göstermek için kullanılır.
                                Benzersiz ve anlamlı bir anahtar seçin.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-lg me-1"></i>
                        Bölüm Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modals -->
@foreach($sections as $section)
<div class="modal fade" id="editSectionModal{{ $section->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Bölüm Düzenle: <span class="badge bg-light text-dark" style="margin-left: 8px; font-size: 14px; font-weight: normal;">{{ $section->section_key }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.about.update', $section->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <!-- Basic Info -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-info-circle" style="color: #A90000;"></i>
                            Bölüm Bilgileri
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">
                                Bölüm Anahtarı
                                <small class="text-muted">(Değiştirmeden önce dikkatli olun)</small>
                            </label>
                            <input type="text" name="section_key" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" value="{{ $section->section_key }}">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Başlık</label>
                            <input type="text" name="title" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" value="{{ $section->title }}">
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-text-paragraph" style="color: #A90000;"></i>
                            İçerik
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Bölüm İçeriği</label>
                            <textarea name="content" class="form-control" rows="5" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;">{{ $section->content }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Image -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-image" style="color: #A90000;"></i>
                            Görsel
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Bölüm Görseli</label>
                            <div class="image-upload-area {{ $section->image ? 'has-image' : '' }}" id="imageUploadArea{{ $section->id }}" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" name="image" id="sectionImage{{ $section->id }}" accept="image/*" style="display: none;">
                                <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                <div class="upload-text" style="color: #374151; font-weight: 500; margin-bottom: 4px;">Görsel yüklemek için tıklayın veya sürükleyin</div>
                                <div class="upload-hint" style="color: #6b7280; font-size: 13px;">JPG, PNG veya GIF (Maks. 2MB)</div>
                                <div class="image-preview" style="{{ $section->image ? 'display: flex;' : 'display: none;' }} position: relative; margin-top: 20px;">
                                    <img id="previewImage{{ $section->id }}" src="{{ $section->image ? asset('storage/' . $section->image) : '' }}" alt="" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                    <button type="button" class="remove-image" onclick="removeEditImage({{ $section->id }})" style="position: absolute; top: -10px; right: -10px; background: #EF4444; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                            @if($section->image)
                            <small class="text-muted mt-2 d-block">Mevcut görsel: {{ basename($section->image) }}</small>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 0; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-toggle-on" style="color: #A90000;"></i>
                            Durum
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Bölüm Durumu</label>
                            <select name="status" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                <option value="1" {{ $section->status ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$section->status ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-lg me-1"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
// Search functionality
let searchTimer;
document.getElementById('sectionSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const cards = document.querySelectorAll('.section-card');
        
        cards.forEach(card => {
            const key = card.dataset.key;
            const title = card.dataset.title;
            
            if (key.includes(query) || title.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);
});

// Image upload for new section
const imageUploadArea = document.getElementById('imageUploadArea');
const sectionImageInput = document.getElementById('sectionImage');
const previewImage = document.getElementById('previewImage');

imageUploadArea.addEventListener('click', () => {
    sectionImageInput.click();
});

imageUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    imageUploadArea.classList.add('dragover');
});

imageUploadArea.addEventListener('dragleave', () => {
    imageUploadArea.classList.remove('dragover');
});

imageUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    imageUploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleImageFile(files[0]);
    }
});

sectionImageInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        handleImageFile(this.files[0]);
    }
});

function handleImageFile(file) {
    if (file.size > 2 * 1024 * 1024) {
        alert('Dosya boyutu 2MB\'dan küçük olmalıdır.');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        imageUploadArea.classList.add('has-image');
        imageUploadArea.querySelector('.image-preview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    sectionImageInput.value = '';
    previewImage.src = '';
    imageUploadArea.classList.remove('has-image');
    imageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Image upload for edit modals
@foreach($sections as $section)
(function() {
    const editArea = document.getElementById('imageUploadArea{{ $section->id }}');
    const editInput = document.getElementById('sectionImage{{ $section->id }}');
    const editPreview = document.getElementById('previewImage{{ $section->id }}');
    
    editArea.addEventListener('click', () => {
        editInput.click();
    });
    
    editInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                editPreview.src = e.target.result;
                editArea.classList.add('has-image');
                editArea.querySelector('.image-preview').style.display = 'flex';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
})();
@endforeach

function removeEditImage(sectionId) {
    const editArea = document.getElementById('imageUploadArea' + sectionId);
    const editInput = document.getElementById('sectionImage' + sectionId);
    const editPreview = document.getElementById('previewImage' + sectionId);
    
    editInput.value = '';
    editPreview.src = '';
    editArea.classList.remove('has-image');
    editArea.querySelector('.image-preview').style.display = 'none';
}

// Show success/error messages
@if(session('success'))
    AdminPanel.showToast('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    AdminPanel.showToast('{{ session('error') }}', 'error');
@endif
</script>
@endpush