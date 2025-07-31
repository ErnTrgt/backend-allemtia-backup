@props(['title', 'breadcrumbs' => [], 'actions' => null])

<div class="page-header-wrapper">
    <div class="page-header-left">
        <h1 class="page-title">{{ $title }}</h1>
        @if(count($breadcrumbs) > 0)
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                    @if($loop->last)
                        <li class="breadcrumb-item active">{{ $breadcrumb['label'] }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
        @endif
    </div>
    @if($actions)
    <div class="page-header-right">
        {{ $actions }}
    </div>
    @endif
</div>

<style>
.page-header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-xl);
}

.page-header-left .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-sm);
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.breadcrumb-item {
    font-size: 14px;
    color: var(--gray-500);
}

.breadcrumb-item.active {
    color: var(--gray-700);
}

.breadcrumb-item a {
    color: var(--gray-500);
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: var(--primary-red);
}

@media (max-width: 768px) {
    .page-header-wrapper {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }
}
</style>