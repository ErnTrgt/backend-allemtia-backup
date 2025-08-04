@extends('layouts.admin')

@section('title', 'Blog Yönetimi')
@section('header-title', 'Blog Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/blogs.css') }}">
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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal" 
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
        <div class="stat-value">{{ $blogs->count() }}</div>
        <div class="stat-label">Toplam Blog</div>
    </div>
    
    <!-- Published -->
    <div class="stat-card published">
        <div class="stat-icon">
            <i class="bi bi-check-square"></i>
        </div>
        <div class="stat-value">{{ $blogs->where('status', true)->count() }}</div>
        <div class="stat-label">Yayında</div>
    </div>
    
    <!-- Drafts -->
    <div class="stat-card draft">
        <div class="stat-icon">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <div class="stat-value">{{ $blogs->where('status', false)->count() }}</div>
        <div class="stat-label">Taslak</div>
    </div>
    
    <!-- Total Views -->
    <div class="stat-card views">
        <div class="stat-icon">
            <i class="bi bi-eye"></i>
        </div>
        <div class="stat-value">{{ number_format($blogs->sum('views', 0)) }}</div>
        <div class="stat-label">Toplam Görüntülenme</div>
    </div>
</div>

<!-- Blogs Grid -->
<div class="blogs-grid" id="blogsGrid">
    @forelse($blogs as $blog)
        <div class="blog-card" data-status="{{ $blog->status ? 'active' : 'inactive' }}" 
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
        <div class="empty-state">
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
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
                        <textarea name="content" class="form-control" rows="6" 
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

<!-- Single Edit Blog Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Blog Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBlogForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                        <textarea name="content" id="editContent" class="form-control" rows="6" 
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

<!-- Hidden Blog Data for JavaScript -->
<script>
const blogsData = {!! json_encode($blogs->map(function($blog) {
    return [
        'id' => $blog->id,
        'title' => $blog->title,
        'author' => $blog->author,
        'date' => $blog->date->format('Y-m-d'),
        'content' => $blog->content,
        'status' => $blog->status,
        'blog_img' => $blog->blog_img ? asset('storage/' . $blog->blog_img) : null
    ];
})) !!};
</script>
@endsection

@push('scripts')
<script>
// Search functionality
let searchTimer;
document.getElementById('blogSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const cards = document.querySelectorAll('.blog-card');
        
        cards.forEach(card => {
            const title = card.dataset.title;
            const author = card.dataset.author;
            
            if (title.includes(query) || author.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);
});

// Status filter
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        
        const status = this.dataset.status;
        const cards = document.querySelectorAll('.blog-card');
        
        cards.forEach(card => {
            if (status === 'all') {
                card.style.display = '';
            } else if (status === 'active' && card.dataset.status === 'active') {
                card.style.display = '';
            } else if (status === 'inactive' && card.dataset.status === 'inactive') {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Image upload for new blog
const imageUploadArea = document.getElementById('imageUploadArea');
const blogImageInput = document.getElementById('blogImage');
const previewImage = document.getElementById('previewImage');

imageUploadArea.addEventListener('click', () => {
    blogImageInput.click();
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

blogImageInput.addEventListener('change', function() {
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
    blogImageInput.value = '';
    previewImage.src = '';
    imageUploadArea.classList.remove('has-image');
    imageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Edit Blog Modal Functions
const editImageUploadArea = document.getElementById('editImageUploadArea');
const editBlogImageInput = document.getElementById('editBlogImage');
const editPreviewImage = document.getElementById('editPreviewImage');

editImageUploadArea.addEventListener('click', () => {
    editBlogImageInput.click();
});

editBlogImageInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            editPreviewImage.src = e.target.result;
            editImageUploadArea.classList.add('has-image');
            editImageUploadArea.querySelector('.image-preview').style.display = 'flex';
        };
        reader.readAsDataURL(this.files[0]);
    }
});

function removeEditImage() {
    editBlogImageInput.value = '';
    editPreviewImage.src = '';
    editImageUploadArea.classList.remove('has-image');
    editImageUploadArea.querySelector('.image-preview').style.display = 'none';
}

// Edit Blog Function
function editBlog(blogId) {
    const blog = blogsData.find(b => b.id === blogId);
    if (blog) {
        // Update form action
        document.getElementById('editBlogForm').action = `/admin/blogs/${blogId}`;
        
        // Fill form fields
        document.getElementById('editTitle').value = blog.title;
        document.getElementById('editAuthor').value = blog.author;
        document.getElementById('editDate').value = blog.date;
        document.getElementById('editContent').value = blog.content;
        document.getElementById('editStatus').value = blog.status ? '1' : '0';
        
        // Handle image
        if (blog.blog_img) {
            editPreviewImage.src = blog.blog_img;
            editImageUploadArea.classList.add('has-image');
            editImageUploadArea.querySelector('.image-preview').style.display = 'flex';
        } else {
            editImageUploadArea.classList.remove('has-image');
            editImageUploadArea.querySelector('.image-preview').style.display = 'none';
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editBlogModal'));
        modal.show();
    }
}

// Toggle Blog Status
function toggleBlogStatus(blogId, newStatus) {
    if (confirm(newStatus ? 'Bu blogu yayınlamak istediğinizden emin misiniz?' : 'Bu blogu taslağa almak istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.blogs.change-status') }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = blogId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        
        form.appendChild(csrfToken);
        form.appendChild(idInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete Blog
function deleteBlog(blogId) {
    if (confirm('Bu blogu silmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/blogs/${blogId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
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