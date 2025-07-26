{{-- resources/views/admin/coupons/index.blade.php --}}
@extends('layouts.layout')

@section('title', 'Kupon Yönetimi')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Kupon Yönetimi</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kuponlar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addCouponModal">
                        + Yeni Kupon Ekle
                    </button>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Coupon List Card -->
        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Kupon Listesi</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kod</th>
                            <th>Tür</th>
                            <th>Değer</th>
                            <th>Min. Sipariş</th>
                            <th>Kullanım (Kullanılan/Limit)</th>
                            <th>Bitiş Tarihi</th>
                            <th>Durum</th>
                            <th class="datatable-nosort">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $coupon->code }}</td>
                            <td>
                                @if($coupon->type === 'fixed')
                                    Sabit
                                @elseif($coupon->type === 'percent')
                                    Yüzde
                                @elseif($coupon->type === 'free_shipping')
                                    Ücretsiz Kargo
                                @else
                                    {{ ucfirst($coupon->type) }}
                                @endif
                            </td>
                            <td>
                                @if($coupon->type === 'percent')
                                    {{ $coupon->value }}%
                                @elseif($coupon->type === 'fixed')
                                    ₺{{ number_format($coupon->value,2) }}
                                @else
                                    Ücretsiz Kargo
                                @endif
                            </td>
                            <td>
                                @if($coupon->min_order_amount !== null)
                                    ₺{{ number_format($coupon->min_order_amount,2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $coupon->used_count }}
                                @if($coupon->usage_limit)
                                    / {{ $coupon->usage_limit }}
                                @endif
                            </td>
                            <td>
                                @if($coupon->expires_at)
                                    {{ $coupon->expires_at->format('d.m.Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $coupon->active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $coupon->active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                       href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <!-- Edit -->
                                        <a class="dropdown-item" data-toggle="modal"
                                           data-target="#editCouponModal{{ $coupon->id }}" href="#">
                                            <i class="dw dw-edit2"></i> Düzenle
                                        </a>
                                        <!-- Toggle Active/Inactive -->
                                        <a class="dropdown-item" href="#"
                                           onclick="event.preventDefault(); document.getElementById('toggle-coupon-{{ $coupon->id }}').submit();">
                                            <i class="dw {{ $coupon->active ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $coupon->active ? 'Pasifleştir' : 'Aktifleştir' }}
                                        </a>
                                        <form id="toggle-coupon-{{ $coupon->id }}"
                                              action="{{ route('admin.coupons.toggle', $coupon->id) }}"
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <!-- Delete -->
                                        <a class="dropdown-item text-danger" href="#"
                                           onclick="event.preventDefault(); document.getElementById('delete-coupon-{{ $coupon->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Sil
                                        </a>
                                        <form id="delete-coupon-{{ $coupon->id }}"
                                              action="{{ route('admin.coupons.destroy', $coupon->id) }}"
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Coupon Modal -->
                        <div class="modal fade" id="editCouponModal{{ $coupon->id }}" tabindex="-1"
                             role="dialog" aria-labelledby="editCouponModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-gradient-primary text-white border-0">
                                        <h4 class="modal-title font-weight-bold">
                                            <i class="dw dw-coupon mr-2"></i>Kupon Düzenle: <span class="badge badge-light text-primary">{{ $coupon->code }}</span>
                                        </h4>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <!-- Temel Bilgiler -->
                                            <div class="form-section mb-4">
                                                <h6 class="text-primary font-weight-bold mb-3">
                                                    <i class="dw dw-settings mr-2"></i>Temel Bilgiler
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-key mr-1 text-info"></i>Kupon Kodu
                                                            </label>
                                                            <input type="text" name="code" class="form-control form-control-lg border-2"
                                                                   value="{{ $coupon->code }}" required placeholder="Örn: INDIRIM2024">
                                                            <small class="text-muted">Büyük/küçük harf duyarlı değil</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-tag mr-1 text-warning"></i>İndirim Türü
                                                            </label>
                                                            <select name="type" class="form-control form-control-lg border-2" required>
                                                                <option value="fixed" {{ $coupon->type=='fixed'?'selected':'' }}>
                                                                    💰 Sabit Tutar (₺)
                                                                </option>
                                                                <option value="percent" {{ $coupon->type=='percent'?'selected':'' }}>
                                                                    📊 Yüzde İndirim (%)
                                                                </option>
                                                                <option value="free_shipping" {{ $coupon->type=='free_shipping'?'selected':'' }}>
                                                                    🚚 Ücretsiz Kargo
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- İndirim Değeri ve Koşullar -->
                                            <div class="form-section mb-4">
                                                <h6 class="text-success font-weight-bold mb-3">
                                                    <i class="dw dw-money-2 mr-2"></i>İndirim Değeri ve Koşullar
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-discount mr-1 text-success"></i>İndirim Değeri
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" min="0" name="value"
                                                                       class="form-control form-control-lg border-2" 
                                                                       value="{{ $coupon->value }}" required placeholder="0.00">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text bg-light">
                                                                        <span class="coupon-currency">₺</span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-shopping-basket mr-1 text-orange"></i>Min. Sipariş Tutarı
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" min="0" name="min_order_amount"
                                                                       class="form-control form-control-lg border-2" 
                                                                       value="{{ $coupon->min_order_amount }}" placeholder="0.00">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text bg-light">₺</span>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">Boş bırakılırsa sınır yok</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-counter mr-1 text-danger"></i>Kullanım Limiti
                                                            </label>
                                                            <input type="number" min="1" name="usage_limit"
                                                                   class="form-control form-control-lg border-2" 
                                                                   value="{{ $coupon->usage_limit }}" placeholder="Sınırsız">
                                                            <small class="text-muted">Boş bırakılırsa sınırsız</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tarih ve Durum -->
                                            <div class="form-section mb-4">
                                                <h6 class="text-warning font-weight-bold mb-3">
                                                    <i class="dw dw-calendar mr-2"></i>Geçerlilik ve Durum
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-time mr-1 text-danger"></i>Son Kullanım Tarihi
                                                            </label>
                                                            <input type="date" name="expires_at" class="form-control form-control-lg border-2"
                                                                   value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '' }}">
                                                            <small class="text-muted">Boş bırakılırsa süresiz</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-semibold text-dark">
                                                                <i class="dw dw-power mr-1"></i>Kupon Durumu
                                                            </label>
                                                            <select name="active" class="form-control form-control-lg border-2" required>
                                                                <option value="1" {{ $coupon->active ? 'selected' : '' }}>
                                                                    ✅ Aktif
                                                                </option>
                                                                <option value="0" {{ !$coupon->active ? 'selected' : '' }}>
                                                                    ❌ Pasif
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ürün Seçimi -->
                                            <div class="form-section">
                                                <h6 class="text-info font-weight-bold mb-3">
                                                    <i class="dw dw-box mr-2"></i>Geçerli Ürünler
                                                </h6>
                                                <div class="form-group">
                                                    <label class="font-weight-semibold text-dark">
                                                        <i class="dw dw-list mr-1 text-primary"></i>Ürün Seçimi
                                                        <small class="text-muted ml-2">(Ctrl tuşu ile çoklu seçim yapabilirsiniz)</small>
                                                    </label>
                                                    <select name="product_ids[]" class="form-control border-2" multiple style="min-height: 150px;">
                                                        @php
                                                            $sel = $coupon->products->pluck('id')->toArray();
                                                        @endphp
                                                        @foreach($products as $p)
                                                            <option value="{{ $p->id }}"
                                                                {{ in_array($p->id, $sel) ? 'selected' : '' }}
                                                                class="p-2">
                                                                {{ $p->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">
                                                        <i class="dw dw-info mr-1"></i>
                                                        Hiç ürün seçilmezse kupon tüm ürünler için geçerli olur
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Mevcut Kullanım Bilgisi -->
                                            <div class="alert alert-info border-0 mt-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="dw dw-info text-info mr-2" style="font-size: 18px;"></i>
                                                    <div>
                                                        <strong>Kullanım Bilgisi:</strong> 
                                                        Bu kupon şu ana kadar <span class="badge badge-primary">{{ $coupon->used_count }}</span> kez kullanıldı
                                                        @if($coupon->usage_limit)
                                                            (Limit: {{ $coupon->usage_limit }})
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0 justify-content-between">
                                            <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                                                <i class="dw dw-cancel mr-2"></i>İptal
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                                <i class="dw dw-save mr-2"></i>Değişiklikleri Kaydet
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End Edit Coupon Modal -->

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- End Coupon List Card -->

    </div>
</div>

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" role="dialog"
     aria-labelledby="addCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white border-0">
                <h4 class="modal-title font-weight-bold">
                    <i class="dw dw-add mr-2"></i>Yeni Kupon Oluştur
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Temel Bilgiler -->
                    <div class="form-section mb-4">
                        <h6 class="text-primary font-weight-bold mb-3">
                            <i class="dw dw-settings mr-2"></i>Temel Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-key mr-1 text-info"></i>Kupon Kodu
                                    </label>
                                    <input type="text" name="code" class="form-control form-control-lg border-2" 
                                           required placeholder="Örn: INDIRIM2024">
                                    <small class="text-muted">Büyük/küçük harf duyarlı değil</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-tag mr-1 text-warning"></i>İndirim Türü
                                    </label>
                                    <select name="type" class="form-control form-control-lg border-2" required>
                                        <option value="fixed">💰 Sabit Tutar (₺)</option>
                                        <option value="percent">📊 Yüzde İndirim (%)</option>
                                        <option value="free_shipping">🚚 Ücretsiz Kargo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İndirim Değeri ve Koşullar -->
                    <div class="form-section mb-4">
                        <h6 class="text-success font-weight-bold mb-3">
                            <i class="dw dw-money-2 mr-2"></i>İndirim Değeri ve Koşullar
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-discount mr-1 text-success"></i>İndirim Değeri
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="value" 
                                               class="form-control form-control-lg border-2" required placeholder="0.00">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light">
                                                <span class="coupon-currency">₺</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-shopping-basket mr-1 text-orange"></i>Min. Sipariş Tutarı
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="min_order_amount" 
                                               class="form-control form-control-lg border-2" placeholder="0.00">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light">₺</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Boş bırakılırsa sınır yok</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-counter mr-1 text-danger"></i>Kullanım Limiti
                                    </label>
                                    <input type="number" min="1" name="usage_limit" 
                                           class="form-control form-control-lg border-2" placeholder="Sınırsız">
                                    <small class="text-muted">Boş bırakılırsa sınırsız</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarih ve Durum -->
                    <div class="form-section mb-4">
                        <h6 class="text-warning font-weight-bold mb-3">
                            <i class="dw dw-calendar mr-2"></i>Geçerlilik ve Durum
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-time mr-1 text-danger"></i>Son Kullanım Tarihi
                                    </label>
                                    <input type="date" name="expires_at" class="form-control form-control-lg border-2">
                                    <small class="text-muted">Boş bırakılırsa süresiz</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold text-dark">
                                        <i class="dw dw-power mr-1"></i>Kupon Durumu
                                    </label>
                                    <select name="active" class="form-control form-control-lg border-2" required>
                                        <option value="1" selected>✅ Aktif</option>
                                        <option value="0">❌ Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ürün Seçimi -->
                    <div class="form-section">
                        <h6 class="text-info font-weight-bold mb-3">
                            <i class="dw dw-box mr-2"></i>Geçerli Ürünler
                        </h6>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-list mr-1 text-primary"></i>Ürün Seçimi
                                <small class="text-muted ml-2">(Ctrl tuşu ile çoklu seçim yapabilirsiniz)</small>
                            </label>
                            <select name="product_ids[]" class="form-control border-2" multiple style="min-height: 150px;">
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" class="p-2">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="dw dw-info mr-1"></i>
                                Hiç ürün seçilmezse kupon tüm ürünler için geçerli olur
                            </small>
                        </div>
                    </div>

                    <!-- Bilgilendirme -->
                    <div class="alert alert-success border-0 mt-3">
                        <div class="d-flex align-items-center">
                            <i class="dw dw-lightbulb text-success mr-2" style="font-size: 18px;"></i>
                            <div>
                                <strong>İpucu:</strong> 
                                Kupon oluşturduktan sonra müşterilerinizle paylaşabilir ve kullanım durumunu takip edebilirsiniz.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 justify-content-between">
                    <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                        <i class="dw dw-cancel mr-2"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="dw dw-add mr-2"></i>Kupon Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
/* Modal Geliştirmeleri */
.modal-xl {
    max-width: 1000px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

.form-control-lg {
    height: calc(2.5rem + 2px);
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.border-2 {
    border-width: 2px !important;
    transition: all 0.3s ease;
}

.border-2:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.input-group-text {
    font-weight: 600;
    border-width: 2px;
    border-left: 0;
}

.font-weight-semibold {
    font-weight: 600;
}

.alert {
    border-radius: 10px;
}

/* Select Multiple Styling */
select[multiple] option {
    padding: 8px 12px;
    margin: 2px 0;
    border-radius: 4px;
}

select[multiple] option:checked {
    background: #007bff !important;
    color: white !important;
}

/* Badge ve Button Geliştirmeleri */
.badge-light {
    background-color: rgba(255,255,255,0.9) !important;
    border: 1px solid rgba(0,0,0,0.1);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Icon Colors */
.text-orange {
    color: #fd7e14 !important;
}

/* Form Section Headers */
.form-section h6 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

/* Modal Shadow */
.modal-content {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
    
    .form-section {
        padding: 15px;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kupon türü değiştiğinde currency symbol'ü güncelle
    function updateCurrencySymbol(selectElement) {
        const modal = selectElement.closest('.modal');
        const currencySpan = modal.querySelector('.coupon-currency');
        const valueInput = modal.querySelector('input[name="value"]');
        
        if (currencySpan && valueInput) {
            switch(selectElement.value) {
                case 'percent':
                    currencySpan.textContent = '%';
                    valueInput.setAttribute('max', '100');
                    valueInput.setAttribute('placeholder', '0-100');
                    break;
                case 'fixed':
                    currencySpan.textContent = '₺';
                    valueInput.removeAttribute('max');
                    valueInput.setAttribute('placeholder', '0.00');
                    break;
                case 'free_shipping':
                    currencySpan.textContent = '🚚';
                    valueInput.value = '0';
                    valueInput.setAttribute('readonly', true);
                    valueInput.setAttribute('placeholder', 'Otomatik');
                    break;
                default:
                    currencySpan.textContent = '₺';
                    valueInput.removeAttribute('readonly');
                    valueInput.setAttribute('placeholder', '0.00');
            }
        }
    }

    // Tüm type select'leri için event listener ekle
    document.querySelectorAll('select[name="type"]').forEach(function(select) {
        // Sayfa yüklendiğinde mevcut değerlere göre güncelle
        updateCurrencySymbol(select);
        
        // Değişiklik olduğunda güncelle
        select.addEventListener('change', function() {
            updateCurrencySymbol(this);
        });
    });

    // Form validation feedback
    document.querySelectorAll('.border-2').forEach(function(input) {
        input.addEventListener('invalid', function() {
            this.style.borderColor = '#dc3545';
        });
        
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '#dc3545';
            }
        });
    });
});
</script>