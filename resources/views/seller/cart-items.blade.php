@extends('seller.layout')

@section('title', 'Sepet Öğeleri')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="title">
                            <h4 class="mb-2">Sepet Öğeleri</h4>
                            <p class="text-muted mb-0">Müşterilerin sepete eklediği ürünleriniz</p>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation" class="mt-2">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Anasayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sepet Öğeleri</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 text-md-right text-sm-left mt-md-0 mt-3">
                        <form action="{{ route('abandoned-cart.send-emails') }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-copy dw dw-email mr-2"></i> Hatırlatma E-postası Gönder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- En Son Sepete Eklenen Ürünler Tablosu -->
            <div class="card-box mb-30">
                <div class="pd-20 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="text-blue mb-0">En Son Sepete Eklenen Ürünler</h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">Toplam {{ $latestCartItems->total() }} kayıt bulundu</small>
                        </div>
                    </div>
                </div>
                <div class="pb-20">
                    <div class="table-responsive">
                        <table class="table table-hover data-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Ürün</th>
                                    <th>Görsel</th>
                                    <th>Fiyat</th>
                                    <th>Adet</th>
                                    <th>Müşteri</th>
                                    <th>Eklenme Tarihi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestCartItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('seller.products.details', $item->product->id) }}" class="text-primary">
                                            {{ $item->product->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($item->product->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}" width="50">
                                        @else
                                        <span class="badge badge-secondary">Görsel Yok</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->product->price, 2) }} TL</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Henüz sepete eklenen ürün bulunmuyor.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $latestCartItems->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- En Çok Sepete Eklenen Ürünler Kartları -->
            <div class="row">
                @forelse($topCartProducts as $item)
                <div class="col-xl-4 col-lg-4 col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex flex-wrap justify-content-between align-items-center pb-0 pb-md-3">
                            <div class="h5 mb-md-0">{{ $item->product->name }}</div>
                            <div class="font-14 font-weight-medium">{{ $item->cart_count }} kez sepete eklendi</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="mr-2">
                                @if($item->product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}" class="img-fluid" style="max-height: 150px; width: auto;">
                                @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px; width: 150px;">
                                    <span class="text-muted">Görsel Yok</span>
                                </div>
                                @endif
                            </div>
                            <div>
                                <h5>Fiyat: {{ number_format($item->product->price, 2) }} TL</h5>
                                <p>Stok: {{ $item->product->stock }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6>Sepete Ekleyen Son 5 Kullanıcı:</h6>
                            <ul class="list-group">
                                @if(isset($productUsers[$item->product_id]) && count($productUsers[$item->product_id]) > 0)
                                    @foreach($productUsers[$item->product_id] as $user)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        {{ $user->name }}
                                        <span class="badge badge-primary badge-pill">{{ $user->email }}</span>
                                    </li>
                                    @endforeach
                                @else
                                    <li class="list-group-item">Kullanıcı bilgisi bulunamadı</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card-box p-4 text-center">
                        <h5>Henüz sepete eklenen ürün bulunmuyor.</h5>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.data-table').DataTable({
            scrollCollapse: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "info": "_START_-_END_ / _TOTAL_ kayıt",
                "search": "Ara:",
                "paginate": {
                    "next": "Sonraki",
                    "previous": "Önceki"
                }
            },
        });
    });
</script>
@endsection 