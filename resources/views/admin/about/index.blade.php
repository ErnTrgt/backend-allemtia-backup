@extends('layouts.admin')

@section('title', 'Hakkımızda Yönetimi')
@section('header-title', 'Hakkımızda Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/about.css') }}">
<style>
/* Loading Toast */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}

/* Toast Container */
.toast-container {
    z-index: 9999;
}

/* Section Card Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: scale(1);
    }
    to {
        opacity: 0;
        transform: scale(0.9);
    }
}

.section-card-new {
    animation: slideIn 0.5s ease;
}

.section-card-removing {
    animation: fadeOut 0.3s ease;
}

/* Updated Section Highlight */
.section-card-updated {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
    transition: box-shadow 1s ease;
}

/* Dragover Effect */
.image-upload-area.dragover {
    background: rgba(169, 0, 0, 0.1) !important;
    border-color: rgba(169, 0, 0, 0.6) !important;
}
</style>
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
    <button class="btn btn-primary" onclick="showAddSectionModal()" 
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
        <div class="stat-value" id="totalSections">{{ $sections->total() }}</div>
        <div class="stat-label">Toplam Bölüm</div>
    </div>
    
    <!-- Active Sections -->
    <div class="stat-card active">
        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-value" id="activeSections">{{ $sections->filter(function($s) { return $s->status == true; })->count() }}</div>
        <div class="stat-label">Aktif Bölüm</div>
    </div>
    
    <!-- Inactive Sections -->
    <div class="stat-card inactive">
        <div class="stat-icon">
            <i class="bi bi-x-circle"></i>
        </div>
        <div class="stat-value" id="inactiveSections">{{ $sections->filter(function($s) { return $s->status == false; })->count() }}</div>
        <div class="stat-label">Pasif Bölüm</div>
    </div>
    
    <!-- With Images -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-image"></i>
        </div>
        <div class="stat-value" id="withImages">{{ $sections->filter(function($s) { return $s->image != null; })->count() }}</div>
        <div class="stat-label">Görselli Bölüm</div>
    </div>
</div>

<!-- Sections Grid -->
<div class="sections-grid">
    @forelse($sections as $section)
        <div class="section-card" data-section-id="{{ $section->id }}" data-key="{{ strtolower($section->section_key) }}" 
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
                    <button class="action-btn edit" onclick="editSection({{ $section->id }})">
                        <i class="bi bi-pencil"></i>
                        Düzenle
                    </button>
                    
                    <button class="action-btn toggle" onclick="toggleSectionStatus({{ $section->id }})">
                        <i class="bi bi-{{ $section->status ? 'pause' : 'play' }}"></i>
                        {{ $section->status ? 'Pasifleştir' : 'Aktifleştir' }}
                    </button>
                    
                    <button class="action-btn delete" onclick="deleteSection({{ $section->id }})">
                        <i class="bi bi-trash"></i>
                        Sil
                    </button>
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
            <form id="addSectionForm" method="POST" enctype="multipart/form-data" onsubmit="handleSectionAdd(event)">
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
                    
                    <!-- Status -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 0; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-toggle-on" style="color: #A90000;"></i>
                            Durum
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Bölüm Durumu</label>
                            <select name="status" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Pasif</option>
                            </select>
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

<!-- Edit Section Modal (Dynamic) -->
<div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Bölüm Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form id="editSectionForm" method="POST" enctype="multipart/form-data" onsubmit="handleSectionEdit(event)">
                @csrf
                <input type="hidden" id="editSectionId" name="section_id">
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
                            <input type="text" name="section_key" id="editSectionKey" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Başlık</label>
                            <input type="text" name="title" id="editTitle" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
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
                            <textarea name="content" id="editContent" class="form-control" rows="5" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
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
                            <div class="image-upload-area" id="editImageUploadArea" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" name="image" id="editSectionImage" accept="image/*" style="display: none;">
                                <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                <div class="upload-text" style="color: #374151; font-weight: 500; margin-bottom: 4px;">Görsel yüklemek için tıklayın veya sürükleyin</div>
                                <div class="upload-hint" style="color: #6b7280; font-size: 13px;">JPG, PNG veya GIF (Maks. 2MB)</div>
                                <div class="image-preview" style="display: none; position: relative; margin-top: 20px;">
                                    <img id="editPreviewImage" src="" alt="" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                    <button type="button" class="remove-image" onclick="removeEditImage()" style="position: absolute; top: -10px; right: -10px; background: #EF4444; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
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
                            <select name="status" id="editStatus" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                <option value="1">Aktif</option>
                                <option value="0">Pasif</option>
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

<!-- Meta tag for CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
<script src="{{ asset('admin/js/about-dynamic.js') }}"></script>
@endpush