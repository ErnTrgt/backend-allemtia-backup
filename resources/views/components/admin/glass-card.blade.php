@props(['class' => '', 'padding' => true])

<div class="glass-card {{ $class }}" {{ $attributes }}>
    @if($padding)
        <div class="glass-card-content">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
    overflow: hidden;
}

.glass-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.glass-card-content {
    padding: var(--spacing-lg);
}

/* Disable hover effect for table cards */
.glass-card.table-card:hover {
    transform: none;
}
</style>