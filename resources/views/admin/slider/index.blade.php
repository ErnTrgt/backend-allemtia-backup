@extends('layouts.admin')

@section('title', 'Slider Yönetimi')
@section('header-title', 'Slider Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/slider.css') }}">
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

/* Slider Card Animations */
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

.slider-card-new {
    animation: slideIn 0.5s ease;
}

.slider-card-removing {
    animation: fadeOut 0.3s ease;
}

/* Updated Slider Highlight */
.slider-card-updated {
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
            <input type="text" class="search-input" placeholder="Slider ara..." id="sliderSearch">
        </div>
    </div>
    
    <!-- Add New Slider -->
    <button class="btn btn-primary" onclick="showAddSliderModal()" 
            style="background: var(--primary-red); border-color: var(--primary-red);">
        <i class="bi bi-plus-circle"></i>
        Yeni Slider Ekle
    </button>
</div>

<!-- Slider Stats -->
<div class="slider-stats">
    <!-- Total Sliders -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-images"></i>
        </div>
        <div class="stat-value" id="totalSliders">{{ $sliders->count() }}</div>
        <div class="stat-label">Toplam Slider</div>
    </div>
    
    <!-- Active Sliders -->
    <div class="stat-card active">
        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-value" id="activeSliders">{{ $sliders->where('status', true)->count() }}</div>
        <div class="stat-label">Aktif Slider</div>
    </div>
    
    <!-- Inactive Sliders -->
    <div class="stat-card inactive">
        <div class="stat-icon">
            <i class="bi bi-x-circle"></i>
        </div>
        <div class="stat-value" id="inactiveSliders">{{ $sliders->where('status', false)->count() }}</div>
        <div class="stat-label">Pasif Slider</div>
    </div>
    
    <!-- With Images -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-image-fill"></i>
        </div>
        <div class="stat-value" id="withImages">{{ $sliders->whereNotNull('image')->count() }}</div>
        <div class="stat-label">Görselli Slider</div>
    </div>
</div>

<!-- Slider Grid -->
<div class="slider-grid">
    @forelse($sliders as $index => $slider)
        <div class="slider-card" data-slider-id="{{ $slider->id }}" 
             data-tags="{{ strtolower($slider->tag_one . ' ' . $slider->tag_two) }}" 
             data-description="{{ strtolower($slider->description) }}">
            <!-- Slider Image -->
            <div class="slider-image">
                @if($slider->image)
                    <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->tag_one }}">
                @else
                    <div class="slider-image-placeholder">
                        <i class="bi bi-image"></i>
                    </div>
                @endif
                
                <!-- Order Badge -->
                <span class="slider-order">{{ $index + 1 }}</span>
                
                <!-- Status Badge -->
                <span class="slider-status {{ $slider->status ? 'active' : 'inactive' }}">
                    <i class="bi bi-{{ $slider->status ? 'check' : 'x' }}-circle"></i>
                    {{ $slider->status ? 'Aktif' : 'Pasif' }}
                </span>
            </div>
            
            <!-- Slider Content -->
            <div class="slider-content">
                <!-- Tags -->
                <div class="slider-tags">
                    @if($slider->tag_one)
                        <div class="slider-tag">
                            <span class="tag-label">Etiket 1</span>
                            <span class="tag-text">{{ $slider->tag_one }}</span>
                        </div>
                    @endif
                    
                    @if($slider->tag_two)
                        <div class="slider-tag">
                            <span class="tag-label">Etiket 2</span>
                            <span class="tag-text">{{ $slider->tag_two }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Description -->
                <p class="slider-description">
                    {{ $slider->description ?: 'Açıklama bulunmuyor...' }}
                </p>
                
                <!-- Actions -->
                <div class="slider-actions">
                    <button class="action-btn edit" onclick="editSlider({{ $slider->id }})">
                        <i class="bi bi-pencil"></i>
                        Düzenle
                    </button>
                    
                    <button class="action-btn toggle" onclick="toggleSliderStatus({{ $slider->id }})">
                        <i class="bi bi-{{ $slider->status ? 'pause' : 'play' }}"></i>
                        {{ $slider->status ? 'Pasifleştir' : 'Aktifleştir' }}
                    </button>
                    
                    <button class="action-btn delete" onclick="deleteSlider({{ $slider->id }})">
                        <i class="bi bi-trash"></i>
                        Sil
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-images empty-icon"></i>
            <h3 class="empty-title">Henüz Slider Yok</h3>
            <p class="empty-text">İlk slider'ınızı oluşturmak için yukarıdaki butonu kullanın.</p>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($sliders instanceof \Illuminate\Pagination\LengthAwarePaginator && $sliders->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $sliders->links('components.admin-pagination') }}
</div>
@endif

<!-- Add Slider Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-plus-circle me-2" style="color: #A90000;"></i>
                    Yeni Slider Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form id="addSliderForm" method="POST" enctype="multipart/form-data" onsubmit="handleSliderAdd(event)">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <!-- Text Content -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-text-left" style="color: #A90000;"></i>
                            Slider Metinleri
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Birinci Etiket</label>
                                    <input type="text" name="tag_one" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" 
                                           placeholder="Ana başlık metni">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">İkinci Etiket</label>
                                    <input type="text" name="tag_two" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" 
                                           placeholder="Alt başlık metni">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                            <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" 
                                      placeholder="Slider açıklaması..."></textarea>
                            <div class="char-counter-container"></div>
                        </div>
                    </div>
                    
                    <!-- Image -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-image" style="color: #A90000;"></i>
                            Slider Görseli
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Görsel</label>
                            <div class="image-upload-area" id="imageUploadArea" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" name="image" id="sliderImage" accept="image/*" style="display: none;">
                                <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                <div class="upload-text" style="color: #374151; font-weight: 500; margin-bottom: 4px;">Görsel yüklemek için tıklayın veya sürükleyin</div>
                                <div class="upload-hint" style="color: #6b7280; font-size: 13px;">JPG, PNG veya GIF (Maks. 5MB) - Önerilen: 1920x800px</div>
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
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Slider Durumu</label>
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
                        Slider Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Slider Modal (Dynamic) -->
<div class="modal fade" id="editSliderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Slider Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form id="editSliderForm" method="POST" enctype="multipart/form-data" onsubmit="handleSliderEdit(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="editSliderId" name="slider_id">
                <div class="modal-body" style="padding: 24px;">
                    <!-- Text Content -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-text-left" style="color: #A90000;"></i>
                            Slider Metinleri
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Birinci Etiket</label>
                                    <input type="text" name="tag_one" id="editTagOne" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">İkinci Etiket</label>
                                    <input type="text" name="tag_two" id="editTagTwo" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
                            <div class="char-counter-container"></div>
                        </div>
                    </div>
                    
                    <!-- Image -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-image" style="color: #A90000;"></i>
                            Slider Görseli
                        </h6>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Görsel</label>
                            <div class="image-upload-area" id="editImageUploadArea" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" name="image" id="editSliderImage" accept="image/*" style="display: none;">
                                <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                <div class="upload-text" style="color: #374151; font-weight: 500; margin-bottom: 4px;">Görsel yüklemek için tıklayın veya sürükleyin</div>
                                <div class="upload-hint" style="color: #6b7280; font-size: 13px;">JPG, PNG veya GIF (Maks. 5MB) - Önerilen: 1920x800px</div>
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
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Slider Durumu</label>
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
<script src="{{ asset('admin/js/slider-dynamic.js') }}"></script>
@endpush