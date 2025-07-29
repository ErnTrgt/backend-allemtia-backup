@extends('layouts.layout')

@section('title', 'Hakkımızda Sayfa İçeriği')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Hakkımızda Sayfa İçerik Yönetimi</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Hakkımızda Sayfası</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addAboutModal">
                        + Yeni Bölüm Ekle
                    </button>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Hakkımızda Sayfa Bölümleri</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bölüm Anahtarı</th>
                            <th>Başlık</th>
                            <th>İçerik</th>
                            <th>Durum</th>
                            <th>Resim</th>
                            <th class="datatable-nosort">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sections as $section)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $section->section_key }}</td>
                            <td>{{ Str::limit($section->title, 40) }}</td>
                            <td>{{ Str::limit(strip_tags($section->content), 50) }}</td>
                            <td>
                                <span class="badge {{ $section->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $section->status ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                @if($section->image)
                                    <img src="{{ asset('storage/' . $section->image) }}" alt="Section Image" width="80">
                                @else
                                    <span>Resim Yok</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" href="{{ route('admin.about.edit', $section->id) }}">
                                            <i class="dw dw-edit2"></i> Düzenle
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('toggle-status-{{ $section->id }}').submit();">
                                            <i class="dw {{ $section->status ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $section->status ? 'Pasifleştir' : 'Aktifleştir' }}
                                        </a>
                                        <form id="toggle-status-{{ $section->id }}" action="{{ route('admin.about.toggleStatus', $section->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('delete-about-{{ $section->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Sil
                                        </a>
                                        <form id="delete-about-{{ $section->id }}" action="{{ route('admin.about.delete', $section->id) }}" method="POST" style="display: none;">
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

<!-- Add About Section Modal -->
<div class="modal fade" id="addAboutModal" tabindex="-1" role="dialog" aria-labelledby="addAboutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white border-0">
                <h4 class="modal-title font-weight-bold">
                    <i class="dw dw-add mr-2"></i>Yeni Hakkımızda Bölümü Ekle
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{ route('admin.about.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Temel Bilgiler -->
                    <div class="form-section mb-4">
                        <h6 class="text-primary font-weight-bold mb-3">
                            <i class="dw dw-settings mr-2"></i>Bölüm Bilgileri
                        </h6>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-key mr-1 text-info"></i>Bölüm Anahtarı <small>(örn: heading, area, features)</small>
                            </label>
                            <input type="text" name="section_key" class="form-control form-control-lg border-2" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-edit mr-1 text-primary"></i>Başlık
                            </label>
                            <input type="text" name="title" class="form-control form-control-lg border-2">
                        </div>
                    </div>

                    <!-- İçerik -->
                    <div class="form-section mb-4">
                        <h6 class="text-success font-weight-bold mb-3">
                            <i class="dw dw-file-text mr-2"></i>İçerik Detayları
                        </h6>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-text-width mr-1 text-success"></i>İçerik
                            </label>
                            <textarea name="content" class="form-control form-control-lg border-2" rows="5"></textarea>
                        </div>
                    </div>

                    <!-- Görsel -->
                    <div class="form-section">
                        <h6 class="text-info font-weight-bold mb-3">
                            <i class="dw dw-image mr-2"></i>Görsel
                        </h6>
                        <div class="form-group">
                            <label class="font-weight-semibold text-dark">
                                <i class="dw dw-upload mr-1 text-warning"></i>Bölüm Görseli
                            </label>
                            <input type="file" name="image" class="form-control-file border-2 p-2">
                            <small class="text-muted">
                                <i class="dw dw-info mr-1"></i>
                                Önerilen boyut: 800x600px, maksimum boyut: 2MB
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 justify-content-between">
                    <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                        <i class="dw dw-cancel mr-2"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="dw dw-add mr-2"></i>Bölüm Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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