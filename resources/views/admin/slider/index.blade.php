@extends('layouts.admin')

@section('title', 'Slider Yönetimi')
@section('header-title', 'Slider Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/slider.css') }}">
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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSliderModal" 
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
        <div class="stat-value">{{ $sliders->count() }}</div>
        <div class="stat-label">Toplam Slider</div>
    </div>
    
    <!-- Active Sliders -->
    <div class="stat-card active">
        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-value">{{ $sliders->where('status', true)->count() }}</div>
        <div class="stat-label">Aktif Slider</div>
    </div>
    
    <!-- Inactive Sliders -->
    <div class="stat-card inactive">
        <div class="stat-icon">
            <i class="bi bi-x-circle"></i>
        </div>
        <div class="stat-value">{{ $sliders->where('status', false)->count() }}</div>
        <div class="stat-label">Pasif Slider</div>
    </div>
    
    <!-- With Images -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-image-fill"></i>
        </div>
        <div class="stat-value">{{ $sliders->whereNotNull('image')->count() }}</div>
        <div class="stat-label">Görselli Slider</div>
    </div>
</div>

<!-- Slider Grid -->
<div class="slider-grid">
    @forelse($sliders as $index => $slider)
        <div class="slider-card" data-tags="{{ strtolower($slider->tag_one . ' ' . $slider->tag_two) }}" 
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
                    <button class="action-btn edit" data-bs-toggle="modal" 
                            data-bs-target="#editSliderModal{{ $slider->id }}">
                        <i class="bi bi-pencil"></i>
                        Düzenle
                    </button>
                    
                    <form action="{{ route('admin.slider.toggle', $slider->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="action-btn toggle">
                            <i class="bi bi-{{ $slider->status ? 'pause' : 'play' }}"></i>
                            {{ $slider->status ? 'Pasifleştir' : 'Aktifleştir' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.slider.delete', $slider->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Bu slider\'ı silmek istediğinizden emin misiniz?')">
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
            <form action="{{ route('admin.slider.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="text" name="tag_one" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Ana başlık metni">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">İkinci Etiket</label>
                                    <input type="text" name="tag_two" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Alt başlık metni">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                            <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="Slider açıklaması..."></textarea>
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
                                <input type="file" name="image" id="sliderImage" accept="image/*" style="display: none;" required>
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
                    
                    <div class="info-message" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px;">
                        <i class="bi bi-info-circle-fill" style="color: #3B82F6; font-size: 20px; flex-shrink: 0;"></i>
                        <div class="info-message-content" style="flex: 1;">
                            <div class="info-message-title" style="font-weight: 600; color: #1f2937; margin-bottom: 2px;">İpucu</div>
                            <div class="info-message-text" style="color: #4b5563; font-size: 14px;">
                                Yüksek kaliteli görseller kullanarak sitenizin görünümünü iyileştirebilirsiniz.
                                Slider'lar ana sayfada gösterilir.
                            </div>
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

<!-- Edit Slider Modals -->
@foreach($sliders as $slider)
<div class="modal fade" id="editSliderModal{{ $slider->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Slider Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.slider.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                                    <input type="text" name="tag_one" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" value="{{ $slider->tag_one }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">İkinci Etiket</label>
                                    <input type="text" name="tag_two" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" value="{{ $slider->tag_two }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                            <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;">{{ $slider->description }}</textarea>
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
                            <div class="image-upload-area {{ $slider->image ? 'has-image' : '' }}" id="imageUploadArea{{ $slider->id }}" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" name="image" id="sliderImage{{ $slider->id }}" accept="image/*" style="display: none;">
                                <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                <div class="upload-text" style="color: #374151; font-weight: 500; margin-bottom: 4px;">Görsel yüklemek için tıklayın veya sürükleyin</div>
                                <div class="upload-hint" style="color: #6b7280; font-size: 13px;">JPG, PNG veya GIF (Maks. 5MB) - Önerilen: 1920x800px</div>
                                <div class="image-preview" style="{{ $slider->image ? 'display: flex;' : 'display: none;' }} position: relative; margin-top: 20px;">
                                    <img id="previewImage{{ $slider->id }}" src="{{ $slider->image ? asset('storage/' . $slider->image) : '' }}" alt="" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                    <button type="button" class="remove-image" onclick="removeEditImage({{ $slider->id }})" style="position: absolute; top: -10px; right: -10px; background: #EF4444; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                            @if($slider->image)
                            <small class="text-muted mt-2 d-block">Mevcut görsel: {{ basename($slider->image) }}</small>
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
                            <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Slider Durumu</label>
                            <select name="status" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                <option value="1" {{ $slider->status ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$slider->status ? 'selected' : '' }}>Pasif</option>
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
document.getElementById('sliderSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const cards = document.querySelectorAll('.slider-card');
        
        cards.forEach(card => {
            const tags = card.dataset.tags;
            const description = card.dataset.description;
            
            if (tags.includes(query) || description.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);
});

// Character counter
function addCharacterCounter(element, maxLength = 255) {
    const container = element.closest('.form-group').querySelector('.char-counter-container');
    if (!container) return;
    
    function updateCounter() {
        const currentLength = element.value.length;
        const remaining = maxLength - currentLength;
        
        let counter = container.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('span');
            counter.className = 'char-counter';
            container.appendChild(counter);
        }
        
        counter.textContent = `${currentLength}/${maxLength} karakter`;
        
        counter.className = 'char-counter';
        if (remaining < 20) {
            counter.classList.add('warning');
        }
        if (remaining < 0) {
            counter.classList.add('danger');
        }
    }
    
    element.addEventListener('input', updateCounter);
    updateCounter();
}

// Image upload for new slider
const imageUploadArea = document.getElementById('imageUploadArea');
const sliderImageInput = document.getElementById('sliderImage');
const previewImage = document.getElementById('previewImage');

imageUploadArea.addEventListener('click', () => {
    sliderImageInput.click();
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

sliderImageInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        handleImageFile(this.files[0]);
    }
});

function handleImageFile(file) {
    if (file.size > 5 * 1024 * 1024) {
        alert('Dosya boyutu 5MB\'dan küçük olmalıdır.');
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
    sliderImageInput.value = '';
    previewImage.src = '';
    imageUploadArea.classList.remove('has-image');
    imageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Image upload for edit modals
@foreach($sliders as $slider)
(function() {
    const editArea = document.getElementById('imageUploadArea{{ $slider->id }}');
    const editInput = document.getElementById('sliderImage{{ $slider->id }}');
    const editPreview = document.getElementById('previewImage{{ $slider->id }}');
    
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

function removeEditImage(sliderId) {
    const editArea = document.getElementById('imageUploadArea' + sliderId);
    const editInput = document.getElementById('sliderImage' + sliderId);
    const editPreview = document.getElementById('previewImage' + sliderId);
    
    editInput.value = '';
    editPreview.src = '';
    editArea.classList.remove('has-image');
    editArea.querySelector('.image-preview').style.display = 'none';
}

// Initialize character counters
document.querySelectorAll('textarea[name="description"]').forEach(textarea => {
    addCharacterCounter(textarea);
});

// Add hover effects to upload areas
document.querySelectorAll('.image-upload-area').forEach(area => {
    area.addEventListener('mouseenter', function() {
        if (!this.classList.contains('has-image')) {
            this.style.background = 'rgba(255, 255, 255, 0.7)';
            this.style.borderColor = 'rgba(169, 0, 0, 0.5)';
        }
    });
    area.addEventListener('mouseleave', function() {
        if (!this.classList.contains('has-image')) {
            this.style.background = 'rgba(255, 255, 255, 0.5)';
            this.style.borderColor = 'rgba(169, 0, 0, 0.3)';
        }
    });
});

// Show success/error messages
@if(session('success'))
    AdminPanel.showToast('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    AdminPanel.showToast('{{ session('error') }}', 'error');
@endif
</script>
@endpush