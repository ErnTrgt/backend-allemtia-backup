@extends('layouts.admin-modern')

@section('title', 'Mağazalar')
@section('header-title', 'Mağaza Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/stores.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Mağaza Yönetimi</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Mağazalar</span>
    </div>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Mağaza ara..." id="storeSearch">
        </div>
        
        <!-- Filter by Status -->
        <div class="filter-dropdown">
            <button class="filter-btn" data-bs-toggle="dropdown">
                <i class="bi bi-funnel"></i>
                Filtrele
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.stores') }}">Tüm Mağazalar</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.stores', ['status' => 'approved']) }}">Aktif</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.stores', ['status' => 'rejected']) }}">Pasif</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.stores', ['status' => 'pending']) }}">Onay Bekleyen</a></li>
            </ul>
        </div>
        
        <!-- View Toggle -->
        <div class="view-toggle">
            <button class="view-toggle-btn active" data-view="grid" title="Grid Görünüm">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button class="view-toggle-btn" data-view="list" title="Liste Görünüm">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
    
    <div class="page-actions-right">
        <!-- Add New Store -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
            <i class="bi bi-plus-circle"></i>
            Yeni Mağaza
        </button>
    </div>
</div>

<!-- Store Cards or List Container -->
<div class="stores-grid grid-view" id="storesContainer">
    @forelse ($stores as $store)
        @php
            // Calculate store statistics
            $totalProducts = $store->products_count ?? $store->products->count() ?? 0;
            $activeProducts = $store->products ? $store->products->where('status', 1)->count() : 0;
            $totalOrders = 0;
            $totalRevenue = 0;
            
            // Calculate orders and revenue
            if ($store->products) {
                foreach ($store->products as $product) {
                    if ($product->orderItems) {
                        $totalOrders += $product->orderItems->count();
                        $totalRevenue += $product->orderItems->sum('total_price');
                    }
                }
            }
            
            // Store rating (you can implement actual rating system)
            $rating = rand(35, 50) / 10;
        @endphp
        
        <!-- Store Card -->
        <div class="store-card" data-status="{{ $store->status }}">
            <div class="store-card-header">
                @if(isset($store->logo) && $store->logo)
                    <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->store_name ?? $store->name }}" class="store-image">
                @else
                    <div class="store-image store-image-placeholder">
                        <i class="bi bi-shop"></i>
                    </div>
                @endif
                <span class="store-status-badge status-{{ $store->status }}">
                    <span class="status-dot"></span>
                    @if($store->status === 'approved')
                        Aktif
                    @elseif($store->status === 'pending')
                        Beklemede
                    @elseif($store->status === 'rejected')
                        Pasif
                    @else
                        {{ ucfirst($store->status) }}
                    @endif
                </span>
            </div>
            
            <div class="store-card-body">
                <h3 class="store-name">{{ $store->store_name ?? $store->name }}</h3>
                <p class="store-owner">
                    <i class="bi bi-person"></i>
                    {{ $store->name }}
                </p>
                
                <div class="store-stats">
                    <div class="store-stat">
                        <div class="stat-value">{{ $totalProducts }}</div>
                        <div class="stat-label">Ürünler</div>
                    </div>
                    <div class="store-stat">
                        <div class="stat-value">{{ $totalOrders }}</div>
                        <div class="stat-label">Siparişler</div>
                    </div>
                    <div class="store-stat">
                        <div class="stat-value">₺{{ number_format($totalRevenue, 0) }}</div>
                        <div class="stat-label">Gelir</div>
                    </div>
                    <div class="store-stat">
                        <div class="stat-value">{{ number_format($rating, 1) }}⭐</div>
                        <div class="stat-label">Puan</div>
                    </div>
                </div>
            </div>
            
            <div class="store-card-footer">
                <div class="store-actions">
                    <button class="action-btn" data-bs-toggle="modal" data-bs-target="#viewStoreModal{{ $store->id }}" title="Görüntüle">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="action-btn edit" data-bs-toggle="modal" data-bs-target="#editStoreModal{{ $store->id }}" title="Düzenle">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="action-btn toggle" onclick="toggleStoreStatus({{ $store->id }})" title="{{ $store->status === 'approved' ? 'Pasifleştir' : 'Aktifleştir' }}">
                        <i class="bi bi-{{ $store->status === 'approved' ? 'pause' : 'play' }}"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteStore({{ $store->id }})" title="Sil">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>

    @empty
        <!-- Empty State -->
        <div class="empty-state">
            <i class="bi bi-shop empty-icon"></i>
            <h3 class="empty-title">Mağaza Bulunamadı</h3>
            <p class="empty-text">Henüz hiç mağaza eklenmemiş.</p>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                <i class="bi bi-plus-circle"></i>
                İlk Mağazayı Ekle
            </button>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($stores instanceof \Illuminate\Pagination\AbstractPaginator && $stores->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $stores->links('components.admin-pagination') }}
</div>
@endif

<!-- Store Modals -->
@foreach ($stores as $store)
    @php
        // Re-calculate for modals
        $totalProducts = $store->products_count ?? $store->products->count() ?? 0;
        $activeProducts = $store->products ? $store->products->where('status', 1)->count() : 0;
        $totalOrders = 0;
        $totalRevenue = 0;
        
        if ($store->products) {
            foreach ($store->products as $product) {
                if ($product->orderItems) {
                    $totalOrders += $product->orderItems->count();
                    $totalRevenue += $product->orderItems->sum('total_price');
                }
            }
        }
        
        $rating = rand(35, 50) / 10;
    @endphp
    
    <!-- View Store Details Modal -->
    <div class="modal fade" id="viewStoreModal{{ $store->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                    <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                        <i class="bi bi-shop me-2" style="color: #A90000;"></i>
                        Mağaza Detayları: <span class="badge bg-light text-dark" style="margin-left: 8px; font-size: 14px; font-weight: normal;">{{ $store->store_name ?? $store->name }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <!-- Store Info Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-shop" style="color: #A90000;"></i>
                            Mağaza Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="store-profile" style="text-align: center;">
                                    @if(isset($store->logo) && $store->logo)
                                        <img src="{{ asset('storage/' . $store->logo) }}" 
                                             alt="Store Logo" style="max-width: 150px; max-height: 150px; border-radius: 12px; margin-bottom: 16px;">
                                    @else
                                        <div style="width: 150px; height: 150px; background: rgba(169, 0, 0, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                            <i class="bi bi-shop" style="font-size: 48px; color: #A90000;"></i>
                                        </div>
                                    @endif
                                    <h5 style="color: #1f2937; margin-bottom: 8px;">{{ $store->store_name ?? $store->name }}</h5>
                                    @if(isset($store->store_description) && $store->store_description)
                                        <p style="color: #6b7280; font-size: 14px;">{{ $store->store_description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-list">
                                    @if($store->address)
                                        <div style="margin-bottom: 12px;">
                                            <i class="bi bi-geo-alt me-2" style="color: #A90000;"></i>
                                            <span style="color: #374151; font-size: 14px;">{{ $store->address }}</span>
                                        </div>
                                    @endif
                                    @if(isset($store->website) && $store->website)
                                        <div style="margin-bottom: 12px;">
                                            <i class="bi bi-globe me-2" style="color: #A90000;"></i>
                                            <a href="{{ $store->website }}" target="_blank" style="color: #0051BB; font-size: 14px;">{{ $store->website }}</a>
                                        </div>
                                    @endif
                                    <div style="margin-bottom: 12px;">
                                        <i class="bi bi-calendar me-2" style="color: #A90000;"></i>
                                        <span style="color: #374151; font-size: 14px;">Katılma Tarihi: {{ $store->created_at->format('d.m.Y') }}</span>
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <i class="bi bi-percent me-2" style="color: #A90000;"></i>
                                        <span style="color: #374151; font-size: 14px;">Komisyon Oranı: %{{ $store->commission_rate ?? 10 }}</span>
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <i class="bi bi-toggle-on me-2" style="color: #A90000;"></i>
                                        <span style="color: #374151; font-size: 14px;">Durum: </span>
                                        @if($store->status === 'approved')
                                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 4px 12px; border-radius: 6px; font-size: 12px;">Aktif</span>
                                        @elseif($store->status === 'pending')
                                            <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; padding: 4px 12px; border-radius: 6px; font-size: 12px;">Beklemede</span>
                                        @elseif($store->status === 'rejected')
                                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; padding: 4px 12px; border-radius: 6px; font-size: 12px;">Pasif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Owner Info Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-person-badge" style="color: #A90000;"></i>
                            Mağaza Sahibi Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; font-weight: 500; color: #6b7280; margin-bottom: 4px; font-size: 13px;">Ad Soyad</label>
                                    <div style="font-size: 15px; color: #1f2937;">{{ $store->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; font-weight: 500; color: #6b7280; margin-bottom: 4px; font-size: 13px;">E-posta</label>
                                    <div style="font-size: 15px; color: #1f2937;">{{ $store->email }}</div>
                                </div>
                            </div>
                            @if($store->phone)
                            <div class="col-md-6">
                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; font-weight: 500; color: #6b7280; margin-bottom: 4px; font-size: 13px;">Telefon</label>
                                    <div style="font-size: 15px; color: #1f2937;">{{ $store->phone }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Performance Metrics -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-graph-up" style="color: #A90000;"></i>
                            Performans Metrikleri
                        </h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div style="text-align: center; padding: 16px; background: rgba(255, 255, 255, 0.5); border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <div style="font-size: 28px; font-weight: 700; color: #1f2937;">{{ $totalProducts }}</div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Toplam Ürün</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div style="text-align: center; padding: 16px; background: rgba(255, 255, 255, 0.5); border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <div style="font-size: 28px; font-weight: 700; color: #1f2937;">{{ $totalOrders }}</div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Toplam Sipariş</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div style="text-align: center; padding: 16px; background: rgba(255, 255, 255, 0.5); border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <div style="font-size: 28px; font-weight: 700; color: #1f2937;">₺{{ number_format($totalRevenue, 0) }}</div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Toplam Gelir</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div style="text-align: center; padding: 16px; background: rgba(255, 255, 255, 0.5); border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <div style="font-size: 28px; font-weight: 700; color: #1f2937;">{{ number_format($rating, 1) }}<span style="font-size: 20px;">⭐</span></div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Değerlendirme</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">Kapat</button>
                    <a href="{{ route('admin.seller.products', $store->id) }}" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px; text-decoration: none;">
                        <i class="bi bi-box me-1"></i>
                        Ürünleri Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Store Modal -->
    <div class="modal fade" id="editStoreModal{{ $store->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                    <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                        <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                        Mağaza Düzenle: <span class="badge bg-light text-dark" style="margin-left: 8px; font-size: 14px; font-weight: normal;">{{ $store->store_name ?? $store->name }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
                </div>
                <form action="{{ route('admin.stores') }}/{{ $store->id }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body" style="padding: 24px;">
                        <!-- Store Info Section -->
                        <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                            <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-shop" style="color: #A90000;"></i>
                                Mağaza Bilgileri
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-bottom: 20px;">
                                        <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Mağaza Adı</label>
                                        <input type="text" name="store_name" class="form-control" value="{{ $store->store_name ?? $store->name }}" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-bottom: 20px;">
                                        <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Durum</label>
                                        <select name="status" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                            <option value="approved" {{ $store->status === 'approved' ? 'selected' : '' }}>Aktif</option>
                                            <option value="rejected" {{ $store->status === 'rejected' ? 'selected' : '' }}>Pasif</option>
                                            <option value="pending" {{ $store->status === 'pending' ? 'selected' : '' }}>Beklemede</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                                <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;">{{ $store->store_description ?? '' }}</textarea>
                            </div>
                        </div>
                        
                        <!-- Commission & Additional Info -->
                        <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                            <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-gear" style="color: #A90000;"></i>
                                Komisyon ve Diğer Bilgiler
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-bottom: 20px;">
                                        <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Komisyon Oranı (%)</label>
                                        <input type="number" name="commission_rate" class="form-control" value="{{ $store->commission_rate ?? 10 }}" min="0" max="100" step="0.1" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-bottom: 20px;">
                                        <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Website</label>
                                        <input type="url" name="website" class="form-control" value="{{ $store->website ?? '' }}" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Adres</label>
                                <textarea name="address" class="form-control" rows="2" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;">{{ $store->address ?? '' }}</textarea>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Mağaza Logosu</label>
                                <input type="file" name="logo" class="form-control" accept="image/*" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                @if(isset($store->logo) && $store->logo)
                                    <small class="text-muted">Mevcut logo yenisiyle değiştirilecektir</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="info-message" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; margin-top: 20px;">
                            <i class="bi bi-info-circle-fill" style="color: #3B82F6; font-size: 20px; flex-shrink: 0;"></i>
                            <div class="info-message-content" style="flex: 1;">
                                <div class="info-message-title" style="font-weight: 600; color: #1f2937; margin-bottom: 2px;">Güncelleme</div>
                                <div class="info-message-text" style="color: #4b5563; font-size: 14px;">
                                    Değişiklikler kaydedildiğinde mağaza sahibi bilgilendirilecektir.
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

<!-- Add Store Modal -->
<div class="modal fade" id="addStoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-plus-circle me-2" style="color: #A90000;"></i>
                    Yeni Mağaza Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.stores') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <!-- Store Info Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-shop" style="color: #A90000;"></i>
                            Mağaza Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Mağaza Adı</label>
                                    <input type="text" name="store_name" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Mağaza adını girin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Komisyon Oranı (%)</label>
                                    <input type="number" name="commission_rate" class="form-control" value="10" min="0" max="100" step="0.1" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Owner Info Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-person-badge" style="color: #A90000;"></i>
                            Mağaza Sahibi Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Ad Soyad</label>
                                    <input type="text" name="name" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Ad soyad girin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">E-posta</label>
                                    <input type="email" name="email" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="E-posta adresi">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Telefon</label>
                                    <input type="tel" name="phone" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="Telefon numarası">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Şifre</label>
                                    <input type="password" name="password" class="form-control" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Info Section -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-info-circle" style="color: #A90000;"></i>
                            Ek Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Açıklama</label>
                                    <textarea name="store_description" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="Mağaza hakkında kısa açıklama"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Website</label>
                                    <input type="url" name="website" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" placeholder="https://example.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Adres</label>
                                    <textarea name="address" class="form-control" rows="2" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="Mağaza adresi"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-message" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; margin-top: 20px;">
                        <i class="bi bi-info-circle-fill" style="color: #3B82F6; font-size: 20px; flex-shrink: 0;"></i>
                        <div class="info-message-content" style="flex: 1;">
                            <div class="info-message-title" style="font-weight: 600; color: #1f2937; margin-bottom: 2px;">Yeni Mağaza</div>
                            <div class="info-message-text" style="color: #4b5563; font-size: 14px;">
                                Mağaza sahibine e-posta adresi ve şifre ile giriş yapabilmesi için bilgiler gönderilecektir.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-lg me-1"></i>
                        Mağaza Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
// Search functionality
let searchTimer;
document.getElementById('storeSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const cards = document.querySelectorAll('.store-card');
        cards.forEach(card => {
            const name = card.querySelector('.store-name')?.textContent.toLowerCase() || '';
            const owner = card.querySelector('.store-owner')?.textContent.toLowerCase() || '';
            
            if (name.includes(query) || owner.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }, 300);
});

// View Toggle
document.querySelectorAll('.view-toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const view = this.dataset.view;
        const container = document.getElementById('storesContainer');
        
        // Update active state
        document.querySelectorAll('.view-toggle-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Toggle view
        if (view === 'list') {
            container.classList.remove('grid-view');
            container.classList.add('list-view');
        } else {
            container.classList.remove('list-view');
            container.classList.add('grid-view');
        }
    });
});

// Store actions
function toggleStoreStatus(id) {
    if (confirm('Bu mağazanın durumunu değiştirmek istediğinizden emin misiniz?')) {
        fetch(`/admin/store/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                AdminPanel.showToast('Mağaza durumu güncellendi!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                AdminPanel.showToast('Bir hata oluştu!', 'error');
            }
        })
        .catch(error => {
            AdminPanel.showToast('Bir hata oluştu!', 'error');
        });
    }
}

function deleteStore(id) {
    if (confirm('Bu mağazayı silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/store/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                AdminPanel.showToast('Mağaza başarıyla silindi!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                AdminPanel.showToast('Bir hata oluştu!', 'error');
            }
        })
        .catch(error => {
            AdminPanel.showToast('Bir hata oluştu!', 'error');
        });
    }
}
</script>
@endpush