@extends('layouts.layout')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Kullanıcı Yönetimi</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Kullanıcı Yönetimi
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Role Göre Filtrele
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.users') }}">Tümü</a>
                                <a class="dropdown-item" href="{{ route('admin.users', ['role' => 'admin']) }}">Yöneticiler</a>
                                <a class="dropdown-item" href="{{ route('admin.users', ['role' => 'seller']) }}">Satıcılar</a>
                                <a class="dropdown-item" href="{{ route('admin.users', ['role' => 'buyer']) }}">Alıcılar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Kullanıcı Tablosu</h4>
                    <p class="mb-0">
                        {{-- Kullanıcıların rollerini yönetin ve daha fazla seçenek   --}}
                    <div class="col-md-12 col-sm-12 text-right">
                        <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal">Kullanıcı Ekle</button>
                    </div>
                    </p>

                </div>

                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th class="table-plus datatable-nosort">Ad</th>
                                <th>E-posta</th>
                                <th>Telefon Numarası</th>
                                <th>Rol</th>
                                <th>Durum</th>
                                <th>Eklenme Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="table-plus">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>

                                    <td>
                                        @if($user->role === 'admin')
                                            Yönetici
                                        @elseif($user->role === 'seller')
                                            Satıcı
                                        @elseif($user->role === 'buyer')
                                            Alıcı
                                        @else
                                            {{ ucfirst($user->role) }}
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge
                                            {{ $user->status === 'approved' ? 'badge-success' : ($user->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                                            @if($user->status === 'approved')
                                                Onaylandı
                                            @elseif($user->status === 'rejected')
                                                Reddedildi
                                            @elseif($user->status === 'pending')
                                                Beklemede
                                            @else
                                                {{ ucfirst($user->status) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <!-- View -->
                                                {{-- <a class="dropdown-item" href="#"><i class="dw dw-eye"></i> Görüntüle</a> --}}

                                                <!-- Edit -->
                                                <a class="dropdown-item" href="#" data-toggle="modal"
                                                    data-target="#editUserModal{{ $user->id }}">
                                                    <i class="dw dw-edit2"></i> Düzenle
                                                </a>


                                                <!-- Change Status -->
                                                <div class="dropdown-divider"></div>
                                                <h6 style="text-align: center ">Durumu Değiştir</h6>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('status-pending-{{ $user->id }}').submit();">
                                                    <i class="icon-copy ion-clock"></i> Beklemede
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('status-approved-{{ $user->id }}').submit();">
                                                    <i class="dw dw-check"></i> Onayla
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('status-rejected-{{ $user->id }}').submit();">
                                                    <i class="dw dw-ban"></i> Reddet
                                                </a>

                                                <!-- Forms for Status Change -->
                                                <form id="status-pending-{{ $user->id }}"
                                                    action="{{ route('admin.users.changeStatus', ['id' => $user->id, 'status' => 'pending']) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <form id="status-approved-{{ $user->id }}"
                                                    action="{{ route('admin.users.changeStatus', ['id' => $user->id, 'status' => 'approved']) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <form id="status-rejected-{{ $user->id }}"
                                                    action="{{ route('admin.users.changeStatus', ['id' => $user->id, 'status' => 'rejected']) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>

                                                <!-- Delete -->
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('delete-user-{{ $user->id }}').submit();">
                                                    <i class="dw dw-delete-3"></i> Sil
                                                </a>
                                                <form id="delete-user-{{ $user->id }}"
                                                    action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Kullanıcı bulunamadı.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
            <!-- Users Table End -->

        </div>

    </div>


    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-success text-white border-0">
                    <h4 class="modal-title font-weight-bold">
                        <i class="dw dw-add-user mr-2"></i>Yeni Kullanıcı Ekle
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <!-- Temel Bilgiler -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary font-weight-bold mb-3">
                                <i class="dw dw-user mr-2"></i>Kişisel Bilgiler
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="userName" class="font-weight-semibold text-dark">
                                            <i class="dw dw-user1 mr-1 text-info"></i>Ad Soyad
                                        </label>
                                        <input type="text" name="name" id="userName" class="form-control form-control-lg border-2" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="userPhone" class="font-weight-semibold text-dark">
                                            <i class="dw dw-smartphone mr-1 text-warning"></i>Telefon Numarası
                                        </label>
                                        <input type="tel" name="phone" id="userPhone" class="form-control form-control-lg border-2" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hesap Bilgileri -->
                        <div class="form-section mb-4">
                            <h6 class="text-success font-weight-bold mb-3">
                                <i class="dw dw-key mr-2"></i>Hesap Bilgileri
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="userEmail" class="font-weight-semibold text-dark">
                                            <i class="dw dw-email mr-1 text-success"></i>E-posta
                                        </label>
                                        <input type="email" name="email" id="userEmail" class="form-control form-control-lg border-2" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="userPassword" class="font-weight-semibold text-dark">
                                            <i class="dw dw-padlock mr-1 text-danger"></i>Şifre
                                        </label>
                                        <input type="password" name="password" id="userPassword" class="form-control form-control-lg border-2" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rol ve Yetki -->
                        <div class="form-section">
                            <h6 class="text-info font-weight-bold mb-3">
                                <i class="dw dw-settings mr-2"></i>Rol ve Yetki
                            </h6>
                            <div class="form-group">
                                <label for="userRole" class="font-weight-semibold text-dark">
                                    <i class="dw dw-id-card mr-1 text-primary"></i>Kullanıcı Rolü
                                </label>
                                <select name="role" id="userRole" class="form-control form-control-lg border-2" required>
                                    <option value="admin">👑 Yönetici</option>
                                    <option value="seller">🏪 Satıcı</option>
                                    <option value="buyer">👤 Alıcı</option>
                                </select>
                                <small class="text-muted">
                                    <i class="dw dw-info mr-1"></i>
                                    Kullanıcı rolü, erişim izinlerini belirler
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 justify-content-between">
                        <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                            <i class="dw dw-cancel mr-2"></i>İptal
                        </button>
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            <i class="dw dw-add mr-2"></i>Kullanıcı Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add User Modal -->

    <!-- Edit Modal -->
    @foreach ($users as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-primary text-white border-0">
                        <h4 class="modal-title font-weight-bold">
                            <i class="dw dw-edit2 mr-2"></i>Kullanıcı Düzenle: <span class="badge badge-light text-primary">{{ $user->name }}</span>
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <!-- Temel Bilgiler -->
                            <div class="form-section mb-4">
                                <h6 class="text-primary font-weight-bold mb-3">
                                    <i class="dw dw-user mr-2"></i>Kişisel Bilgiler
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="userName{{ $user->id }}" class="font-weight-semibold text-dark">
                                                <i class="dw dw-user1 mr-1 text-info"></i>Ad Soyad
                                            </label>
                                            <input type="text" name="name" id="userName{{ $user->id }}"
                                                class="form-control form-control-lg border-2" value="{{ $user->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="userPhone{{ $user->id }}" class="font-weight-semibold text-dark">
                                                <i class="dw dw-smartphone mr-1 text-warning"></i>Telefon
                                            </label>
                                            <input type="text" name="phone" id="userPhone{{ $user->id }}"
                                                class="form-control form-control-lg border-2" value="{{ $user->phone }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hesap Bilgileri -->
                            <div class="form-section mb-4">
                                <h6 class="text-success font-weight-bold mb-3">
                                    <i class="dw dw-key mr-2"></i>Hesap Bilgileri
                                </h6>
                                <div class="form-group">
                                    <label for="userEmail{{ $user->id }}" class="font-weight-semibold text-dark">
                                        <i class="dw dw-email mr-1 text-success"></i>E-posta
                                    </label>
                                    <input type="email" name="email" id="userEmail{{ $user->id }}"
                                        class="form-control form-control-lg border-2" value="{{ $user->email }}" required>
                                </div>
                            </div>

                            <!-- Rol ve Durum -->
                            <div class="form-section">
                                <h6 class="text-info font-weight-bold mb-3">
                                    <i class="dw dw-settings mr-2"></i>Rol ve Durum
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="userRole{{ $user->id }}" class="font-weight-semibold text-dark">
                                                <i class="dw dw-id-card mr-1 text-primary"></i>Kullanıcı Rolü
                                            </label>
                                            <select name="role" id="userRole{{ $user->id }}" class="form-control form-control-lg border-2" required>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>👑 Yönetici
                                                </option>
                                                <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>🏪 Satıcı
                                                </option>
                                                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>👤 Alıcı
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="userStatus{{ $user->id }}" class="font-weight-semibold text-dark">
                                                <i class="dw dw-power mr-1 text-danger"></i>Hesap Durumu
                                            </label>
                                            <select name="status" id="userStatus{{ $user->id }}" class="form-control form-control-lg border-2" required>
                                                <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>
                                                    ⏳ Beklemede</option>
                                                <option value="approved" {{ $user->status === 'approved' ? 'selected' : '' }}>
                                                    ✅ Onaylandı</option>
                                                <option value="rejected" {{ $user->status === 'rejected' ? 'selected' : '' }}>
                                                    ❌ Reddedildi</option>
                                            </select>
                                        </div>
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
    @endforeach
    <!-- End Edit Modal -->

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


@endsection