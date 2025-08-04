<li class="category-item">
    <div class="category-node" onclick="toggleCategory(this); selectCategory({{ $category->id }})">
        <span class="toggle-icon">
            @if($category->children->isNotEmpty())
                <i class="bi bi-chevron-right"></i>
            @endif
        </span>
        
        <div class="category-icon">
            <i class="bi bi-folder{{ $level > 0 ? '2' : '' }}"></i>
        </div>
        
        <div class="category-info">
            <div class="category-name">{{ $category->name }}</div>
            <div class="category-meta">
                {{ $category->children->count() }} alt kategori
                @if($category->products_count ?? 0)
                    • {{ $category->products_count }} ürün
                @endif
            </div>
        </div>
        
        <div class="category-actions">
            <button class="category-action-btn add" data-bs-toggle="modal" data-bs-target="#addCategoryModal" 
                    onclick="event.stopPropagation(); setParentCategory({{ $category->id }}, '{{ $category->name }}')" 
                    data-tooltip="Alt kategori ekle">
                <i class="bi bi-plus"></i>
            </button>
            <button class="category-action-btn edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}" 
                    onclick="event.stopPropagation()" data-tooltip="Düzenle">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="category-action-btn delete" onclick="event.stopPropagation(); deleteCategory({{ $category->id }})" 
                    data-tooltip="Sil">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
    
    @if($category->children->isNotEmpty())
        <ul class="subcategories" style="display: none;">
            @foreach($category->children as $child)
                @include('admin.partials.category-tree-item', ['category' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>