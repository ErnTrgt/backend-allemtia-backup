@extends('layouts.layout')

@section('title', 'Slider Yönetimi')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Slider Yönetimi</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Slider'lar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addSliderModal">
                        + Yeni Slider Ekle
                    </button>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Slider Listesi</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Etiket 1</th>
                            <th>Etiket 2</th>
                            <th>Açıklama</th>
                            <th>Resim</th>
                            <th>Durum</th>
                            <th class="datatable-nosort">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sliders as $slider)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $slider->tag_one }}</td>
                            <td>{{ $slider->tag_two }}</td>
                            <td>{{ Str::limit(strip_tags($slider->description), 50) }}</td>
                            <td>
                                @if($slider->image)
                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="slider image" width="80">
                                @else
                                    <span>Resim Yok</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $slider->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $slider->status ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                        href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" data-toggle="modal"
                                            data-target="#editSliderModal{{ $slider->id }}" href="#">
                                            <i class="dw dw-edit2"></i> Düzenle
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('toggle-slider-{{ $slider->id }}').submit();">
                                            <i class="dw {{ $slider->status ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $slider->status ? 'Pasifleştir' : 'Aktifleştir' }}
                                        </a>
                                        <form id="toggle-slider-{{ $slider->id }}"
                                            action="{{ route('admin.slider.toggle', $slider->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="event.preventDefault(); document.getElementById('delete-slider-{{ $slider->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Sil
                                        </a>
                                        <form id="delete-slider-{{ $slider->id }}"
                                            action="{{ route('admin.slider.delete', $slider->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editSliderModal{{ $slider->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="editSliderModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-gradient-primary text-white border-0">
                                        <h4 class="modal-title font-weight-bold">
                                            <i class="dw dw-image mr-2"></i>Slider Düzenle
                                            <small class="ml-2 opacity-75">#{{ $slider->id }}</small>
                                        </h4>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.slider.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <!-- Metin İçerikleri -->
                                            <div class="form-section mb-4">
                                                <h6 class="text-primary font-weight-bold mb-3">
                                                    <i class="dw dw-text mr-2"></i>Slider Metinleri
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-tag mr-1 text-info"></i>Birinci Etiket
                                                            </label>
                                                            <input type="text" name="tag_one" class="form-control form-control-lg border-2" 
                                                                   value="{{ $slider->tag_one }}" placeholder="Ana başlık metni">
                                                            <small class="text-muted">Slider'ın ana başlık metni</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-tag mr-1 text-warning"></i>İkinci Etiket
                                                            </label>
                                                            <input type="text" name="tag_two" class="form-control form-control-lg border-2" 
                                                                   value="{{ $slider->tag_two }}" placeholder="Alt başlık metni">
                                                            <small class="text-muted">Slider'ın alt başlık metni</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-semibold text-dark">
                                                        <i class="dw dw-text-width mr-1 text-success"></i>Açıklama
                                                    </label>
                                                    <textarea name="description" rows="4" class="form-control form-control-lg border-2" 
                                                              placeholder="Slider açıklaması...">{{ $slider->description }}</textarea>
                                                    <small class="text-muted">Slider için detaylı açıklama metni</small>
                                                </div>
                                            </div>

                                            <!-- Görsel İçerik -->
                                            <div class="form-section">
                                                <h6 class="text-success font-weight-bold mb-3">
                                                    <i class="dw dw-image mr-2"></i>Slider Görseli
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-upload mr-1 text-primary"></i>Yeni Görsel Seç
                                                            </label>
                                                            <div class="custom-file">
                                                                <input type="file" name="image" class="custom-file-input border-2" 
                                                                       id="sliderImage{{ $slider->id }}" accept="image/*">
                                                                <label class="custom-file-label form-control-lg" 
                                                                       for="sliderImage{{ $slider->id }}">Görsel dosyası seçin</label>
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="dw dw-info mr-1"></i>
                                                                Önerilen boyut: 1920x800px. Maksimum dosya boyutu: 5MB
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @if($slider->image)
                                                            <div class="current-image">
                                                                <label class="font-weight-semibold text-dark d-block">
                                                                    <i class="dw dw-eye mr-1 text-info"></i>Mevcut Görsel
                                                                </label>
                                                                <div class="image-preview-container">
                                                                    <img src="{{ asset('storage/' . $slider->image) }}" 
                                                                         alt="Mevcut slider görseli" 
                                                                         class="img-fluid rounded border shadow-sm"
                                                                         style="max-height: 120px; object-fit: cover;">
                                                                </div>
                                                                <small class="text-muted d-block mt-1">
                                                                    Yeni görsel seçmezseniz mevcut görsel korunur
                                                                </small>
                                                            </div>
                                                        @else
                                                            <div class="no-image-placeholder">
                                                                <div class="text-center p-3 border rounded bg-light">
                                                                    <i class="dw dw-image text-muted" style="font-size: 32px;"></i>
                                                                    <p class="text-muted mb-0 mt-2">Mevcut görsel yok</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Önizleme Alanı -->
                                            <div class="preview-section mt-4">
                                                <div class="alert alert-info border-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="dw dw-lightbulb text-info mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <strong>İpucu:</strong> 
                                                            Değişiklikleri kaydetmeden önce tüm alanları kontrol edin. 
                                                            Görsel yüklemesi biraz zaman alabilir.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0 justify-content-between">
                                            <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                                                <i class="dw dw-cancel mr-2"></i>İptal
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                                <i class="dw dw-save mr-2"></i>Değişiklikleri Kaydet
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Modal End -->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1" role="dialog" aria-labelledby="addSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white border-0">
                <h4 class="modal-title font-weight-bold">
                    <i class="dw dw-add mr-2"></i>Yeni Slider Oluştur
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.slider.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Metin İçerikleri -->
                    <div class="form-section mb-4">
                        <h6 class="text-primary font-weight-bold mb-3">
                            <i class="dw dw-text mr-2"></i>Slider Metinleri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-tag mr-1 text-info"></i>Birinci Etiket
                                    </label>
                                    <input type="text" name="tag_one" class="form-control form-control-lg border-2" 
                                           placeholder="Ana başlık metni">
                                    <small class="text-muted">Slider'ın ana başlık metni</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-tag mr-1 text-warning"></i>İkinci Etiket
                                    </label>
                                    <input type="text" name="tag_two" class="form-control form-control-lg border-2" 
                                           placeholder="Alt başlık metni">
                                    <small class="text-muted">Slider'ın alt başlık metni</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-text-width mr-1 text-success"></i>Açıklama
                            </label>
                            <textarea name="description" rows="4" class="form-control form-control-lg border-2" 
                                      placeholder="Slider açıklaması..."></textarea>
                            <small class="text-muted">Slider için detaylı açıklama metni</small>
                        </div>
                    </div>

                    <!-- Görsel İçerik -->
                    <div class="form-section">
                        <h6 class="text-success font-weight-bold mb-3">
                            <i class="dw dw-image mr-2"></i>Slider Görseli
                        </h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-upload mr-1 text-primary"></i>Görsel Seç
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" name="image" class="custom-file-input border-2" 
                                               id="newSliderImage" accept="image/*" required>
                                        <label class="custom-file-label form-control-lg" 
                                               for="newSliderImage">Görsel dosyası seçin</label>
                                    </div>
                                    <small class="text-muted">
                                        <i class="dw dw-info mr-1"></i>
                                        Önerilen boyut: 1920x800px. Maksimum dosya boyutu: 5MB
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="upload-preview">
                                    <label class="font-weight-semibold text-dark d-block">
                                        <i class="dw dw-eye mr-1 text-info"></i>Görsel Önizleme
                                    </label>
                                    <div id="imagePreview" class="image-preview-container">
                                        <div class="text-center p-4 border rounded bg-light">
                                            <i class="dw dw-cloud-upload text-muted" style="font-size: 48px;"></i>
                                            <p class="text-muted mb-0 mt-2">Görsel seçildikten sonra<br>önizleme burada görünür</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bilgilendirme -->
                    <div class="alert alert-success border-0 mt-4">
                        <div class="d-flex align-items-center">
                            <i class="dw dw-lightbulb text-success mr-2" style="font-size: 18px;"></i>
                            <div>
                                <strong>İpucu:</strong> 
                                Slider oluşturduktan sonra durumunu "Aktif" yapmayı unutmayın. 
                                Yüksek kaliteli görseller kullanmak sitenizin görünümünü iyileştirir.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 justify-content-between">
                    <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                        <i class="dw dw-cancel mr-2"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="dw dw-add mr-2"></i>Slider Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
/* Modal Geliştirmeleri */
.modal-xl {
    max-width: 1000px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

.form-control-lg {
    height: calc(2.5rem + 2px);
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.border-2 {
    border-width: 2px !important;
    transition: all 0.3s ease;
}

.border-2:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.font-weight-semibold {
    font-weight: 600;
}

.alert {
    border-radius: 10px;
}

/* Custom File Input */
.custom-file-label {
    border-width: 2px;
    transition: all 0.3s ease;
}

.custom-file-input:focus ~ .custom-file-label {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.custom-file-label::after {
    background: #007bff;
    color: white;
    border-color: #007bff;
    font-weight: 600;
}

/* Image Preview */
.image-preview-container {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-preview-container img {
    max-width: 100%;
    max-height: 120px;
    object-fit: cover;
    border-radius: 8px;
}

.no-image-placeholder,
.upload-preview {
    height: 100%;
}

.current-image {
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Badge ve Button Geliştirmeleri */
.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Form Section Headers */
.form-section h6 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

/* Modal Shadow */
.modal-content {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

/* File input text update */
.custom-file-input:lang(tr) ~ .custom-file-label::after {
    content: "Gözat";
}

/* Opacity utility */
.opacity-75 {
    opacity: 0.75;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
    
    .form-section {
        padding: 15px;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 14px;
    }
    
    .image-preview-container {
        min-height: 80px;
    }
    
    .image-preview-container img {
        max-height: 80px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File input change handler for image preview
    function handleImagePreview(input, previewContainer) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Görsel önizleme" 
                         class="img-fluid rounded border shadow-sm"
                         style="max-height: 120px; object-fit: cover;">
                `;
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Add modal image preview
    const newSliderImageInput = document.getElementById('newSliderImage');
    const imagePreview = document.getElementById('imagePreview');
    
    if (newSliderImageInput && imagePreview) {
        newSliderImageInput.addEventListener('change', function() {
            handleImagePreview(this, imagePreview);
            
            // Update file label
            const fileName = this.files[0] ? this.files[0].name : 'Görsel dosyası seçin';
            const label = this.nextElementSibling;
            if (label) {
                label.textContent = fileName;
            }
        });
    }

    // Edit modal image previews
    document.querySelectorAll('input[type="file"][name="image"]').forEach(function(input) {
        if (input.id && input.id.startsWith('sliderImage')) {
            input.addEventListener('change', function() {
                const fileName = this.files[0] ? this.files[0].name : 'Görsel dosyası seçin';
                const label = this.nextElementSibling;
                if (label) {
                    label.textContent = fileName;
                }
                
                // If there's a preview area nearby, update it
                const modal = this.closest('.modal');
                const currentImageContainer = modal.querySelector('.current-image .image-preview-container');
                if (currentImageContainer && this.files && this.files[0]) {
                    handleImagePreview(this, currentImageContainer);
                }
            });
        }
    });

    // Form validation feedback
    document.querySelectorAll('.border-2').forEach(function(input) {
        input.addEventListener('invalid', function() {
            this.style.borderColor = '#dc3545';
        });
        
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = '#28a745';
            } else if (this.value.length > 0) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#ced4da';
            }
        });
    });

    // Character counter for description fields
    document.querySelectorAll('textarea[name="description"]').forEach(function(textarea) {
        const maxLength = 255; // Adjust as needed
        
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            // Find or create counter element
            let counter = this.parentNode.querySelector('.char-counter');
            if (!counter) {
                counter = document.createElement('small');
                counter.className = 'char-counter text-muted float-right';
                this.parentNode.appendChild(counter);
            }
            
            counter.textContent = `${currentLength}/${maxLength} karakter`;
            
            if (remaining < 20) {
                counter.className = 'char-counter text-warning float-right';
            } else if (remaining < 0) {
                counter.className = 'char-counter text-danger float-right';
            } else {
                counter.className = 'char-counter text-muted float-right';
            }
        });
    });
});
</script>