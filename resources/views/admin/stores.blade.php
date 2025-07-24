@extends('layouts.layout')

@section('title', 'Mağaza Yönetimi')

@section('content')
    <!-- CSRF Token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Mağaza Yönetimi</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Mağazalar</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Duruma Göre Filtrele
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.stores') }}">Tüm Mağazalar</a>
                                <a class="dropdown-item" href="{{ route('admin.stores', ['status' => 'approved']) }}">Aktif</a>
                                <a class="dropdown-item" href="{{ route('admin.stores', ['status' => 'rejected']) }}">Pasif</a>
                                <a class="dropdown-item" href="{{ route('admin.stores', ['status' => 'pending']) }}">Onay Bekleyen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Statistics Cards -->
            <div class="row mb-30">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card-box pd-20 height-100-p">
                        <div class="d-flex justify-content-between">
                            <div class="h2 text-blue">{{ $stores->count() }}</div>
                            <div class="font-24 text-right text-blue">
                                <i class="dw dw-store"></i>
                            </div>
                        </div>
                        <div class="font-14 text-secondary font-weight-medium">Toplam Mağaza</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card-box pd-20 height-100-p">
                        <div class="d-flex justify-content-between">
                            <div class="h2 text-green">{{ $stores->where('status', 'approved')->count() }}</div>
                            <div class="font-24 text-right text-green">
                                <i class="dw dw-check"></i>
                            </div>
                        </div>
                        <div class="font-14 text-secondary font-weight-medium">Aktif Mağazalar</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card-box pd-20 height-100-p">
                        <div class="d-flex justify-content-between">
                            <div class="h2 text-orange">{{ $stores->where('status', 'pending')->count() }}</div>
                            <div class="font-24 text-right text-orange">
                                <i class="dw dw-clock"></i>
                            </div>
                        </div>
                        <div class="font-14 text-secondary font-weight-medium">Onay Bekleyen</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card-box pd-20 height-100-p">
                        <div class="d-flex justify-content-between">
                            <div class="h2 text-red">{{ $stores->where('status', 'rejected')->count() }}</div>
                            <div class="font-24 text-right text-red">
                                <i class="dw dw-ban"></i>
                            </div>
                        </div>
                        <div class="font-14 text-secondary font-weight-medium">Pasif Mağazalar</div>
                    </div>
                </div>
            </div>

            <!-- Stores Table -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Mağaza Listesi</h4>
                    <p class="text-muted">Tüm satıcı mağazalarını yönet</p>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th class="table-plus">#</th>
                                <th>Mağaza Bilgisi</th>
                                <th>Sahip Detayları</th>
                                <th>Ürünler</th>
                                <th>Performans</th>
                                <th>Durum</th>
                                <th>Oluşturulma</th>
                                <th class="datatable-nosort">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                <tr>
                                    <td class="table-plus">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="store-info">
                                            @if(isset($store->logo) && $store->logo)
                                                <img src="{{ asset('storage/' . $store->logo) }}" 
                                                     alt="Store Logo" class="store-logo mr-3">
                                            @else
                                                <div class="store-logo-placeholder mr-3">
                                                    <i class="dw dw-store"></i>
                                                </div>
                                            @endif
                                            <div class="store-details">
                                                <strong class="store-name">{{ $store->store_name ?? $store->name }}</strong>
                                                @if(isset($store->store_description) && $store->store_description)
                                                    <br><small class="text-muted">{{ Str::limit($store->store_description, 50) }}</small>
                                                @endif
                                                @if(isset($store->website) && $store->website)
                                                    <br><small><a href="{{ $store->website }}" target="_blank" class="text-primary">{{ $store->website }}</a></small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="owner-info">
                                            <strong>{{ $store->name }}</strong>
                                            <br><small class="text-muted">{{ $store->email }}</small>
                                            @if($store->phone)
                                                <br><small class="text-muted">{{ $store->phone }}</small>
                                            @endif
                                            <br><span class="badge {{ $store->status === 'approved' ? 'badge-success' : ($store->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                                @if($store->status === 'approved')
                                                    Onaylandı
                                                @elseif($store->status === 'pending')
                                                    Beklemede
                                                @elseif($store->status === 'rejected')
                                                    Reddedildi
                                                @else
                                                    {{ ucfirst($store->status) }}
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-stats">
                                            <div class="stat-item">
                                                <strong>{{ $totalProducts }}</strong>
                                                <small class="text-muted d-block">Ürünler</small>
                                            </div>
                                            <div class="stat-item">
                                                <strong>{{ $activeProducts }}</strong>
                                                <small class="text-muted d-block">Aktif</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="performance-stats">
                                            <div class="stat-item">
                                                <strong>₺{{ number_format($totalRevenue, 0) }}</strong>
                                                <small class="text-muted d-block">Gelir</small>
                                            </div>
                                            <div class="stat-item">
                                                <strong>{{ $totalOrders }}</strong>
                                                <small class="text-muted d-block">Sipariş</small>
                                            </div>
                                            <div class="stat-item">
                                                <div class="rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fa fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <small class="text-muted d-block">{{ number_format($rating, 1) }} Değerlendirme</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @switch($store->status)
                                                @case('approved') badge-success @break
                                                @case('rejected') badge-danger @break
                                                @case('pending') badge-warning @break
                                                @default badge-secondary
                                            @endswitch
                                        ">
                                            @if($store->status === 'approved')
                                                Onaylandı
                                            @elseif($store->status === 'pending')
                                                Beklemede
                                            @elseif($store->status === 'rejected')
                                                Reddedildi
                                            @else
                                                {{ ucfirst($store->status) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $store->created_at->format('d M Y') }}</small>
                                        <br><small class="text-muted">{{ $store->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <a class="dropdown-item" data-toggle="modal"
                                                    data-target="#viewStoreModal{{ $store->id }}" href="#">
                                                    <i class="dw dw-eye"></i> Detayları Görüntüle
                                                </a>
                                                <a class="dropdown-item" 
                                                    href="{{ route('admin.seller.products', $store->id) }}">
                                                    <i class="dw dw-box"></i> Ürünleri Görüntüle
                                                </a>
                                                <a class="dropdown-item" data-toggle="modal"
                                                    data-target="#editStoreModal{{ $store->id }}" href="#">
                                                    <i class="dw dw-edit2"></i> Mağaza Düzenle
                                                </a>
                                                @if($store->status === 'approved')
                                                    <a class="dropdown-item" href="#"
                                                        onclick="toggleStoreStatus({{ $store->id }})">
                                                        <i class="dw dw-ban"></i> Pasifleştir
                                                    </a>
                                                @else
                                                    <a class="dropdown-item" href="#"
                                                        onclick="toggleStoreStatus({{ $store->id }})">
                                                        <i class="dw dw-check"></i> Aktifleştir
                                                    </a>
                                                @endif
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="deleteStore({{ $store->id }})">
                                                    <i class="dw dw-delete-3"></i> Sil
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- View Store Details Modal -->
                                <div class="modal fade" id="viewStoreModal{{ $store->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="viewStoreModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h4 class="modal-title">Mağaza Detayları - {{ $store->store_name ?? $store->name }}</h4>
                                                <button type="button" class="close text-white" data-dismiss="modal"
                                                    aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="store-profile">
                                                            @if(isset($store->logo) && $store->logo)
                                                                <img src="{{ asset('storage/' . $store->logo) }}" 
                                                                     alt="Store Logo" class="store-logo-large mb-3">
                                                            @endif
                                                            <h5>{{ $store->store_name ?? $store->name }}</h5>
                                                            @if(isset($store->store_description) && $store->store_description)
                                                                <p class="text-muted">{{ $store->store_description }}</p>
                                                            @endif
                                                            @if($store->address)
                                                                <p><i class="dw dw-location mr-2"></i>{{ $store->address }}</p>
                                                            @endif
                                                            @if(isset($store->website) && $store->website)
                                                                <p><i class="dw dw-world mr-2"></i>
                                                                    <a href="{{ $store->website }}" target="_blank">{{ $store->website }}</a>
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="owner-profile">
                                                            <h6>Sahip Bilgileri</h6>
                                                            <div class="info-item">
                                                                <span class="info-label">İsim:</span>
                                                                <span class="info-value">{{ $store->name }}</span>
                                                            </div>
                                                            <div class="info-item">
                                                                <span class="info-label">E-posta:</span>
                                                                <span class="info-value">{{ $store->email }}</span>
                                                            </div>
                                                            @if($store->phone)
                                                                <div class="info-item">
                                                                    <span class="info-label">Telefon:</span>
                                                                    <span class="info-value">{{ $store->phone }}</span>
                                                                </div>
                                                            @endif
                                                            <div class="info-item">
                                                                <span class="info-label">Durum:</span>
                                                                <span class="badge {{ $store->status === 'approved' ? 'badge-success' : ($store->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                                                    @if($store->status === 'approved')
                                                                        Onaylandı
                                                                    @elseif($store->status === 'pending')
                                                                        Beklemede
                                                                    @elseif($store->status === 'rejected')
                                                                        Reddedildi
                                                                    @else
                                                                        {{ ucfirst($store->status) }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div class="info-item">
                                                                <span class="info-label">Komisyon Oranı:</span>
                                                                <span class="info-value">{{ $store->commission_rate ?? 10 }}%</span>
                                                            </div>
                                                            <div class="info-item">
                                                                <span class="info-label">Katılma:</span>
                                                                <span class="info-value">{{ $store->created_at->format('d M Y') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Performance Metrics -->
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <h6>Performans Metrikleri</h6>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="metric-card">
                                                                    <div class="metric-value">{{ $totalProducts }}</div>
                                                                    <div class="metric-label">Toplam Ürün</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="metric-card">
                                                                    <div class="metric-value">{{ $totalOrders }}</div>
                                                                    <div class="metric-label">Toplam Sipariş</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="metric-card">
                                                                    <div class="metric-value">₺{{ number_format($totalRevenue, 0) }}</div>
                                                                    <div class="metric-label">Gelir</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="metric-card">
                                                                    <div class="metric-value">{{ number_format($rating, 1) }} ⭐</div>
                                                                    <div class="metric-label">Değerlendirme</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                                <a href="{{ route('admin.seller.products', $store->id) }}" class="btn btn-primary">
                                                    Ürünleri Görüntüle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Store Modal -->
                                <div class="modal fade" id="editStoreModal{{ $store->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="editStoreModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h4 class="modal-title">Mağaza Düzenle</h4>
                                                <button type="button" class="close text-white" data-dismiss="modal"
                                                    aria-hidden="true">×</button>
                                            </div>
                                            <form action="{{ route('admin.store.update', $store->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="storeName{{ $store->id }}">Mağaza Adı</label>
                                                                <input type="text" name="store_name" id="storeName{{ $store->id }}"
                                                                    class="form-control" value="{{ $store->store_name ?? $store->name }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="storeStatus{{ $store->id }}">Durum</label>
                                                                <select name="status" id="storeStatus{{ $store->id }}" class="form-control" required>
                                                                    <option value="approved" {{ $store->status === 'approved' ? 'selected' : '' }}>Aktif</option>
                                                                    <option value="rejected" {{ $store->status === 'rejected' ? 'selected' : '' }}>Pasif</option>
                                                                    <option value="pending" {{ $store->status === 'pending' ? 'selected' : '' }}>Beklemede</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="storeDescription{{ $store->id }}">Açıklama</label>
                                                        <textarea name="description" id="storeDescription{{ $store->id }}" 
                                                            class="form-control" rows="3">{{ $store->store_description ?? '' }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="storeCommission{{ $store->id }}">Komisyon Oranı (%)</label>
                                                                <input type="number" name="commission_rate" id="storeCommission{{ $store->id }}"
                                                                    class="form-control" value="{{ $store->commission_rate ?? 10 }}" 
                                                                    min="0" max="100" step="0.1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="storeWebsite{{ $store->id }}">Website</label>
                                                                <input type="url" name="website" id="storeWebsite{{ $store->id }}"
                                                                    class="form-control" value="{{ $store->website ?? '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="storeAddress{{ $store->id }}">Adres</label>
                                                        <textarea name="address" id="storeAddress{{ $store->id }}" 
                                                            class="form-control" rows="2">{{ $store->address ?? '' }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="storeLogo{{ $store->id }}">Mağaza Logosu</label>
                                                        <input type="file" name="logo" id="storeLogo{{ $store->id }}"
                                                            class="form-control-file" accept="image/*">
                                                        @if(isset($store->logo) && $store->logo)
                                                            <small class="text-muted">Mevcut logo yenisiyle değiştirilecektir</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                                    <button type="submit" class="btn btn-success">Mağaza Güncelle</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="dw dw-store text-muted mb-3" style="font-size: 48px;"></i>
                                            <h5 class="text-muted">Mağaza bulunamadı</h5>
                                            <p class="text-muted">Henüz satıcı mağazası oluşturulmamış.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .store-info {
            display: flex;
            align-items: center;
        }
        
        .store-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e9ecef;
        }
        
        .store-logo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e9ecef;
        }
        
        .store-logo-large {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e9ecef;
        }
        
        .store-name {
            color: #007bff;
            font-size: 14px;
        }
        
        .owner-info strong {
            color: #495057;
        }
        
        .product-stats, .performance-stats {
            display: flex;
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-item strong {
            font-size: 16px;
            color: #495057;
        }
        
        .rating .fa-star {
            font-size: 12px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        
        .info-value {
            color: #495057;
        }
        
        .metric-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }
        
        .metric-label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .badge {
            font-size: 11px;
            padding: 4px 8px;
        }
        
        .empty-state {
            padding: 40px 20px;
        }
        
        @media (max-width: 768px) {
            .product-stats, .performance-stats {
                flex-direction: column;
                gap: 10px;
            }
            
            .store-info {
                flex-direction: column;
                text-align: center;
            }
            
            .store-logo, .store-logo-placeholder {
                margin-right: 0 !important;
                margin-bottom: 10px;
            }
        }
    </style>

    <script>
        // Session Messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        function toggleStoreStatus(storeId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu mağazanın durumunu değiştirmek istiyor musunuz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, değiştir!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/store/${storeId}/toggle`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function deleteStore(storeId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu mağazayı ve tüm ilişkili verileri kalıcı olarak silecek!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/store/${storeId}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection