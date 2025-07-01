@extends('layouts.layout')

@section('title', 'Blog Yönetimi')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Blog Yönetimi</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Blog</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Yeni Blog Ekle
                    </a>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Blog Listesi</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Görsel</th>
                            <th>Başlık</th>
                            <th>Yazar</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th class="datatable-nosort">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $blogs = \App\Models\Blog::latest()->get();
                        @endphp
                        @foreach ($blogs as $blog)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($blog->blog_img)
                                    <img src="{{ asset('storage/' . $blog->blog_img) }}" alt="Blog Görseli" width="80" height="60" style="object-fit: cover; border-radius: 4px;">
                                @else
                                    <span class="text-muted">Görsel Yok</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($blog->title, 40) }}</td>
                            <td>{{ $blog->author }}</td>
                            <td>{{ $blog->date->format('d.m.Y') }}</td>
                            <td>
                                <span class="badge {{ $blog->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $blog->status ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" href="{{ route('admin.blogs.edit', $blog->id) }}">
                                            <i class="dw dw-edit2"></i> Düzenle
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('toggle-status-{{ $blog->id }}').submit();">
                                            <i class="dw {{ $blog->status ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $blog->status ? 'Pasif Yap' : 'Aktif Yap' }}
                                        </a>
                                        <form id="toggle-status-{{ $blog->id }}" action="{{ route('admin.blogs.change-status') }}" method="POST" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $blog->id }}">
                                            <input type="hidden" name="status" value="{{ $blog->status ? 0 : 1 }}">
                                        </form>
                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('delete-blog-{{ $blog->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Sil
                                        </a>
                                        <form id="delete-blog-{{ $blog->id }}" action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/styles/datatable.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('js')
<!-- DataTables JS -->
<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // DataTable Başlatma
    $('.data-table').DataTable({
        responsive: true,
        columnDefs: [{
            targets: "datatable-nosort",
            orderable: false,
        }],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tümü"]],
        "language": {
            "sDecimal": ",",
            "sEmptyTable": "Tabloda herhangi bir veri mevcut değil",
            "sInfo": "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
            "sInfoEmpty": "Kayıt yok",
            "sInfoFiltered": "(_MAX_ kayıt içerisinden bulunan)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "Sayfada _MENU_ kayıt göster",
            "sLoadingRecords": "Yükleniyor...",
            "sProcessing": "İşleniyor...",
            "sSearch": "Ara:",
            "sZeroRecords": "Eşleşen kayıt bulunamadı",
            "oPaginate": {
                "sFirst": "İlk",
                "sLast": "Son",
                "sNext": "Sonraki",
                "sPrevious": "Önceki"
            },
            "oAria": {
                "sSortAscending": ": artan sütun sıralamasını aktifleştir",
                "sSortDescending": ": azalan sütun sıralamasını aktifleştir"
            },
            "select": {
                "rows": {
                    "_": "%d kayıt seçildi",
                    "0": "",
                    "1": "1 kayıt seçildi"
                }
            }
        }
    });

    // Başarı mesajı
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Başarılı!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    // Hata mesajı
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Hata!',
        text: '{{ session('error') }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif
});
</script>
@endsection