{{-- resources/views/seller/coupons/index.blade.php --}}
@extends('seller.layout') {{-- veya seller panelinizin layout’u, örn. layouts.sellerLayout --}}

@section('title', 'Seller Coupon Management')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title"><h4>Kuponlarım</h4></div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Anasayfa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kuponlar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addCouponModal">
                        + Yeni Kupon Ekle
                    </button>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Coupon List -->
        <div class="card-box mb-30">
            <div class="pd-20"><h4 class="text-blue h4">Kupon Listesi</h4></div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Kod</th><th>Tip</th><th>Değer</th><th>Min Sipariş</th>
                            <th>Kullanım (Kullanıldı/Limit)</th><th>Süre Bitişi</th><th>Durum</th><th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $coupon->code }}</td>
                            <td>{{ ucfirst($coupon->type) }}</td>
                            <td>
                                @if($coupon->type==='percent') {{ $coupon->value }}%
                                @elseif($coupon->type==='fixed') ₺{{ number_format($coupon->value,2) }}
                                @else Free Shipping
                                @endif
                            </td>
                            <td>
                                {{ $coupon->min_order_amount 
                                    ? '₺'.number_format($coupon->min_order_amount,2) 
                                    : '-' }}
                            </td>
                            <td>
                                {{ $coupon->used_count }}
                                @if($coupon->usage_limit)/ {{ $coupon->usage_limit }}@endif
                            </td>
                            <td>{{ $coupon->expires_at?->format('d.m.Y') ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $coupon->active?'badge-success':'badge-danger' }}">
                                    {{ $coupon->active?'Aktif':'Pasif' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link p-0 dropdown-toggle" href="#" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <!-- Edit -->
                                        <a class="dropdown-item" data-toggle="modal" data-target="#editCouponModal{{ $coupon->id }}" href="#">
                                            <i class="dw dw-edit2"></i> Düzenle
                                        </a>
                                        <!-- Toggle -->
                                        <a class="dropdown-item" href="#"
                                           onclick="event.preventDefault(); document.getElementById('toggle-coupon-{{ $coupon->id }}').submit();">
                                            <i class="dw {{ $coupon->active?'dw-ban':'dw-check' }}"></i>
                                            {{ $coupon->active?'Devre Dışı Bırak':'Devreye Al' }}
                                        </a>
                                        <form id="toggle-coupon-{{ $coupon->id }}"
                                              action="{{ route('seller.coupons.toggle', $coupon->id) }}"
                                              method="POST" style="display:none;">
                                            @csrf @method('PUT')
                                        </form>
                                        <!-- Delete -->
                                        <a class="dropdown-item text-danger" href="#"
                                           onclick="event.preventDefault(); document.getElementById('delete-coupon-{{ $coupon->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Sil
                                        </a>
                                        <form id="delete-coupon-{{ $coupon->id }}"
                                              action="{{ route('seller.coupons.destroy', $coupon->id) }}"
                                              method="POST" style="display:none;">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Coupon Modal -->
                        <div class="modal fade" id="editCouponModal{{ $coupon->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Kupon Düzenle: {{ $coupon->code }}</h4>
                                        <button type="button" class="close" data-dismiss="modal">×</button>
                                    </div>
                                    <form action="{{ route('seller.coupons.update',$coupon->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Kod</label>
                                                    <input type="text" name="code" class="form-control"
                                                           value="{{ $coupon->code }}" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Tip</label>
                                                    <select name="type" class="form-control" required>
                                                        <option value="fixed"      {{ $coupon->type=='fixed'?'selected':'' }}>Sabit</option>
                                                        <option value="percent"    {{ $coupon->type=='percent'?'selected':'' }}>Yüzde</option>
                                                        <option value="free_shipping" {{ $coupon->type=='free_shipping'?'selected':'' }}>Ücretsiz Kargo</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Değer</label>
                                                    <input type="number" step="0.01" min="0" name="value"
                                                           class="form-control" value="{{ $coupon->value }}" required>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Min Sipariş</label>
                                                    <input type="number" step="0.01" min="0"
                                                           name="min_order_amount" class="form-control"
                                                           value="{{ $coupon->min_order_amount }}">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Kullanım Limit</label>
                                                    <input type="number" min="1" name="usage_limit"
                                                           class="form-control" value="{{ $coupon->usage_limit }}">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Süre Bitişi</label>
                                                    <input type="date" name="expires_at" class="form-control"
                                                           value="{{ $coupon->expires_at?->format('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Durum</label>
                                                <select name="active" class="form-control" required>
                                                    <option value="1" {{ $coupon->active?'selected':'' }}>Aktif</option>
                                                    <option value="0" {{ !$coupon->active?'selected':'' }}>Pasif</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Ürünler</label>
                                                <select name="product_ids[]" class="form-control" multiple>
                                                    @php $sel = $coupon->products->pluck('id')->toArray(); @endphp
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ in_array($p->id,$sel)?'selected':'' }}>
                                                            {{ $p->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Kaydet</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /Edit Modal -->

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Coupon List -->

    </div>
</div>

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Yeni Kupon Ekle</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form action="{{ route('seller.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Kod</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tip</label>
                            <select name="type" class="form-control" required>
                                <option value="fixed">Sabit</option>
                                <option value="percent">Yüzde</option>
                                <option value="free_shipping">Ücretsiz Kargo</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Değer</label>
                            <input type="number" step="0.01" min="0" name="value" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Min Sipariş</label>
                            <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Kullanım Limit</label>
                            <input type="number" min="1" name="usage_limit" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Süre Bitişi</label>
                            <input type="date" name="expires_at" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Durum</label>
                        <select name="active" class="form-control" required>
                            <option value="1" selected>Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ürünler</label>
                        <select name="product_ids[]" class="form-control" multiple>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Ekle</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
