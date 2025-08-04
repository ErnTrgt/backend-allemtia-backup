@extends('layouts.admin-modern')

@section('title', 'Kategoriler')
@section('header-title', 'Kategori Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/categories.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Kategori Yönetimi</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Kategoriler</span>
    </div>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Kategori ara..." id="categorySearch">
        </div>
    </div>
    
    <div class="page-actions-right">
        <!-- Add Category -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="background: var(--primary-red); border-color: var(--primary-red);">
            <i class="bi bi-plus-circle"></i>
            Yeni Kategori
        </button>
    </div>
</div>

<!-- Categories Container -->
<div class="categories-container">
    <!-- Categories Tree -->
    <div class="categories-tree">
        <div class="tree-header">
            <h3 class="tree-title">Kategori Ağacı</h3>
            <div class="tree-actions">
                <button class="btn btn-sm btn-outline-secondary" onclick="expandAll()">
                    <i class="bi bi-arrows-expand"></i>
                    Tümünü Aç
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                    <i class="bi bi-arrows-collapse"></i>
                    Tümünü Kapat
                </button>
            </div>
        </div>
        
        <div class="tree-content">
            <ul class="category-tree">
                @foreach ($categories as $category)
                    @include('admin.partials.category-tree-item', ['category' => $category, 'level' => 0])
                @endforeach
            </ul>
            
            @if($categories->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-folder-x" style="font-size: 48px; color: var(--gray-300);"></i>
                    <p class="text-muted mt-3">Henüz kategori eklenmemiş</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Category Details -->
    <div class="category-details" id="categoryDetails">
        <div class="details-header">
            <h3 class="details-title">Kategori Detayları</h3>
        </div>
        
        <div class="details-content" id="detailsContent">
            <div class="empty-details">
                <i class="bi bi-info-circle empty-icon"></i>
                <p class="empty-text">Detaylarını görmek için bir kategori seçin</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-folder-plus me-2" style="color: #A90000;"></i>
                    Yeni Kategori Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <!-- Kategori Bilgileri -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-folder" style="color: #A90000;"></i>
                            Kategori Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kategori Adı</label>
                                    <input type="text" name="name" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Örn: Elektronik, Giyim" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Üst Kategori</label>
                                    <select name="parent_id" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                        <option value="">Ana Kategori</option>
                                        @foreach ($allCategories as $cat)
                                            @php
                                                $prefix = '';
                                                if ($cat->parent) {
                                                    $parent = $cat->parent;
                                                    $prefix = '— ';
                                                    while ($parent->parent) {
                                                        $prefix .= '— ';
                                                        $parent = $parent->parent;
                                                    }
                                                }
                                            @endphp
                                            <option value="{{ $cat->id }}">{{ $prefix }}{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Boş bırakırsanız ana kategori olarak eklenir</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Görsel ve Açıklama -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-image" style="color: #A90000;"></i>
                            Görsel ve Açıklama
                        </h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kategori Görseli</label>
                                    <div class="image-upload-area" onclick="document.getElementById('categoryImage').click()" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                        <p class="upload-text" style="color: #6b7280; margin: 0; font-size: 14px;">Görsel yüklemek için tıklayın</p>
                                        <input type="file" name="image" id="categoryImage" class="d-none" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                                    <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="Kategori açıklaması (isteğe bağlı)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-lg me-1"></i>
                        Kategori Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modals -->
@foreach ($allCategories as $category)
<div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Kategori Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.updateCategory', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 24px;">
                    <!-- Kategori Bilgileri -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-folder" style="color: #A90000;"></i>
                            Kategori Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kategori Adı</label>
                                    <input type="text" name="name" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" value="{{ $category->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($category->parent_id || $category->parent_id === null)
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Üst Kategori</label>
                                    <select name="parent_id" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                        <option value="">Ana Kategori</option>
                                        @foreach ($allCategories as $cat)
                                            @if($cat->id != $category->id && !($cat->isDescendantOf ?? false)($category))
                                                @php
                                                    $prefix = '';
                                                    if ($cat->parent) {
                                                        $parent = $cat->parent;
                                                        $prefix = '— ';
                                                        while ($parent->parent) {
                                                            $prefix .= '— ';
                                                            $parent = $parent->parent;
                                                        }
                                                    }
                                                @endphp
                                                <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                                                    {{ $prefix }}{{ $cat->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Görsel ve Açıklama -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-image" style="color: #A90000;"></i>
                            Görsel ve Açıklama
                        </h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kategori Görseli</label>
                                    <div class="image-upload-area" onclick="document.getElementById('editCategoryImage{{ $category->id }}').click()" style="background: rgba(255, 255, 255, 0.5); border: 2px dashed rgba(169, 0, 0, 0.3); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
                                        <p class="upload-text" style="color: #6b7280; margin: 0; font-size: 14px;">Yeni görsel yüklemek için tıklayın</p>
                                        <input type="file" name="image" id="editCategoryImage{{ $category->id }}" class="d-none" accept="image/*">
                                    </div>
                                    @if($category->image)
                                    <small class="text-muted mt-2 d-block">Mevcut görsel: {{ basename($category->image) }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                                    <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;">{{ $category->description ?? '' }}</textarea>
                                </div>
                            </div>
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
document.getElementById('categorySearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const categoryNodes = document.querySelectorAll('.category-node');
        
        categoryNodes.forEach(node => {
            const categoryName = node.querySelector('.category-name').textContent.toLowerCase();
            const categoryItem = node.closest('.category-item');
            
            if (categoryName.includes(query)) {
                categoryItem.style.display = '';
                // Expand parent categories
                let parent = categoryItem.parentElement.closest('.category-item');
                while (parent) {
                    parent.style.display = '';
                    const parentSubcategories = parent.querySelector('.subcategories');
                    if (parentSubcategories) {
                        parentSubcategories.style.display = 'block';
                        const toggleIcon = parent.querySelector('.toggle-icon');
                        if (toggleIcon) toggleIcon.classList.add('expanded');
                    }
                    parent = parent.parentElement.closest('.category-item');
                }
            } else {
                categoryItem.style.display = 'none';
            }
        });
    }, 300);
});

// Toggle subcategories
function toggleCategory(element) {
    const categoryItem = element.closest('.category-item');
    const subcategories = categoryItem.querySelector('.subcategories');
    const toggleIcon = element.querySelector('.toggle-icon');
    
    if (subcategories) {
        if (subcategories.style.display === 'none' || !subcategories.style.display) {
            subcategories.style.display = 'block';
            toggleIcon.classList.add('expanded');
        } else {
            subcategories.style.display = 'none';
            toggleIcon.classList.remove('expanded');
        }
    }
}

// Expand all categories
function expandAll() {
    document.querySelectorAll('.subcategories').forEach(sub => {
        sub.style.display = 'block';
    });
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.add('expanded');
    });
}

// Collapse all categories
function collapseAll() {
    document.querySelectorAll('.subcategories').forEach(sub => {
        sub.style.display = 'none';
    });
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.remove('expanded');
    });
}

// Select category and show details
function selectCategory(categoryId) {
    // Remove active state from all
    document.querySelectorAll('.category-node').forEach(node => {
        node.classList.remove('active');
    });
    
    // Add active state to selected
    event.currentTarget.classList.add('active');
    
    // Fetch and display category details
    fetch(`/admin/categories/${categoryId}/details`)
        .then(response => response.json())
        .then(data => {
            displayCategoryDetails(data);
        })
        .catch(error => {
            console.error('Error:', error);
            // If endpoint doesn't exist, show mock data
            displayMockCategoryDetails(categoryId);
        });
}

// Display category details
function displayCategoryDetails(category) {
    const detailsContent = document.getElementById('detailsContent');
    
    detailsContent.innerHTML = `
        <div class="category-image-wrapper">
            ${category.image ? 
                `<img src="${category.image}" alt="${category.name}" class="category-image">` :
                `<i class="bi bi-folder no-image"></i>`
            }
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Kategori Adı</div>
            <div class="detail-value">${category.name}</div>
        </div>
        
        ${category.parent ? `
            <div class="detail-item">
                <div class="detail-label">Üst Kategori</div>
                <div class="detail-value">${category.parent.name}</div>
            </div>
        ` : ''}
        
        <div class="detail-item">
            <div class="detail-label">Açıklama</div>
            <div class="detail-value">${category.description || 'Açıklama eklenmemiş'}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Oluşturulma Tarihi</div>
            <div class="detail-value">${category.created_at}</div>
        </div>
        
        <div class="category-stats">
            <div class="stat-card">
                <div class="stat-value">${category.subcategories_count || 0}</div>
                <div class="stat-label">Alt Kategori</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">${category.products_count || 0}</div>
                <div class="stat-label">Ürün</div>
            </div>
        </div>
        
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal${category.id}" style="background: var(--primary-blue); border-color: var(--primary-blue);">
                <i class="bi bi-pencil"></i>
                Düzenle
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})" style="background: var(--danger); border-color: var(--danger);">
                <i class="bi bi-trash"></i>
                Sil
            </button>
        </div>
    `;
}

// Display mock category details (fallback)
function displayMockCategoryDetails(categoryId) {
    const categoryNode = document.querySelector(`[onclick="selectCategory(${categoryId})"]`);
    const categoryName = categoryNode.querySelector('.category-name').textContent;
    const subcategoriesCount = categoryNode.closest('.category-item').querySelectorAll('.subcategories .category-item').length;
    
    const detailsContent = document.getElementById('detailsContent');
    
    detailsContent.innerHTML = `
        <div class="category-image-wrapper">
            <i class="bi bi-folder no-image"></i>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Kategori Adı</div>
            <div class="detail-value">${categoryName}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Açıklama</div>
            <div class="detail-value">Açıklama eklenmemiş</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Oluşturulma Tarihi</div>
            <div class="detail-value">${new Date().toLocaleDateString('tr-TR')}</div>
        </div>
        
        <div class="category-stats">
            <div class="stat-card">
                <div class="stat-value">${subcategoriesCount}</div>
                <div class="stat-label">Alt Kategori</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Ürün</div>
            </div>
        </div>
        
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal${categoryId}" style="background: var(--primary-blue); border-color: var(--primary-blue);">
                <i class="bi bi-pencil"></i>
                Düzenle
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteCategory(${categoryId})" style="background: var(--danger); border-color: var(--danger);">
                <i class="bi bi-trash"></i>
                Sil
            </button>
        </div>
    `;
}

// Delete category
function deleteCategory(categoryId) {
    if (confirm('Bu kategoriyi ve tüm alt kategorilerini silmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/categories/${categoryId}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Image preview
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const uploadArea = input.closest('.image-upload-area');
                uploadArea.innerHTML = `
                    <img src="${e.target.result}" style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage(this)">
                        <i class="bi bi-trash"></i> Kaldır
                    </button>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
});

// Remove image preview
function removeImage(button) {
    const uploadArea = button.closest('.image-upload-area');
    const fileInput = uploadArea.querySelector('input[type="file"]');
    fileInput.value = '';
    uploadArea.innerHTML = `
        <i class="bi bi-cloud-upload upload-icon" style="font-size: 48px; color: rgba(169, 0, 0, 0.5); margin-bottom: 16px; display: block;"></i>
        <p class="upload-text" style="color: #6b7280; margin: 0; font-size: 14px;">Görsel yüklemek için tıklayın</p>
        <input type="file" name="image" id="${fileInput.id}" class="d-none" accept="image/*">
    `;
    // Re-attach event listener
    const newInput = uploadArea.querySelector('input[type="file"]');
    newInput.addEventListener('change', handleImageChange);
}

// Handle image change
function handleImageChange(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const uploadArea = e.target.closest('.image-upload-area');
            uploadArea.innerHTML = `
                <img src="${e.target.result}" style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage(this)">
                    <i class="bi bi-trash"></i> Kaldır
                </button>
            `;
        };
        reader.readAsDataURL(file);
    }
}

// Add hover effect to upload areas
document.querySelectorAll('.image-upload-area').forEach(area => {
    area.addEventListener('mouseenter', function() {
        this.style.background = 'rgba(255, 255, 255, 0.7)';
        this.style.borderColor = 'rgba(169, 0, 0, 0.5)';
    });
    area.addEventListener('mouseleave', function() {
        this.style.background = 'rgba(255, 255, 255, 0.5)';
        this.style.borderColor = 'rgba(169, 0, 0, 0.3)';
    });
});

// Set parent category when adding subcategory
function setParentCategory(categoryId, categoryName) {
    const select = document.querySelector('#addCategoryModal select[name="parent_id"]');
    if (select) {
        select.value = categoryId;
    }
}
</script>
@endpush