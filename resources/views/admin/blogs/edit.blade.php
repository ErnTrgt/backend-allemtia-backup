@extends('layouts.layout')

@section('title', 'Blog Düzenle')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header-modern mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">
                            <i class="dw dw-edit-2 text-primary mr-2"></i>Blog Düzenle
                        </h2>
                        {{-- <p class="page-subtitle text-muted">{{ Str::limit($blog->title, 60) }}</p> --}}
                    </div>
                    <div class="page-actions">
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-light btn-lg">
                            <i class="dw dw-arrow-left mr-2"></i>Blog Listesi
                        </a>
                    </div>
                </div>
               
            </div>

            <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data" id="blogEditForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Ana İçerik -->
                    <div class="col-lg-8">
                        <!-- Temel Bilgiler -->
                        <div class="form-section-card mb-4">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-text text-primary mr-2"></i>
                                    Temel Bilgiler
                                </h5>
                                <div class="section-badge">
                                    <span class="badge badge-primary">Gerekli</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="form-group">
                                    <label for="title" class="form-label-modern">
                                        <i class="dw dw-edit mr-1"></i>Blog Başlığı
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-modern @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $blog->title) }}" 
                                           required placeholder="Blog başlığınızı yazın...">
                                    <div class="form-help">
                                        <small class="text-muted">SEO dostu, açıklayıcı bir başlık yazın</small>
                                        <div class="char-counter" data-target="title" data-max="100"></div>
                                    </div>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label-modern">
                                        <i class="dw dw-text-width mr-1"></i>Kısa Açıklama
                                    </label>
                                    <textarea class="form-control form-control-modern @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Blog için kısa bir açıklama yazın...">{{ old('description', $blog->description) }}</textarea>
                                    <div class="form-help">
                                        <small class="text-muted">Arama sonuçlarında görünecek özet</small>
                                        <div class="char-counter" data-target="description" data-max="160"></div>
                                    </div>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- İçerik Editörü -->
                        <div class="form-section-card mb-4">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-file-text text-success mr-2"></i>
                                    Blog İçeriği
                                </h5>
                                <div class="section-badge">
                                    <span class="badge badge-success">Ana İçerik</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="form-group">
                                    <label for="content" class="form-label-modern">
                                        <i class="dw dw-edit-2 mr-1"></i>Detaylı İçerik
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="editor-container">
                                        <textarea class="form-control summernote @error('content') is-invalid @enderror" 
                                                  id="content" name="content" required>{{ old('content', $blog->content) }}</textarea>
                                    </div>
                                    <div class="form-help">
                                        <small class="text-muted">
                                            <i class="dw dw-info mr-1"></i>
                                            Resim, video ve formatlanmış metin ekleyebilirsiniz
                                        </small>
                                        <div class="word-counter" id="contentWordCount">0 kelime</div>
                                    </div>
                                    @error('content')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yan Panel -->
                    <div class="col-lg-4">
                        <!-- Görsel Yükleme -->
                        <div class="form-section-card mb-4">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-image text-info mr-2"></i>
                                    Blog Görseli
                                </h5>
                            </div>
                            <div class="section-body">
                                <!-- Mevcut Görsel -->
                                @if($blog->blog_img)
                                <div class="current-image-preview mb-3">
                                    <label class="form-label-modern">
                                        <i class="dw dw-eye mr-1"></i>Mevcut Görsel
                                    </label>
                                    <div class="image-preview-container">
                                        <img src="{{ asset('storage/' . $blog->blog_img) }}" 
                                             alt="Mevcut blog görseli" 
                                             class="img-fluid rounded shadow-sm current-image">
                                        <div class="image-overlay">
                                            <span class="image-info">
                                                <i class="dw dw-check text-success"></i>
                                                Mevcut
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Yeni Görsel Yükleme -->
                                <div class="form-group">
                                    <label for="blog_img" class="form-label-modern">
                                        <i class="dw dw-upload mr-1"></i>
                                        {{ $blog->blog_img ? 'Görseli Değiştir' : 'Görsel Yükle' }}
                                    </label>
                                    <div class="upload-area" id="uploadArea">
                                        <div class="upload-content">
                                            <i class="dw dw-cloud-upload upload-icon"></i>
                                            <p class="upload-text">Görsel sürükleyin veya tıklayarak seçin</p>
                                            <p class="upload-formats">JPG, PNG, GIF, WEBP (Max: 2MB)</p>
                                        </div>
                                        <input type="file" class="file-input @error('blog_img') is-invalid @enderror" 
                                               id="blog_img" name="blog_img" accept="image/*" hidden>
                                    </div>
                                    @error('blog_img')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Yeni Görsel Önizleme -->
                                <div class="new-image-preview" id="newImagePreview" style="display: none;">
                                    <label class="form-label-modern">
                                        <i class="dw dw-eye mr-1"></i>Yeni Görsel Önizleme
                                    </label>
                                    <div class="image-preview-container">
                                        <img id="preview" src="#" alt="Yeni görsel önizleme" 
                                             class="img-fluid rounded shadow-sm">
                                        <div class="image-overlay">
                                            <span class="image-info new">
                                                <i class="dw dw-upload text-primary"></i>
                                                Yeni
                                            </span>
                                            <button type="button" class="btn btn-sm btn-danger remove-image" id="removePreview">
                                                <i class="dw dw-delete-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Blog Ayarları -->
                        <div class="form-section-card mb-4">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-settings text-warning mr-2"></i>
                                    Blog Ayarları
                                </h5>
                            </div>
                            <div class="section-body">
                                <div class="form-group">
                                    <label for="author" class="form-label-modern">
                                        <i class="dw dw-user mr-1"></i>Yazar Adı
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-modern @error('author') is-invalid @enderror" 
                                           id="author" name="author" value="{{ old('author', $blog->author) }}" 
                                           required placeholder="Yazar adını yazın">
                                    @error('author')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="date" class="form-label-modern">
                                        <i class="dw dw-calendar mr-1"></i>Yayın Tarihi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control form-control-modern @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', $blog->date->format('Y-m-d')) }}" required>
                                    @error('date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status" class="form-label-modern">
                                        <i class="dw dw-power mr-1"></i>Yayın Durumu
                                    </label>
                                    <div class="status-toggle">
                                        <select class="form-control form-control-modern" id="status" name="status">
                                            <option value="1" {{ old('status', $blog->status) == 1 ? 'selected' : '' }}>
                                                ✅ Yayında (Aktif)
                                            </option>
                                            <option value="0" {{ old('status', $blog->status) == 0 ? 'selected' : '' }}>
                                                ❌ Taslak (Pasif)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Blog İstatistikleri -->
                        <div class="form-section-card">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-analytics text-success mr-2"></i>
                                    Blog İstatistikleri
                                </h5>
                            </div>
                            <div class="section-body">
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="dw dw-calendar"></i>
                                        </div>
                                        <div class="stat-info">
                                            <span class="stat-label">Oluşturulma</span>
                                            <span class="stat-value">{{ $blog->created_at->format('d.m.Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <div class="stat-icon">
                                                <i class="dw dw-edit"></i>
                                            </div>
                                        </div>
                                        <div class="stat-info">
                                            <span class="stat-label">Son Güncelleme</span>
                                            <span class="stat-value">{{ $blog->updated_at->format('d.m.Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="form-actions-bar">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-status">
                            <span class="text-muted">
                                <i class="dw dw-info mr-1"></i>
                                Son değişiklik: <span id="lastModified">Şimdi</span>
                            </span>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-light btn-lg mr-2">
                                <i class="dw dw-cancel mr-2"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="saveButton">
                                <i class="dw dw-save mr-2"></i>
                                <span class="btn-text">Değişiklikleri Kaydet</span>
                                <div class="btn-loader" style="display: none;">
                                    <i class="dw dw-loading"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
/* Modern Page Header */
.page-header-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 30px;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #495057;
    margin: 0;
}

.page-subtitle {
    font-size: 14px;
    margin: 5px 0 0 0;
}

/* Progress Steps */
.progress-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.step-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.step.active .step-circle {
    background: #007bff;
    color: white;
}

.step span {
    font-size: 11px;
    margin-top: 5px;
    color: #6c757d;
}

.step.active span {
    color: #007bff;
    font-weight: 600;
}

.step-line {
    width: 50px;
    height: 2px;
    background: #e9ecef;
    margin: 0 10px;
}

/* Form Section Cards */
.form-section-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.section-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #495057;
}

.section-badge .badge {
    font-size: 11px;
    padding: 4px 8px;
}

.section-body {
    padding: 25px;
}

/* Modern Form Controls */
.form-label-modern {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.form-control-modern {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

.form-help {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 5px;
}

/* Character Counter */
.char-counter,
.word-counter {
    font-size: 11px;
    padding: 2px 8px;
    background: #e9ecef;
    border-radius: 12px;
    color: #6c757d;
    font-weight: 600;
}

.char-counter.warning,
.word-counter.warning {
    background: #fff3cd;
    color: #856404;
}

.char-counter.danger,
.word-counter.danger {
    background: #f8d7da;
    color: #721c24;
}

/* Upload Area */
.upload-area {
    border: 2px dashed #007bff;
    border-radius: 15px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.05) 0%, rgba(0, 123, 255, 0.1) 100%);
}

.upload-area:hover {
    border-color: #0056b3;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.15) 100%);
}

.upload-area.dragover {
    border-color: #28a745;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.15) 100%);
}

.upload-icon {
    font-size: 48px;
    color: #007bff;
    margin-bottom: 15px;
}

.upload-text {
    font-size: 16px;
    font-weight: 600;
    color: #495057;
    margin: 10px 0 5px 0;
}

.upload-formats {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
}

/* Image Preview */
.image-preview-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
}

.current-image,
#preview {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.1), rgba(0,0,0,0.3));
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-preview-container:hover .image-overlay {
    opacity: 1;
}

.image-info {
    background: rgba(255, 255, 255, 0.9);
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
}

.image-info.new {
    background: rgba(0, 123, 255, 0.9);
    color: white;
}

.remove-image {
    border-radius: 50%;
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.stat-info {
    flex: 1;
}

.stat-label {
    display: block;
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.stat-value {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #495057;
    margin-top: 2px;
}

/* Actions Bar */
.form-actions-bar {
    position: sticky;
    bottom: 0;
    background: white;
    border-top: 1px solid #e9ecef;
    padding: 20px 30px;
    margin: 30px -15px 0;
    z-index: 1000;
    box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.05);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

#saveButton {
    position: relative;
}

.btn-loader {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.btn-loader i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Editor Container */
.editor-container {
    border: 2px solid #e9ecef;
    border-radius: 15px;
    overflow: hidden;
}

.editor-container .note-editor {
    border: none;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header-modern {
        padding: 20px;
    }
    
    .page-title {
        font-size: 24px;
    }
    
    .progress-steps {
        display: none;
    }
    
    .section-body {
        padding: 20px;
    }
    
    .form-actions-bar {
        padding: 15px 20px;
        margin: 20px -15px 0;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .upload-area {
        padding: 30px 15px;
    }
    
    .upload-icon {
        font-size: 36px;
    }
}

/* Animation */
.form-section-card {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-tr-TR.js"></script>

<script>
$(document).ready(function() {
    // Summernote Editor
    $('.summernote').summernote({
        height: 350,
        lang: 'tr-TR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                updateWordCount(contents);
            }
        }
    });

    // Character Counter
    function setupCharCounter(inputId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.querySelector(`[data-target="${inputId}"]`);
        
        if (!input || !counter) return;
        
        function updateCounter() {
            const currentLength = input.value.length;
            const remaining = maxLength - currentLength;
            
            counter.textContent = `${currentLength}/${maxLength}`;
            counter.className = 'char-counter';
            
            if (remaining < 20) {
                counter.classList.add('warning');
            }
            if (remaining < 0) {
                counter.classList.add('danger');
            }
        }
        
        input.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // Setup character counters
    setupCharCounter('title', 100);
    setupCharCounter('description', 160);
    
    // Word Counter for content
    function updateWordCount(content) {
        const text = $('<div>').html(content).text();
        const wordCount = text.trim().split(/\s+/).filter(word => word.length > 0).length;
        const counter = document.getElementById('contentWordCount');
        
        if (counter) {
            counter.textContent = `${wordCount} kelime`;
            counter.className = 'word-counter';
            
            if (wordCount < 100) {
                counter.classList.add('warning');
            }
            if (wordCount < 50) {
                counter.classList.add('danger');
            }
        }
    }
    
    // Initial word count
    updateWordCount($('.summernote').summernote('code'));

    // File Upload Handling
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('blog_img');
    const preview = document.getElementById('preview');
    const newImagePreview = document.getElementById('newImagePreview');
    const removePreview = document.getElementById('removePreview');

    // Click to upload
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                newImagePreview.style.display = 'block';
                
                // Scroll to preview
                newImagePreview.scrollIntoView({ behavior: 'smooth', block: 'center' });
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove preview
    removePreview.addEventListener('click', () => {
        fileInput.value = '';
        newImagePreview.style.display = 'none';
        preview.src = '#';
    });

    // Form submission
    const form = document.getElementById('blogEditForm');
    const saveButton = document.getElementById('saveButton');
    const btnText = saveButton.querySelector('.btn-text');
    const btnLoader = saveButton.querySelector('.btn-loader');

    form.addEventListener('submit', function(e) {
        // Show loading state
        saveButton.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-block';
        
        // Update button text after a delay to show feedback
        setTimeout(() => {
            btnText.textContent = 'Kaydediliyor...';
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
        }, 1000);
    });

    // Auto-save indication (simulation)
    let lastModified = new Date();
    setInterval(() => {
        document.getElementById('lastModified').textContent = 
            Math.floor((new Date() - lastModified) / 1000) + ' saniye önce';
    }, 1000);

    // Form change detection
    const formInputs = form.querySelectorAll('input, textarea, select');
    formInputs.forEach(input => {
        input.addEventListener('change', () => {
            lastModified = new Date();
        });
    });
});
</script>
@endsection