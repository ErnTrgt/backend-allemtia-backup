@extends('layouts.admin-modern')

@section('title', 'Kategoriler')
@section('header-title', 'Kategoriler')

@section('content')
<div class="categories-container">
    <!-- Page Header Component -->
    <x-admin.page-header 
        title="Kategoriler"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Kategoriler']
        ]">
        <x-slot name="actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-circle me-2"></i>
                Yeni Kategori
            </button>
        </x-slot>
    </x-admin.page-header>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card total">
                <div class="stat-icon">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalCategories) }}</h3>
                    <p>Toplam Kategori</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card parent">
                <div class="stat-icon">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($categories->count()) }}</h3>
                    <p>Ana Kategori</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card sub">
                <div class="stat-icon">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalSubcategories) }}</h3>
                    <p>Alt Kategori</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card requests">
                <div class="stat-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($pendingRequests ?? 0) }}</h3>
                    <p>Bekleyen İstek</p>
                </div>
            </x-admin.glass-card>
        </div>
    </div>
    
    <!-- Categories Tree View -->
    <div class="row g-4">
        <div class="col-lg-8">
            <x-admin.glass-card>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Kategori Ağacı</h5>
                    <div class="category-actions">
                        <button class="btn btn-sm btn-outline-danger" onclick="expandAll()">
                            <i class="bi bi-arrows-expand me-1"></i>Tümünü Aç
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                            <i class="bi bi-arrows-collapse me-1"></i>Tümünü Kapat
                        </button>
                    </div>
                </div>
                
                <div class="category-tree">
                    @foreach($categories as $category)
                    <div class="category-item" data-id="{{ $category->id }}">
                        <div class="category-header">
                            @if($category->children->count() > 0)
                            <button class="expand-btn" onclick="toggleCategory({{ $category->id }})">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            @else
                            <span class="no-expand"></span>
                            @endif
                            
                            <div class="category-info">
                                <i class="bi bi-folder-fill category-icon"></i>
                                <span class="category-name">{{ $category->name }}</span>
                                <span class="category-count">({{ $category->products_count ?? 0 }} ürün)</span>
                            </div>
                            
                            <div class="category-actions">
                                <button class="btn-action" onclick="addSubcategory({{ $category->id }}, '{{ $category->name }}', 1)" title="Alt Kategori Ekle">
                                    <i class="bi bi-plus"></i>
                                </button>
                                <button class="btn-action" onclick="editCategory({{ $category->id }}, '{{ $category->name }}', {{ $category->parent_id ?? 'null' }}, 1)" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action text-danger" onclick="deleteCategory({{ $category->id }})" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if($category->children->count() > 0)
                        <div class="category-children" id="children-{{ $category->id }}" style="display: none;">
                            @foreach($category->children as $child)
                            <div class="category-item subcategory" data-id="{{ $child->id }}">
                                <div class="category-header">
                                    @if($child->children->count() > 0)
                                    <button class="expand-btn" onclick="toggleCategory({{ $child->id }})">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                    @else
                                    <span class="no-expand"></span>
                                    @endif
                                    
                                    <div class="category-info">
                                        <i class="bi bi-folder2-open category-icon"></i>
                                        <span class="category-name">{{ $child->name }}</span>
                                        <span class="category-count">({{ $child->products_count ?? 0 }} ürün)</span>
                                    </div>
                                    
                                    <div class="category-actions">
                                        @if($child->children->count() == 0)
                                        <button class="btn-action" onclick="addSubcategory({{ $child->id }}, '{{ $child->name }}', 2)" title="Alt-Alt Kategori Ekle">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        @endif
                                        <button class="btn-action" onclick="editCategory({{ $child->id }}, '{{ $child->name }}', {{ $child->parent_id ?? 'null' }}, 2)" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-action text-danger" onclick="deleteCategory({{ $child->id }})" title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                @if($child->children->count() > 0)
                                <div class="category-children" id="children-{{ $child->id }}" style="display: none;">
                                    @foreach($child->children as $grandchild)
                                    <div class="category-item subsubcategory" data-id="{{ $grandchild->id }}">
                                        <div class="category-header">
                                            <span class="no-expand"></span>
                                            
                                            <div class="category-info">
                                                <i class="bi bi-file-earmark category-icon"></i>
                                                <span class="category-name">{{ $grandchild->name }}</span>
                                                <span class="category-count">({{ $grandchild->products_count ?? 0 }} ürün)</span>
                                            </div>
                                            
                                            <div class="category-actions">
                                                <button class="btn-action" onclick="editCategory({{ $grandchild->id }}, '{{ $grandchild->name }}', {{ $grandchild->parent_id ?? 'null' }}, 3)" title="Düzenle">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn-action text-danger" onclick="deleteCategory({{ $grandchild->id }})" title="Sil">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </x-admin.glass-card>
        </div>
        
        <div class="col-lg-4">
            <!-- Category Requests -->
            <x-admin.glass-card>
                <h5 class="mb-3">Kategori İstekleri</h5>
                @if($categoryRequests && $categoryRequests->count() > 0)
                <div class="request-list">
                    @foreach($categoryRequests->where('status', 'pending')->take(5) as $request)
                    <div class="request-item">
                        <div class="request-info">
                            <h6>{{ $request->name }}</h6>
                            <span class="text-muted">{{ $request->seller->name ?? 'Bilinmiyor' }}</span>
                        </div>
                        <div class="request-actions">
                            <button class="btn btn-sm btn-success" onclick="approveRequest({{ $request->id }})">
                                <i class="bi bi-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rejectRequest({{ $request->id }})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.category.requests') }}" class="btn btn-sm btn-outline-danger w-100 mt-3">
                    Tüm İstekleri Gör
                </a>
                @else
                <p class="text-muted text-center mb-0">Bekleyen istek bulunmuyor</p>
                @endif
            </x-admin.glass-card>
            
            <!-- Quick Stats -->
            <x-admin.glass-card class="mt-4">
                <h5 class="mb-3">Hızlı İstatistikler</h5>
                <div class="quick-stats">
                    <div class="stat-item">
                        <span class="stat-label">En Çok Ürün:</span>
                        <span class="stat-value">{{ $topCategory->name ?? '-' }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Boş Kategoriler:</span>
                        <span class="stat-value">{{ $emptyCategories ?? 0 }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Son Eklenen:</span>
                        <span class="stat-value">{{ $latestCategory->name ?? '-' }}</span>
                    </div>
                </div>
            </x-admin.glass-card>
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
            <form id="addCategoryForm" action="{{ route('admin.categories.store') }}" method="POST" onsubmit="handleCategoryAdd(event)">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 0; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-folder" style="color: #A90000;"></i>
                            Kategori Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kategori Adı</label>
                                    <input type="text" class="form-control" name="name" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Üst Kategori (Opsiyonel)</label>
                                    <select class="form-select" name="parent_id" id="parentCategorySelect" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" onchange="checkCategoryLevel()">
                                        <option value="">Ana Kategori Olarak Ekle</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" data-level="1">{{ $cat->name }}</option>
                                            @foreach($cat->children as $subcat)
                                                <option value="{{ $subcat->id }}" data-level="2">&nbsp;&nbsp;&nbsp;&nbsp;└─ {{ $subcat->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    <small class="text-muted" id="categoryLevelInfo">Ana kategori olarak eklenecek</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-circle me-2"></i>
                        Kategori Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Subcategory Modal -->
<div class="modal fade" id="addSubcategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-folder2-open me-2" style="color: #A90000;"></i>
                    Alt Kategori Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form id="addSubcategoryForm" action="{{ route('admin.subcategories.store') }}" method="POST" onsubmit="handleSubcategoryAdd(event)">
                @csrf
                <input type="hidden" name="category_id" id="parentCategoryId">
                <input type="hidden" name="parent_level" id="parentLevel">
                <div class="modal-body" style="padding: 24px;">
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 0; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-folder2" style="color: #A90000;"></i>
                            Alt Kategori Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Üst Kategori</label>
                                    <input type="text" class="form-control" id="parentCategoryName" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.5); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Alt Kategori Adı</label>
                                    <input type="text" class="form-control" name="name" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-circle me-2"></i>
                        Alt Kategori Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Kategori Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form id="editCategoryForm" method="POST" onsubmit="handleCategoryEdit(event)">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 24px;">
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 0; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-folder" style="color: #A90000;"></i>
                            Kategori Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kategori Adı</label>
                                    <input type="text" class="form-control" name="name" id="editCategoryName" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Üst Kategori</label>
                                    <select class="form-select" name="parent_id" id="editParentCategorySelect" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" onchange="checkEditCategoryLevel()">
                                        <option value="">Ana Kategori</option>
                                    </select>
                                    <small class="text-muted" id="editCategoryLevelInfo"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-circle me-2"></i>
                        Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Categories Page Styles */
.categories-container {
    width: 100%;
    padding: 0 var(--spacing-lg);
}

/* Stat Cards */
.stat-card {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    height: 100%;
}

.stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--white);
}

.stat-card.total .stat-icon {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
}

.stat-card.parent .stat-icon {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
}

.stat-card.sub .stat-icon {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
}

.stat-card.requests .stat-icon {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.stat-card .stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-xs);
}

.stat-card .stat-content p {
    font-size: 14px;
    color: var(--gray-600);
    margin: 0;
}

/* Category Tree */
.category-tree {
    max-height: 700px;
    overflow-y: auto;
    padding-right: var(--spacing-sm);
    background: rgba(240, 248, 255, 0.3);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
}

.category-item {
    margin-bottom: var(--spacing-xs);
}

.category-header {
    display: flex;
    align-items: center;
    padding: var(--spacing-sm) var(--spacing-md);
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-sm);
    transition: all 0.2s ease;
    cursor: pointer;
}

.category-header:hover {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(169, 0, 0, 0.2);
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(169, 0, 0, 0.1);
}

.expand-btn {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: var(--primary-red);
    cursor: pointer;
    transition: transform 0.2s ease;
}

.expand-btn.expanded {
    transform: rotate(90deg);
}

.no-expand {
    width: 24px;
    height: 24px;
    display: inline-block;
}

.category-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-left: var(--spacing-sm);
}

.category-icon {
    font-size: 18px;
    color: var(--primary-red);
}

.category-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
}

.category-count {
    font-size: 12px;
    color: var(--gray-500);
}

.category-actions {
    display: flex;
    gap: var(--spacing-xs);
    opacity: 0;
    transition: opacity 0.2s ease;
}

.category-header:hover .category-actions {
    opacity: 1;
}

.btn-action {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(169, 0, 0, 0.2);
    border-radius: var(--radius-sm);
    color: var(--primary-red);
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background: var(--white);
    border-color: var(--primary-red);
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(169, 0, 0, 0.2);
}

.btn-action.text-danger:hover {
    background: rgba(193, 18, 31, 0.1);
    border-color: var(--secondary-red);
    color: var(--secondary-red);
}

/* Category Children */
.category-children {
    margin-left: 40px;
    margin-top: var(--spacing-xs);
}

.subcategory .category-header {
    background: rgba(0, 81, 187, 0.08);
    border-color: rgba(0, 81, 187, 0.2);
}

.subcategory .category-header:hover {
    background: rgba(0, 81, 187, 0.15);
    border-color: var(--primary-blue);
    box-shadow: 0 2px 8px rgba(0, 81, 187, 0.2);
}

.subcategory .category-icon {
    color: var(--primary-blue);
}

.subsubcategory .category-header {
    background: rgba(63, 161, 221, 0.08);
    border-color: rgba(63, 161, 221, 0.2);
}

.subsubcategory .category-header:hover {
    background: rgba(63, 161, 221, 0.15);
    border-color: var(--secondary-blue);
    box-shadow: 0 2px 8px rgba(63, 161, 221, 0.2);
}

.subsubcategory .category-icon {
    color: var(--secondary-blue);
}

/* Request List */
.request-list {
    max-height: 300px;
    overflow-y: auto;
}

.request-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm);
    border-bottom: 1px solid var(--gray-100);
}

.request-item:last-child {
    border-bottom: none;
}

.request-info h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 var(--spacing-xs);
    color: var(--gray-900);
}

.request-info span {
    font-size: 12px;
}

.request-actions {
    display: flex;
    gap: var(--spacing-xs);
}

/* Quick Stats */
.quick-stats {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm);
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-sm);
    transition: all 0.2s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(169, 0, 0, 0.2);
    transform: translateY(-1px);
}

.stat-label {
    font-size: 13px;
    color: var(--gray-600);
}

.stat-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
}

/* Custom Scrollbar */
.category-tree::-webkit-scrollbar,
.request-list::-webkit-scrollbar {
    width: 8px;
}

.category-tree::-webkit-scrollbar-track,
.request-list::-webkit-scrollbar-track {
    background: rgba(240, 248, 255, 0.5);
    border-radius: 4px;
}

.category-tree::-webkit-scrollbar-thumb,
.request-list::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, var(--primary-red), var(--secondary-red));
    border-radius: 4px;
}

.category-tree::-webkit-scrollbar-thumb:hover,
.request-list::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, var(--secondary-red), var(--primary-red));
}

/* Responsive */
@media (max-width: 991px) {
    .category-children {
        margin-left: 20px;
    }
    
    .category-actions {
        opacity: 1;
    }
}
</style>
@endsection

@push('scripts')
<script>
// Toggle Category
function toggleCategory(categoryId) {
    const children = document.getElementById(`children-${categoryId}`);
    const expandBtn = event.currentTarget;
    
    if (children.style.display === 'none') {
        children.style.display = 'block';
        expandBtn.classList.add('expanded');
    } else {
        children.style.display = 'none';
        expandBtn.classList.remove('expanded');
    }
}

// Expand All
function expandAll() {
    document.querySelectorAll('.category-children').forEach(child => {
        child.style.display = 'block';
    });
    document.querySelectorAll('.expand-btn').forEach(btn => {
        btn.classList.add('expanded');
    });
}

// Collapse All
function collapseAll() {
    document.querySelectorAll('.category-children').forEach(child => {
        child.style.display = 'none';
    });
    document.querySelectorAll('.expand-btn').forEach(btn => {
        btn.classList.remove('expanded');
    });
}

// Add Subcategory
function addSubcategory(parentId, parentName, level) {
    // 3. seviyeden sonra ekleme yapılmasın
    if (level >= 3) {
        alert('3. seviyeden daha derin kategori eklenemez!');
        return;
    }
    
    document.getElementById('parentCategoryId').value = parentId;
    document.getElementById('parentCategoryName').value = parentName;
    document.getElementById('parentLevel').value = level;
    
    // Modal başlığını güncelle
    const modalTitle = document.querySelector('#addSubcategoryModal .modal-title');
    if (level === 1) {
        modalTitle.innerHTML = '<i class="bi bi-folder2-open me-2" style="color: #A90000;"></i>Alt Kategori Ekle';
    } else if (level === 2) {
        modalTitle.innerHTML = '<i class="bi bi-file-earmark me-2" style="color: #A90000;"></i>Alt-Alt Kategori Ekle';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('addSubcategoryModal'));
    modal.show();
}

// Handle Subcategory Add with AJAX
function handleSubcategoryAdd(event) {
    event.preventDefault();
    
    const form = document.getElementById('addSubcategoryForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Ekleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addSubcategoryModal'));
            if (modal) {
                modal.hide();
            }
            
            // Add new subcategory to tree
            addCategoryToTree(data.category);
            
            // Update stats
            updateCategoryStats();
            
            // Show success toast
            const level = document.getElementById('parentLevel').value;
            const message = level == 1 ? 'Alt kategori başarıyla eklendi!' : 'Alt-alt kategori başarıyla eklendi!';
            showSuccessToast(message);
            
            // Reset form
            form.reset();
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        } else {
            alert(data.message || 'Bir hata oluştu!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kategori eklenirken bir hata oluştu. Lütfen tekrar deneyin.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Check Category Level for Add Category Modal
function checkCategoryLevel() {
    const select = document.getElementById('parentCategorySelect');
    const selectedOption = select.options[select.selectedIndex];
    const level = selectedOption.getAttribute('data-level');
    const info = document.getElementById('categoryLevelInfo');
    
    if (!select.value) {
        info.textContent = 'Ana kategori olarak eklenecek';
        info.style.color = '#6b7280';
    } else if (level === '1') {
        info.textContent = 'Alt kategori olarak eklenecek';
        info.style.color = '#059669';
    } else if (level === '2') {
        info.textContent = 'Alt-alt kategori olarak eklenecek (3. seviye - Son seviye)';
        info.style.color = '#dc2626';
    }
}

// Edit Category
function editCategory(categoryId, categoryName, parentId = null, currentLevel = 0) {
    const form = document.getElementById('editCategoryForm');
    form.action = `/admin/categories/${categoryId}`;
    document.getElementById('editCategoryName').value = categoryName;
    
    // Parent select'i doldur
    const parentSelect = document.getElementById('editParentCategorySelect');
    
    // Ana kategori ise boş seçenek ekle, değilse ekleme
    if (currentLevel === 1 || !parentId) {
        parentSelect.innerHTML = '<option value="">Ana Kategori</option>';
    } else {
        parentSelect.innerHTML = '';
    }
    
    // Hidden input ekle - parent_id'yi saklamak için
    let hiddenParentInput = document.getElementById('hiddenParentId');
    if (!hiddenParentInput) {
        hiddenParentInput = document.createElement('input');
        hiddenParentInput.type = 'hidden';
        hiddenParentInput.name = 'keep_parent_id';
        hiddenParentInput.id = 'hiddenParentId';
        form.appendChild(hiddenParentInput);
    }
    hiddenParentInput.value = parentId || '';
    
    // Tüm kategorileri ekle (kendisi ve alt kategorileri hariç)
    @foreach($categories as $cat)
        if ({{ $cat->id }} !== categoryId) {
            const option1 = document.createElement('option');
            option1.value = '{{ $cat->id }}';
            option1.textContent = '{{ $cat->name }}';
            option1.setAttribute('data-level', '1');
            if ({{ $cat->id }} == parentId) option1.selected = true;
            parentSelect.appendChild(option1);
            
            @foreach($cat->children as $subcat)
                if ({{ $subcat->id }} !== categoryId) {
                    const option2 = document.createElement('option');
                    option2.value = '{{ $subcat->id }}';
                    option2.textContent = '    └─ {{ $subcat->name }}';
                    option2.setAttribute('data-level', '2');
                    if ({{ $subcat->id }} == parentId) option2.selected = true;
                    parentSelect.appendChild(option2);
                    
                    // Alt kategorileri de kontrol et (kendisi değilse)
                    @foreach($subcat->children as $grandchild)
                        if ({{ $grandchild->id }} !== categoryId) {
                            // 3. seviye kategoriler parent listesinde gösterilmez
                        }
                    @endforeach
                }
            @endforeach
        }
    @endforeach
    
    // Eğer parent seçilmemişse ve currentLevel > 1 ise, uyarı göster
    if (!parentId && currentLevel > 1) {
        const info = document.getElementById('editCategoryLevelInfo');
        info.textContent = 'UYARI: Parent seçilmezse ana kategori olur!';
        info.style.color = '#dc2626';
    }
    
    checkEditCategoryLevel();
    
    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}

// Check Edit Category Level
function checkEditCategoryLevel() {
    const select = document.getElementById('editParentCategorySelect');
    const selectedOption = select.options[select.selectedIndex];
    const level = selectedOption.getAttribute('data-level');
    const info = document.getElementById('editCategoryLevelInfo');
    
    if (!select.value) {
        info.textContent = 'Ana kategori olarak güncellenecek';
        info.style.color = '#6b7280';
    } else if (level === '1') {
        info.textContent = 'Alt kategori olarak güncellenecek';
        info.style.color = '#059669';
    } else if (level === '2') {
        info.textContent = 'Alt-alt kategori olarak güncellenecek (3. seviye)';
        info.style.color = '#dc2626';
    }
}

// Handle Category Add with AJAX
function handleCategoryAdd(event) {
    event.preventDefault();
    
    const form = document.getElementById('addCategoryForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Ekleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
            if (modal) {
                modal.hide();
            }
            
            // Add new category to tree
            addCategoryToTree(data.category);
            
            // Update stats
            updateCategoryStats();
            
            // Show success toast
            showSuccessToast('Kategori başarıyla eklendi!');
            
            // Reset form
            form.reset();
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        } else {
            alert(data.message || 'Bir hata oluştu!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kategori eklenirken bir hata oluştu. Lütfen tekrar deneyin.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Handle Category Edit with AJAX
function handleCategoryEdit(event) {
    event.preventDefault();
    
    const form = document.getElementById('editCategoryForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Güncelleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
            if (modal) {
                modal.hide();
            }
            
            // Update category in tree
            updateCategoryInTree(data.category);
            
            // Show success toast
            showSuccessToast('Kategori başarıyla güncellendi!');
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        } else {
            alert(data.message || 'Bir hata oluştu!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Kategori güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Delete Category with AJAX
function deleteCategory(categoryId) {
    if (confirm('Bu kategoriyi ve alt kategorilerini silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove category from tree
                removeCategoryFromTree(categoryId);
                
                // Update stats
                updateCategoryStats();
                
                // Show success toast
                showSuccessToast('Kategori başarıyla silindi!');
            } else {
                alert(data.message || 'Bir hata oluştu!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kategori silinirken bir hata oluştu. Lütfen tekrar deneyin.');
        });
    }
}

// Original delete category function backup
function deleteCategory_old(categoryId) {
    if (confirm('Bu kategoriyi ve alt kategorilerini silmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/categories/${categoryId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Add category to tree dynamically
function addCategoryToTree(category) {
    const categoryTree = document.querySelector('.category-tree');
    
    // If it's a subcategory
    if (category.parent_id) {
        const parentItem = document.querySelector(`.category-item[data-id="${category.parent_id}"]`);
        if (parentItem) {
            let childrenContainer = parentItem.querySelector('.category-children');
            
            // Create children container if it doesn't exist
            if (!childrenContainer) {
                childrenContainer = document.createElement('div');
                childrenContainer.className = 'category-children';
                childrenContainer.id = `children-${category.parent_id}`;
                childrenContainer.style.display = 'block'; // Directly show for new items
                parentItem.appendChild(childrenContainer);
                
                // Add expand button to parent
                const header = parentItem.querySelector('.category-header');
                const expandBtn = header.querySelector('.expand-btn');
                if (!expandBtn) {
                    const noExpand = header.querySelector('.no-expand');
                    if (noExpand) {
                        const newExpandBtn = document.createElement('button');
                        newExpandBtn.className = 'expand-btn expanded';
                        newExpandBtn.onclick = () => toggleCategory(category.parent_id);
                        newExpandBtn.innerHTML = '<i class="bi bi-chevron-down"></i>';
                        noExpand.replaceWith(newExpandBtn);
                    }
                }
            } else {
                // Show the container if it's hidden
                childrenContainer.style.display = 'block';
                // Update expand button
                const expandBtn = parentItem.querySelector('.expand-btn');
                if (expandBtn && !expandBtn.classList.contains('expanded')) {
                    expandBtn.classList.add('expanded');
                    expandBtn.innerHTML = '<i class="bi bi-chevron-down"></i>';
                }
            }
            
            // Add new category to children container
            const newCategoryHtml = createCategoryItemHtml(category);
            childrenContainer.insertAdjacentHTML('beforeend', newCategoryHtml);
        }
    } else {
        // Add as main category
        const newCategoryHtml = createCategoryItemHtml(category);
        categoryTree.insertAdjacentHTML('beforeend', newCategoryHtml);
    }
    
    // Add animation
    const newElement = document.querySelector(`.category-item[data-id="${category.id}"]`);
    if (newElement) {
        newElement.style.animation = 'slideIn 0.5s ease';
        
        // Scroll into view
        setTimeout(() => {
            newElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }
}

// Update category in tree
function updateCategoryInTree(category) {
    const categoryItem = document.querySelector(`.category-item[data-id="${category.id}"]`);
    if (categoryItem) {
        const nameElement = categoryItem.querySelector('.category-name');
        if (nameElement) {
            nameElement.textContent = category.name;
        }
        
        // Add update animation
        categoryItem.style.background = 'rgba(16, 185, 129, 0.1)';
        setTimeout(() => {
            categoryItem.style.background = '';
        }, 1000);
    }
}

// Remove category from tree
function removeCategoryFromTree(categoryId) {
    const categoryItem = document.querySelector(`.category-item[data-id="${categoryId}"]`);
    if (categoryItem) {
        // Add fade out animation
        categoryItem.style.transition = 'opacity 0.3s ease';
        categoryItem.style.opacity = '0';
        
        setTimeout(() => {
            categoryItem.remove();
            
            // Check if parent has any children left
            const parent = categoryItem.parentElement;
            if (parent && parent.classList.contains('category-children')) {
                if (parent.children.length === 0) {
                    // Remove expand button from parent
                    const parentId = parent.id.replace('children-', '');
                    const parentItem = document.querySelector(`.category-item[data-id="${parentId}"]`);
                    if (parentItem) {
                        const expandBtn = parentItem.querySelector('.expand-btn');
                        if (expandBtn) {
                            const noExpand = document.createElement('span');
                            noExpand.className = 'no-expand';
                            expandBtn.replaceWith(noExpand);
                        }
                    }
                    parent.remove();
                }
            }
        }, 300);
    }
}

// Create category item HTML
function createCategoryItemHtml(category) {
    // Determine level based on parent relationships
    let level = '';
    let levelNum = 1;
    
    if (category.parent_id) {
        // Check if parent has parent_id to determine if this is subsubcategory
        const parentItem = document.querySelector(`.category-item[data-id="${category.parent_id}"]`);
        if (parentItem) {
            if (parentItem.classList.contains('subcategory')) {
                level = 'subsubcategory';
                levelNum = 3;
            } else {
                level = 'subcategory';
                levelNum = 2;
            }
        } else {
            // If parent not found in DOM, check by parent data
            level = category.parent?.parent_id ? 'subsubcategory' : 'subcategory';
            levelNum = category.parent?.parent_id ? 3 : 2;
        }
    }
    
    const icon = level === 'subsubcategory' ? 'bi-file-earmark' : (level === 'subcategory' ? 'bi-folder2-open' : 'bi-folder-fill');
    
    return `
        <div class="category-item ${level}" data-id="${category.id}">
            <div class="category-header">
                <span class="no-expand"></span>
                <div class="category-info">
                    <i class="bi ${icon} category-icon"></i>
                    <span class="category-name">${category.name}</span>
                    <span class="category-count">(${category.products_count || 0} ürün)</span>
                </div>
                <div class="category-actions">
                    ${levelNum < 3 ? `
                        <button class="btn-action" onclick="addSubcategory(${category.id}, '${category.name.replace(/'/g, "\\''")}', ${levelNum})" title="${levelNum === 1 ? 'Alt Kategori' : 'Alt-Alt Kategori'} Ekle">
                            <i class="bi bi-plus"></i>
                        </button>
                    ` : ''}
                    <button class="btn-action" onclick="editCategory(${category.id}, '${category.name.replace(/'/g, "\\''")}', ${category.parent_id || 'null'}, ${levelNum})" title="Düzenle">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn-action text-danger" onclick="deleteCategory(${category.id})" title="Sil">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Update category stats
function updateCategoryStats() {
    fetch('/admin/categories/stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update total categories
        const totalCard = document.querySelector('.stat-card.total h3');
        if (totalCard) totalCard.textContent = data.total.toLocaleString('tr-TR');
        
        // Update parent categories
        const parentCard = document.querySelector('.stat-card.parent h3');
        if (parentCard) parentCard.textContent = data.parents.toLocaleString('tr-TR');
        
        // Update subcategories
        const subCard = document.querySelector('.stat-card.sub h3');
        if (subCard) subCard.textContent = data.subcategories.toLocaleString('tr-TR');
    })
    .catch(error => console.error('Error updating stats:', error));
}

// Show success toast
function showSuccessToast(message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show and auto-hide toast
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();
    
    // Remove toast element after it's hidden
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
`;
document.head.appendChild(style);

// Approve Request
function approveRequest(requestId) {
    if (confirm('Bu kategori isteğini onaylamak istediğinizden emin misiniz?')) {
        updateRequestStatus(requestId, 'approved');
    }
}

// Reject Request
function rejectRequest(requestId) {
    if (confirm('Bu kategori isteğini reddetmek istediğinizden emin misiniz?')) {
        updateRequestStatus(requestId, 'rejected');
    }
}

// Update Request Status
function updateRequestStatus(requestId, status) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/category-requests/${requestId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'PUT';
    
    const statusField = document.createElement('input');
    statusField.type = 'hidden';
    statusField.name = 'status';
    statusField.value = status;
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    form.appendChild(statusField);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush