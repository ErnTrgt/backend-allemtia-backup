@extends('layouts.layout')

@section('title', 'Yeni Blog Ekle')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header-modern mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">
                            <i class="dw dw-add text-success mr-2"></i>Yeni Blog Oluştur
                        </h2>
                        <p class="page-subtitle text-muted">Yeni bir blog yazısı ekleyin ve yayınlayın</p>
                    </div>
                    <div class="page-actions">
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-light btn-lg">
                            <i class="dw dw-arrow-left mr-2"></i>Blog Listesi
                        </a>
                    </div>
                </div>
                <!-- Progress Indicator -->
                
            </div>

            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogCreateForm">
                @csrf
                
                <div class="row">
                    <!-- Ana İçerik -->
                    <div class="col-lg-8">
                        <!-- Temel Bilgiler -->
                        <div class="form-section-card mb-4">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-text text-primary mr-2"></i>
                                    Blog Başlığı ve Açıklama
                                </h5>
                                <div class="section-badge">
                                    <span class="badge badge-primary">1. Adım</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="form-group">
                                    <label for="title" class="form-label-modern">
                                        <i class="dw dw-edit mr-1"></i>Blog Başlığı
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-modern @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           required placeholder="Çekici ve SEO dostu bir başlık yazın...">
                                    <div class="form-help">
                                        <small class="text-muted">Okuyucuları çekecek ve arama motorlarında bulunabilir bir başlık</small>
                                        <div class="char-counter" data-target="title" data-max="100"></div>
                                    </div>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label-modern">
                                        <i class="dw dw-text-width mr-1"></i>Meta Açıklama
                                    </label>
                                    <textarea class="form-control form-control-modern @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Blog için kısa ve çekici bir açıklama yazın...">{{ old('description') }}</textarea>
                                    <div class="form-help">
                                        <small class="text-muted">Google arama sonuçlarında görünecek açıklama</small>
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
                                        <div class="editor-toolbar">
                                            <div class="toolbar-info">
                                                <small class="text-muted">
                                                    <i class="dw dw-info mr-1"></i>
                                                    Zengin metin editörü - resim, video ve bağlantı ekleyebilirsiniz
                                                </small>
                                            </div>
                                        </div>
                                        <textarea class="form-control summernote @error('content') is-invalid @enderror" 
                                                  id="content" name="content" required>{{ old('content') }}</textarea>
                                    </div>
                                    <div class="form-help">
                                        <div class="editor-stats">
                                            <div class="word-counter" id="contentWordCount">0 kelime</div>
                                            <div class="reading-time" id="readingTime">~0 dk okuma</div>
                                        </div>
                                    </div>
                                    @error('content')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- İçerik İpuçları -->
                        <div class="tips-card mb-4">
                            <div class="tips-header">
                                <h6 class="tips-title">
                                    <i class="dw dw-lightbulb text-warning mr-2"></i>
                                    İyi Blog Yazma İpuçları
                                </h6>
                            </div>
                            <div class="tips-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="tips-list">
                                            <li>• Başlığınızı etkileyici yapın</li>
                                            <li>• Kısa paragraflar kullanın</li>
                                            <li>• Alt başlıklar ekleyin</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="tips-list">
                                            <li>• Görseller ekleyin</li>
                                            <li>• Anlaşılır dil kullanın</li>
                                            <li>• Sonuçta özet yapın</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yan Panel -->
                    <div class="col-lg-4">
                        <!-- Yayınlama Ayarları -->
                        <div class="form-section-card mb-4">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-settings text-warning mr-2"></i>
                                    Yayınlama Ayarları
                                </h5>
                                <div class="section-badge">
                                    <span class="badge badge-warning">3. Adım</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="form-group">
                                    <label for="author" class="form-label-modern">
                                        <i class="dw dw-user mr-1"></i>Yazar Adı
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-modern @error('author') is-invalid @enderror" 
                                           id="author" name="author" value="{{ old('author', auth()->user()->name) }}" 
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
                                           id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                    <small class="text-muted">Gelecek bir tarih seçerseniz zamanlanmış yayın olur</small>
                                    @error('date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status" class="form-label-modern">
                                        <i class="dw dw-power mr-1"></i>Yayın Durumu
                                    </label>
                                    <div class="status-selector">
                                        <select class="form-control form-control-modern" id="status" name="status">
                                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>
                                                ✅ Hemen Yayınla (Aktif)
                                            </option>
                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>
                                                📝 Taslak Olarak Kaydet (Pasif)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Görsel Yükleme -->
                        <div class="form-section-card mb-4">
                            <div class="section-header">
                                <h5 class="section-title">
                                    <i class="dw dw-image text-info mr-2"></i>
                                    Blog Görseli
                                </h5>
                                <div class="section-badge">
                                    <span class="badge badge-info">2. Adım</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <!-- Upload Area -->
                                <div class="form-group">
                                    <label class="form-label-modern">
                                        <i class="dw dw-upload mr-1"></i>Kapak Görseli Yükle
                                    </label>
                                    <div class="upload-area" id="uploadArea">
                                        <label for="blog_img" class="upload-label">
                                            <div class="upload-content">
                                                <i class="dw dw-cloud-upload upload-icon"></i>
                                                <p class="upload-text">Görsel sürükleyin veya tıklayarak seçin</p>
                                                <p class="upload-formats">JPG, PNG, GIF, WEBP (Max: 2MB)</p>
                                            </div>
                                            <input type="file" class="file-input" id="blog_img" name="blog_img" accept="image/*">
                                        </label>
                                    </div>
                                    @error('blog_img')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Image Preview -->
                                <div class="image-preview-section" id="imagePreviewSection" style="display: none;">
                                    <label class="form-label-modern">
                                        <i class="dw dw-eye mr-1"></i>Görsel Önizleme
                                    </label>
                                    <div class="image-preview-container">
                                        <img id="preview" src="#" alt="Görsel önizleme" class="img-fluid rounded shadow-sm">
                                        <div class="image-overlay">
                                            <div class="image-info">
                                                <i class="dw dw-upload text-primary"></i>
                                                Yeni Görsel
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger remove-image" id="removePreview">
                                                <i class="dw dw-delete-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="image-actions mt-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="changeImage">
                                            <i class="dw dw-image mr-1"></i>Görsel Değiştir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Blog Özeti -->
                        <div class="blog-summary-card">
                            <div class="summary-header">
                                <h6 class="summary-title">
                                    <i class="dw dw-analytics text-success mr-2"></i>
                                    Blog Özeti
                                </h6>
                            </div>
                            <div class="summary-body">
                                <div class="summary-stats">
                                    <div class="summary-stat">
                                        <div class="stat-icon">
                                            <i class="dw dw-text"></i>
                                        </div>
                                        <div class="stat-info">
                                            <span class="stat-value" id="titleLength">0</span>
                                            <span class="stat-label">Başlık Karakter</span>
                                        </div>
                                    </div>
                                    <div class="summary-stat">
                                        <div class="stat-icon">
                                            <i class="dw dw-book"></i>
                                        </div>
                                        <div class="stat-info">
                                            <span class="stat-value" id="wordCount">0</span>
                                            <span class="stat-label">Kelime</span>
                                        </div>
                                    </div>
                                    <div class="summary-stat">
                                        <div class="stat-icon">
                                            <i class="dw dw-clock"></i>
                                        </div>
                                        <div class="stat-info">
                                            <span class="stat-value" id="readTime">0</span>
                                            <span class="stat-label">Dk Okuma</span>
                                        </dev>
                                    </div>
                                    <div class="summary-stat">
                                        <div class="stat-icon">
                                            <i class="dw dw-image"></i>
                                        </div>
                                        <div class="stat-info">
                                            <span class="stat-value" id="imageStatus">❌</span>
                                            <span class="stat-label">Görsel</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="completion-progress mt-3">
                                    <div class="progress-label">
                                        <span>Tamamlanma: <strong id="completionPercentage">0%</strong></span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" id="completionBar" style="width: 0%"></div>
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
                            <div class="status-indicator" id="statusIndicator">
                                <i class="dw dw-pencil text-warning mr-1"></i>
                                <span class="text-muted">Blog yazılıyor...</span>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-light btn-lg mr-2">
                                <i class="dw dw-cancel mr-2"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" id="saveButton">
                                <i class="dw dw-add mr-2"></i>
                                <span class="btn-text">Blog Oluştur</span>
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
    background: #28a745;
    color: white;
}

.step span {
    font-size: 11px;
    margin-top: 5px;
    color: #6c757d;
}

.step.active span {
    color: #28a745;
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
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
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

/* Editor Container */
.editor-container {
    border: 2px solid #e9ecef;
    border-radius: 15px;
    overflow: hidden;
}

.editor-toolbar {
    background: #f8f9fa;
    padding: 10px 15px;
    border-bottom: 1px solid #e9ecef;
}

.editor-container .note-editor {
    border: none;
}

.editor-stats {
    display: flex;
    gap: 15px;
    align-items: center;
}

.reading-time {
    font-size: 11px;
    padding: 2px 8px;
    background: #e7f3ff;
    border-radius: 12px;
    color: #0056b3;
    font-weight: 600;
}

/* Tips Card */
.tips-card {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-radius: 15px;
    border: 1px solid #ffeaa7;
    overflow: hidden;
}

.tips-header {
    padding: 15px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.tips-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #856404;
}

.tips-body {
    padding: 15px 20px;
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    color: #856404;
    font-size: 12px;
    margin-bottom: 5px;
}

/* Upload Area - DÜZELTİLMİŞ */
.upload-area {
    border: 2px dashed #28a745;
    border-radius: 15px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, rgba(40, 167, 69, 0.1) 100%);
    position: relative;
    overflow: hidden;
}

.upload-area:hover {
    border-color: #1e7e34;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.15) 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
}

.upload-area.dragover {
    border-color: #007bff;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.15) 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.upload-label {
    display: block;
    width: 100%;
    height: 100%;
    cursor: pointer;
    position: relative;
    z-index: 2;
}

.upload-content {
    position: relative;
    z-index: 2;
}

.upload-icon {
    font-size: 48px;
    color: #28a745;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.upload-area:hover .upload-icon {
    transform: scale(1.1);
    color: #1e7e34;
}

.upload-text {
    font-size: 18px;
    font-weight: 600;
    color: #495057;
    margin: 10px 0 5px 0;
}

.upload-formats {
    font-size: 12px;
    color: #6c757d;
    margin: 15px 0;
    background: rgba(255, 255, 255, 0.7);
    display: inline-block;
    padding: 5px 15px;
    border-radius: 20px;
}

/* File input gizlenmesi */
.file-input {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}

/* Image Preview */
.image-preview-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
}

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
    background: rgba(0, 123, 255, 0.9);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
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

.image-actions {
    text-align: center;
}

/* Blog Summary Card */
.blog-summary-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.summary-header {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    padding: 15px 20px;
    border-bottom: 1px solid #c3e6cb;
}

.summary-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #155724;
}

.summary-body {
    padding: 20px;
}

.summary-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.summary-stat {
    display: flex;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.summary-stat .stat-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #28a745;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-size: 12px;
}

.stat-info {
    flex: 1;
}

.stat-value {
    display: block;
    font-size: 16px;
    font-weight: 700;
    color: #495057;
}

.stat-label {
    display: block;
    font-size: 10px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.completion-progress {
    text-align: center;
}

.progress-label {
    margin-bottom: 8px;
    font-size: 12px;
    color: #6c757d;
}

.progress {
    height: 8px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    transition: width 0.3s ease;
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

.status-indicator {
    display: flex;
    align-items: center;
    font-size: 14px;
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
    
    .summary-stats {
        grid-template-columns: 1fr;
    }
    
    .upload-area {
        padding: 30px 15px;
    }
    
    .upload-icon {
        font-size: 36px;
    }
    
    .editor-stats {
        flex-direction: column;
        gap: 5px;
        align-items: flex-start;
    }
}

/* Animation */
.form-section-card,
.tips-card,
.blog-summary-card {
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
                updateContentStats(contents);
                updateCompletionProgress();
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
            
            // Update summary stats
            if (inputId === 'title') {
                document.getElementById('titleLength').textContent = currentLength;
            }
            
            updateCompletionProgress();
        }
        
        input.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // Setup character counters
    setupCharCounter('title', 100);
    setupCharCounter('description', 160);
    
    // Content Stats
    function updateContentStats(content) {
        const text = $('<div>').html(content).text();
        const words = text.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;
        const readingTime = Math.max(1, Math.ceil(wordCount / 200)); // 200 words per minute
        
        // Update counters
        document.getElementById('contentWordCount').textContent = `${wordCount} kelime`;
        document.getElementById('readingTime').textContent = `~${readingTime} dk okuma`;
        
        // Update summary
        document.getElementById('wordCount').textContent = wordCount;
        document.getElementById('readTime').textContent = readingTime;
        
        // Update counter styling
        const wordCounter = document.getElementById('contentWordCount');
        wordCounter.className = 'word-counter';
        
        if (wordCount < 100) {
            wordCounter.classList.add('warning');
        }
        if (wordCount < 50) {
            wordCounter.classList.add('danger');
        }
    }
    
    // Initial content stats
    updateContentStats($('.summernote').summernote('code'));

    // File Upload Handling - DÜZELTİLMİŞ
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('blog_img');
    const preview = document.getElementById('preview');
    const imagePreviewSection = document.getElementById('imagePreviewSection');
    const removePreview = document.getElementById('removePreview');
    const changeImage = document.getElementById('changeImage');

    // Change image button
    if (changeImage) {
        changeImage.addEventListener('click', function() {
            if (fileInput) {
                fileInput.click();
            }
        });
    }

    // Drag and drop
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            // Sadece upload area'dan çıkıldığında removeClass yap
            if (!uploadArea.contains(e.relatedTarget)) {
                uploadArea.classList.remove('dragover');
            }
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            } else {
                alert('Lütfen sadece resim dosyası yükleyin.');
            }
        });
    }

    // File input change
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                
                // Dosya türü kontrolü
                if (!file.type.startsWith('image/')) {
                    alert('Lütfen sadece resim dosyası seçin.');
                    fileInput.value = '';
                    return;
                }
                
                // Dosya boyutu kontrolü (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Dosya boyutu 2MB\'dan küçük olmalıdır.');
                    fileInput.value = '';
                    return;
                }
                
                handleFileSelect(file);
            }
        });
    }

    // Handle file selection
    function handleFileSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreviewSection.style.display = 'block';
                uploadArea.style.display = 'none';
                
                // Update image status
                document.getElementById('imageStatus').textContent = '✅';
                updateCompletionProgress();
                
                // Scroll to preview with smooth animation
                setTimeout(() => {
                    imagePreviewSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }, 100);
            };
            reader.onerror = function() {
                alert('Dosya okuma hatası. Lütfen tekrar deneyin.');
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove preview
    if (removePreview) {
        removePreview.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Confirmation dialog
            if (confirm('Seçilen görseli kaldırmak istediğinizden emin misiniz?')) {
                fileInput.value = '';
                imagePreviewSection.style.display = 'none';
                uploadArea.style.display = 'block';
                preview.src = '#';
                
                // Update image status
                document.getElementById('imageStatus').textContent = '❌';
                updateCompletionProgress();
                
                // Smooth scroll back to upload area
                setTimeout(() => {
                    uploadArea.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }, 100);
            }
        });
    }

    // Completion Progress
    function updateCompletionProgress() {
        const title = document.getElementById('title').value.trim();
        const content = $('.summernote').summernote('code').trim();
        const hasImage = fileInput.files.length > 0;
        const author = document.getElementById('author').value.trim();
        
        let completed = 0;
        let total = 4;
        
        if (title.length >= 10) completed++;
        if (content.length >= 100) completed++;
        if (hasImage) completed++;
        if (author.length >= 2) completed++;
        
        const percentage = Math.round((completed / total) * 100);
        
        document.getElementById('completionPercentage').textContent = percentage + '%';
        document.getElementById('completionBar').style.width = percentage + '%';
        
        // Update progress steps
        const steps = document.querySelectorAll('.step');
        steps.forEach((step, index) => {
            if (index === 0 && (title || content)) {
                step.classList.add('active');
            } else if (index === 1 && hasImage) {
                step.classList.add('active');
            } else if (index === 2 && percentage >= 75) {
                step.classList.add('active');
            }
        });
        
        // Update status indicator
        const statusIndicator = document.getElementById('statusIndicator');
        if (percentage >= 90) {
            statusIndicator.innerHTML = '<i class="dw dw-check text-success mr-1"></i><span class="text-success">Blog hazır!</span>';
        } else if (percentage >= 50) {
            statusIndicator.innerHTML = '<i class="dw dw-edit text-primary mr-1"></i><span class="text-primary">Blog şekilleniyor...</span>';
        } else {
            statusIndicator.innerHTML = '<i class="dw dw-pencil text-warning mr-1"></i><span class="text-muted">Blog yazılıyor...</span>';
        }
    }

    // Form submission
    const form = document.getElementById('blogCreateForm');
    const saveButton = document.getElementById('saveButton');
    const btnText = saveButton.querySelector('.btn-text');
    const btnLoader = saveButton.querySelector('.btn-loader');

    form.addEventListener('submit', function(e) {
        // Show loading state
        saveButton.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-block';
        
        // Update button text after a delay
        setTimeout(() => {
            btnText.textContent = 'Oluşturuluyor...';
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
        }, 1000);
    });

    // Initial completion check
    updateCompletionProgress();
});
</script>
@endsection