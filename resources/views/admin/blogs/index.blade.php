@extends('layouts.admin')

@section('title', 'Blog Yönetimi')
@section('header-title', 'Blog Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/blogs.css') }}">
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

/* Blog Card Animations */
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

.blog-card-new {
    animation: slideIn 0.5s ease;
}

.blog-card-removing {
    animation: fadeOut 0.3s ease;
}

/* Updated Blog Highlight */
.blog-card-updated {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
    transition: box-shadow 1s ease;
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
            <input type="text" class="search-input" placeholder="Blog ara..." id="blogSearch">
        </div>
        
        <!-- Status Filter -->
        <div class="filter-pills">
            <button class="filter-pill active" data-status="all">
                Tümü
            </button>
            <button class="filter-pill" data-status="active">
                <i class="bi bi-check-circle"></i>
                Aktif
            </button>
            <button class="filter-pill" data-status="inactive">
                <i class="bi bi-x-circle"></i>
                Pasif
            </button>
        </div>
    </div>
    
    <!-- Add New Blog -->
    <button class="btn btn-primary" onclick="showAddBlogModal()" 
            style="background: var(--primary-red); border-color: var(--primary-red);">
        <i class="bi bi-plus-circle"></i>
        Yeni Blog Ekle
    </button>
</div>

<!-- Blog Stats -->
<div class="blog-stats">
    <!-- Total Blogs -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-newspaper"></i>
        </div>
        <div class="stat-value" id="totalBlogs">{{ $blogs->count() }}</div>
        <div class="stat-label">Toplam Blog</div>
    </div>
    
    <!-- Published -->
    <div class="stat-card published">
        <div class="stat-icon">
            <i class="bi bi-check-square"></i>
        </div>
        <div class="stat-value" id="activeBlogs">{{ $blogs->where('status', true)->count() }}</div>
        <div class="stat-label">Yayında</div>
    </div>
    
    <!-- Drafts -->
    <div class="stat-card draft">
        <div class="stat-icon">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <div class="stat-value" id="inactiveBlogs">{{ $blogs->where('status', false)->count() }}</div>
        <div class="stat-label">Taslak</div>
    </div>
    
    <!-- Total Views -->
    <div class="stat-card views">
        <div class="stat-icon">
            <i class="bi bi-eye"></i>
        </div>
        <div class="stat-value" id="totalViews">{{ number_format($blogs->sum('views', 0)) }}</div>
        <div class="stat-label">Toplam Görüntülenme</div>
    </div>
</div>

<!-- Blogs Grid -->
<div class="blogs-grid" id="blogsGrid">
    @forelse($blogs as $blog)
        <div class="blog-card" data-blog-id="{{ $blog->id }}" data-status="{{ $blog->status ? 'active' : 'inactive' }}" 
             data-title="{{ strtolower($blog->title) }}" data-author="{{ strtolower($blog->author) }}">
            <!-- Blog Image -->
            <div class="blog-image">
                @if($blog->blog_img)
                    <img src="{{ asset('storage/' . $blog->blog_img) }}" alt="{{ $blog->title }}">
                @else
                    <div class="blog-image-placeholder">
                        <i class="bi bi-image"></i>
                    </div>
                @endif
                
                <!-- Status Badge -->
                <span class="blog-status {{ $blog->status ? 'active' : 'inactive' }}">
                    <i class="bi bi-{{ $blog->status ? 'check' : 'x' }}-circle"></i>
                    {{ $blog->status ? 'Yayında' : 'Taslak' }}
                </span>
                
                <!-- Quick Actions -->
                <div class="blog-quick-actions">
                    <button class="quick-action-btn" onclick="editBlog({{ $blog->id }})" title="Düzenle">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="quick-action-btn" onclick="toggleBlogStatus({{ $blog->id }}, {{ $blog->status ? 0 : 1 }})" title="{{ $blog->status ? 'Taslağa Al' : 'Yayınla' }}">
                        <i class="bi bi-{{ $blog->status ? 'pause' : 'play' }}-circle"></i>
                    </button>
                    <button class="quick-action-btn danger" onclick="deleteBlog({{ $blog->id }})" title="Sil">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- Blog Content -->
            <div class="blog-content">
                <!-- Meta Info -->
                <div class="blog-meta">
                    <div class="blog-meta-item">
                        <i class="bi bi-person"></i>
                        <span>{{ $blog->author }}</span>
                    </div>
                    <div class="blog-meta-item">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ $blog->date->format('d.m.Y') }}</span>
                    </div>
                    @if($blog->views)
                    <div class="blog-meta-item">
                        <i class="bi bi-eye"></i>
                        <span>{{ number_format($blog->views) }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Title -->
                <h3 class="blog-title">{{ $blog->title }}</h3>
                
                <!-- Excerpt -->
                <p class="blog-excerpt">
                    {{ Str::limit(strip_tags($blog->content ?? 'Blog içeriği için kısa açıklama...'), 120) }}
                </p>
            </div>
        </div>
    @empty
        <div class="empty-state" id="emptyState">
            <i class="bi bi-newspaper empty-icon"></i>
            <h3 class="empty-title">Henüz Blog Yok</h3>
            <p class="empty-text">İlk blogunuzu oluşturmak için yukarıdaki butonu kullanın.</p>
        </div>
    @endforelse
</div>

<!-- Add Blog Modal -->
<div class="modal fade" id="addBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Yeni Blog Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form id="addBlogForm" method="POST" enctype="multipart/form-data" onsubmit="handleBlogAdd(event)">
                @csrf
                <div class="modal-body">
                    <!-- Blog Image -->
                    <div class="form-group">
                        <label class="form-label">Blog Görseli</label>
                        <div class="image-upload-area" id="imageUploadArea">
                            <input type="file" name="blog_img" id="blogImage" accept="image/*" style="display: none;">
                            <i class="bi bi-cloud-upload upload-icon"></i>
                            <div class="upload-text">Görsel yüklemek için tıklayın veya sürükleyin</div>
                            <div class="upload-hint">JPG, PNG veya GIF (Maks. 2MB)</div>
                            <div class="image-preview" style="display: none;">
                                <img id="previewImage" src="" alt="">
                                <button type="button" class="remove-image" onclick="removeImage()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Title -->
                    <div class="form-group">
                        <label class="form-label">Blog Başlığı</label>
                        <input type="text" name="title" class="form-control" required 
                               placeholder="Blog başlığını girin">
                    </div>
                    
                    <!-- Author -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Yazar</label>
                                <input type="text" name="author" class="form-control" required 
                                       placeholder="Yazar adı" value="{{ Auth::user()->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Yayın Tarihi</label>
                                <input type="date" name="date" class="form-control" required 
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="form-group">
                        <label class="form-label">Blog İçeriği</label>
                        <textarea name="content" class="form-control" rows="6" required
                                  placeholder="Blog içeriğini girin..."></textarea>
                    </div>
                    
                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label">Durum</label>
                        <select name="status" class="form-control" required>
                            <option value="1">Yayında</option>
                            <option value="0">Taslak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                        <i class="bi bi-check-lg me-1"></i>
                        Blog Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Blog Modal (Dynamic) -->
<div class="modal fade" id="editBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Blog Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form id="editBlogForm" method="POST" enctype="multipart/form-data" onsubmit="handleBlogEdit(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="editBlogId" name="blog_id">
                <div class="modal-body">
                    <!-- Blog Image -->
                    <div class="form-group">
                        <label class="form-label">Blog Görseli</label>
                        <div class="image-upload-area" id="editImageUploadArea">
                            <input type="file" name="blog_img" id="editBlogImage" accept="image/*" style="display: none;">
                            <i class="bi bi-cloud-upload upload-icon"></i>
                            <div class="upload-text">Görsel yüklemek için tıklayın veya sürükleyin</div>
                            <div class="upload-hint">JPG, PNG veya GIF (Maks. 2MB)</div>
                            <div class="image-preview" style="display: none;">
                                <img id="editPreviewImage" src="" alt="">
                                <button type="button" class="remove-image" onclick="removeEditImage()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Title -->
                    <div class="form-group">
                        <label class="form-label">Blog Başlığı</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required 
                               placeholder="Blog başlığını girin">
                    </div>
                    
                    <!-- Author -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Yazar</label>
                                <input type="text" name="author" id="editAuthor" class="form-control" required 
                                       placeholder="Yazar adı">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Yayın Tarihi</label>
                                <input type="date" name="date" id="editDate" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="form-group">
                        <label class="form-label">Blog İçeriği</label>
                        <textarea name="content" id="editContent" class="form-control" rows="6" required
                                  placeholder="Blog içeriğini girin..."></textarea>
                    </div>
                    
                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label">Durum</label>
                        <select name="status" id="editStatus" class="form-control" required>
                            <option value="1">Yayında</option>
                            <option value="0">Taslak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
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
<script src="{{ asset('admin/js/blogs-dynamic.js') }}"></script>
@endpush