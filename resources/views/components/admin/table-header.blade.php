@props(['searchPlaceholder' => 'Ara...', 'filters' => null, 'actions' => null])

<div class="table-header">
    <div class="table-filters">
        @if($filters)
        <div class="filter-group">
            {{ $filters }}
        </div>
        @endif
        
        <div class="table-actions">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" 
                       class="table-search" 
                       placeholder="{{ $searchPlaceholder }}" 
                       id="{{ $attributes->get('search-id', 'tableSearch') }}">
            </div>
            
            @if($actions)
                {{ $actions }}
            @endif
        </div>
    </div>
</div>

<style>
.table-header {
    padding: var(--spacing-lg);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.table-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
}

.filter-group {
    display: flex;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.table-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.search-wrapper {
    position: relative;
}

.search-wrapper i {
    position: absolute;
    left: var(--spacing-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
    pointer-events: none;
}

.table-search {
    padding: var(--spacing-sm) var(--spacing-md) var(--spacing-sm) var(--spacing-2xl);
    background: var(--gray-50);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    width: 300px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.table-search:focus {
    outline: none;
    background: var(--white);
    border-color: var(--primary-red);
    box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
}

@media (max-width: 991px) {
    .table-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: var(--spacing-sm);
    }
    
    .table-search {
        width: 100%;
    }
}
</style>