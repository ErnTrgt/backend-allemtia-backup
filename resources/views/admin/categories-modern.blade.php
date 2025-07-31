@extends('layouts.admin-modern')

@section('title', 'Kategoriler')

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
                                <button class="btn-action" onclick="addSubcategory({{ $category->id }}, '{{ $category->name }}')" title="Alt Kategori Ekle">
                                    <i class="bi bi-plus"></i>
                                </button>
                                <button class="btn-action" onclick="editCategory({{ $category->id }}, '{{ $category->name }}')" title="Düzenle">
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
                                        <button class="btn-action" onclick="addSubcategory({{ $child->id }}, '{{ $child->name }}')" title="Alt Kategori Ekle">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        @endif
                                        <button class="btn-action" onclick="editCategory({{ $child->id }}, '{{ $child->name }}')" title="Düzenle">
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
                                                <button class="btn-action" onclick="editCategory({{ $grandchild->id }}, '{{ $grandchild->name }}')" title="Düzenle">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kategori Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Üst Kategori (Opsiyonel)</label>
                        <select class="form-select" name="parent_id">
                            <option value="">Ana Kategori</option>
                            @foreach($allCategories as $cat)
                                @if(!$cat->parent_id)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alt Kategori Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subcategories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="category_id" id="parentCategoryId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Üst Kategori</label>
                        <input type="text" class="form-control" id="parentCategoryName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alt Kategori Adı</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kategori Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
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
function addSubcategory(parentId, parentName) {
    document.getElementById('parentCategoryId').value = parentId;
    document.getElementById('parentCategoryName').value = parentName;
    
    const modal = new bootstrap.Modal(document.getElementById('addSubcategoryModal'));
    modal.show();
}

// Edit Category
function editCategory(categoryId, categoryName) {
    const form = document.getElementById('editCategoryForm');
    form.action = `/admin/categories/${categoryId}`;
    document.getElementById('editCategoryName').value = categoryName;
    
    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}

// Delete Category
function deleteCategory(categoryId) {
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