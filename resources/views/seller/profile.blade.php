@extends('seller.layout')

@section('title', 'Seller Profile')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Profil</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/seller/dashboard') }}">Anasayfa</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Profil</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <div class="profile-photo">
                            <a href="javascript:;" data-toggle="modal" data-target="#avatarModal" class="edit-avatar">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('admin/vendors/images/photo1.jpg') }}" 
                                 alt="Profile Photo" 
                                 class="avatar-photo"
                                 id="currentAvatar" />
                            
                            <!-- Avatar Upload Modal -->
                            <div class="modal fade" id="avatarModal" tabindex="-1" role="dialog"
                                aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Profil Fotoğrafını Güncelle</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center mb-3">
                                                <img id="avatarPreview" 
                                                     src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('admin/vendors/images/photo1.jpg') }}" 
                                                     alt="Preview" 
                                                     style="max-width: 200px; max-height: 200px; border-radius: 50%;" />
                                            </div>
                                            <form id="avatarUploadForm" enctype="multipart/form-data">
                                                @csrf
                                                <div class="custom-file">
                                                    <input type="file" 
                                                           class="custom-file-input" 
                                                           id="avatarInput" 
                                                           name="avatar" 
                                                           accept="image/*" 
                                                           required>
                                                    <label class="custom-file-label" for="avatarInput">Dosya Seçin</label>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">İzin verilen formatlar: JPG, JPEG, PNG, GIF. Max boyut: 2MB</small>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                            <button type="button" class="btn btn-primary" onclick="uploadAvatar()">Fotoğrafı Yükle</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5 class="text-center h5 mb-0">{{ auth()->user()->name }}</h5>
                        <p class="text-center text-muted font-14">
                            {{ auth()->user()->store_name ?? 'Satıcı Hesabı' }}
                        </p>
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">İletişim Bilgileri</h5>
                            <ul>
                                <li><span>Email Adresi:</span> {{ auth()->user()->email }}</li>
                                <li><span>Telefon Numarası:</span> {{ auth()->user()->phone ?? 'Not provided' }}</li>
                                <li><span>Ülke:</span> {{ auth()->user()->country ?? 'Not provided' }}</li>
                                <li>
                                    <span>Şehir:</span>
                                    {{ auth()->user()->state ?? 'Not provided' }}
                                </li>
                                <li>
                                    <span>Adres:</span>
                                    {{ auth()->user()->address ?? 'Not provided' }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                    <div class="card-box height-100-p overflow-hidden">
                        <div class="profile-tab height-100-p">
                            <div class="tab height-100-p">
                                <ul class="nav nav-tabs customtab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#timeline"
                                            role="tab">Zaman Çizelgesi</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tasks" role="tab">Görevler</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#setting" role="tab">Ayarlar</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <!-- Timeline Tab start -->
                                    <div class="tab-pane fade show active" id="timeline" role="tabpanel">
                                        <div class="pd-20">
                                            <div class="profile-timeline">
                                                <div class="timeline-month">
                                                    <h5>{{ \Carbon\Carbon::now()->format('F, Y') }}</h5>
                                                </div>
                                                <div class="profile-timeline-list">
                                                    <ul>
                                                        @forelse ($products as $product)
                                                            <li>
                                                                <div class="date">{{ $product->created_at->format('d M') }}</div>
                                                                <div class="task-name">
                                                                    <i class="ion-android-alarm-clock"></i> {{ $product->name }}
                                                                </div>
                                                                <p>{{ \Illuminate\Support\Str::limit($product->description, 50) }}</p>
                                                                <div class="task-time">{{ $product->created_at->format('h:i A') }}</div>
                                                            </li>
                                                        @empty
                                                            <li>
                                                                <div class="task-name">
                                                                    <i class="ion-ios-information"></i> No Products Found
                                                                </div>
                                                            </li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timeline Tab End -->
                                    <!-- Tasks Tab start -->
                                    <div class="tab-pane fade" id="tasks" role="tabpanel">
                                        <div class="pd-20 profile-task-wrap">
                                            <div class="container pd-0">
                                                <!-- Open Task start -->
                                                <div class="task-title row align-items-center">
                                                    <div class="col-md-8 col-sm-12">
                                                        <h5>Açık Görevler (4 Kalan)</h5>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 text-right">
                                                        <a href="javascript:;" data-toggle="modal" data-target="#task-add"
                                                            class="bg-light-blue btn text-blue weight-500"><i
                                                                class="ion-plus-round"></i> Ekle</a>
                                                    </div>
                                                </div>
                                                <div class="profile-task-list pb-30">
                                                    <ul>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-1" />
                                                                <label class="custom-control-label"
                                                                    for="task-1"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet, consectetur
                                                            adipisicing elit. Id ea earum.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2019</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-2" />
                                                                <label class="custom-control-label"
                                                                    for="task-2"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2019</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-3" />
                                                                <label class="custom-control-label"
                                                                    for="task-3"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet, consectetur
                                                            adipisicing elit.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2019</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-4" />
                                                                <label class="custom-control-label"
                                                                    for="task-4"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet. Id ea earum.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2019</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <!-- Open Task End -->
                                                <!-- Close Task start -->
                                                <div class="task-title row align-items-center">
                                                    <div class="col-md-12 col-sm-12">
                                                        <h5>Kapalı Görevler</h5>
                                                    </div>
                                                </div>
                                                <div class="profile-task-list close-tasks">
                                                    <ul>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-close-1" checked="" disabled="" />
                                                                <label class="custom-control-label"
                                                                    for="task-close-1"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet, consectetur
                                                            adipisicing elit. Id ea earum.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2018</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-close-2" checked="" disabled="" />
                                                                <label class="custom-control-label"
                                                                    for="task-close-2"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2018</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-close-3" checked="" disabled="" />
                                                                <label class="custom-control-label"
                                                                    for="task-close-3"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet, consectetur
                                                            adipisicing elit.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2018</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-checkbox mb-5">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="task-close-4" checked="" disabled="" />
                                                                <label class="custom-control-label"
                                                                    for="task-close-4"></label>
                                                            </div>
                                                            <div class="task-type">Email</div>
                                                            Lorem ipsum dolor sit amet. Id ea earum.
                                                            <div class="task-assign">
                                                                Assigned to Ferdinand M.
                                                                <div class="due-date">
                                                                    due date <span>22 February 2018</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <!-- Close Task start -->
                                                <!-- add task popup start -->
                                                <div class="modal fade customscroll" id="task-add" tabindex="-1"
                                                    role="dialog">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                                    Görev Ekle
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    title="" data-original-title="Close Modal">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body pd-0">
                                                                <div class="task-list-form">
                                                                    <ul>
                                                                        <li>
                                                                            <form>
                                                                                <div class="form-group row">
                                                                                    <label class="col-md-4">Görev
                                                                                        Tipi</label>
                                                                                    <div class="col-md-8">
                                                                                        <input type="text"
                                                                                            class="form-control" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <label class="col-md-4">Görev
                                                                                        Mesajı</label>
                                                                                    <div class="col-md-8">
                                                                                        <textarea class="form-control"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <label class="col-md-4">Atanmış
                                                                                        Kişi</label>
                                                                                    <div class="col-md-8">
                                                                                        <select
                                                                                            class="selectpicker form-control"
                                                                                            data-style="btn-outline-primary"
                                                                                            title="Seçilmedi"
                                                                                            multiple=""
                                                                                            data-selected-text-format="count"
                                                                                            data-count-selected-text="{0} people selected">
                                                                                            <option>Ferdinand M.</option>
                                                                                            <option>Don H. Rabon</option>
                                                                                            <option>Ann P. Harris</option>
                                                                                            <option>
                                                                                                Katie D. Verdin
                                                                                            </option>
                                                                                            <option>
                                                                                                Christopher S. Fulghum
                                                                                            </option>
                                                                                            <option>
                                                                                                Matthew C. Porter
                                                                                            </option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row mb-0">
                                                                                    <label class="col-md-4">Due
                                                                                        Date</label>
                                                                                    <div class="col-md-8">
                                                                                        <input type="text"
                                                                                            class="form-control date-picker" />
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="add-more-task">
                                                                    <a href="#" data-toggle="tooltip"
                                                                        data-placement="bottom" title=""
                                                                        data-original-title="Add Task"><i
                                                                            class="ion-plus-circled"></i> Daha Fazla Görev</a>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-primary">
                                                                    Ekle
                                                                </button>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">
                                                                    Kapat
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- add task popup End -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Tasks Tab End -->
                                   <!-- Setting Tab start -->
<div class="tab-pane fade height-100-p" id="setting" role="tabpanel">
    <div class="profile-setting">
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form action="{{ route('seller.profile.update') }}" method="POST">
            @csrf
            <ul class="profile-edit-list row">
                <li class="weight-500 col-md-12">
                    <h4 class="text-blue h5 mb-20">Kişisel Ayarlarınızı Düzenleyin</h4>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="form-group">
                        <label>Tam Adınız</label>
                        <input class="form-control form-control-lg" type="text" name="name" value="{{ $user->name }}" required />
                    </div>
                    <div class="form-group">
                        <label>Email Adresi</label>
                        <input class="form-control form-control-lg" type="email" name="email" value="{{ $user->email }}" required />
                    </div>
                    <div class="form-group">
                        <label>Telefon Numarası</label>
                        <input class="form-control form-control-lg" type="text" name="phone" value="{{ $user->phone }}" />
                    </div>
                    <div class="form-group">
                        <label>Ülke</label>
                        <input class="form-control form-control-lg" type="text" name="country" value="{{ $user->country }}" />
                    </div>
                    <div class="form-group">
                        <label>Şehir/İl/İlçe</label>
                        <input class="form-control form-control-lg" type="text" name="state" value="{{ $user->state }}" />
                    </div>
                    <div class="form-group">
                        <label>Posta Kodu</label>
                        <input class="form-control form-control-lg" type="text" name="postal_code" value="{{ $user->postal_code }}" />
                    </div>
                    <div class="form-group">
                        <label>Adres</label>
                        <textarea class="form-control" name="address">{{ $user->address }}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <input type="submit" class="btn btn-primary" value="Bilgileri Güncelle" />
                    </div>
                </li>
            </ul>
        </form>
    </div>
</div>
<!-- Setting Tab End -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Avatar upload function
function uploadAvatar() {
    console.log('Upload function called');
    
    const fileInput = document.getElementById('avatarInput');
    if (!fileInput.files.length) {
        Swal.fire({
            icon: 'warning',
            title: 'No file selected',
            text: 'Please select an image file'
        });
        return;
    }
    
    const file = fileInput.files[0];
    
    // Check file size
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File too large',
            text: 'Please select an image smaller than 2MB'
        });
        return;
    }
    
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Disable button and show loading
    const uploadBtn = document.querySelector('.modal-footer .btn-primary');
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>Uploading...';
    
    // jQuery AJAX
    $.ajax({
        url: '{{ route("seller.avatar.upload") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Success:', response);
            
            if (response.success) {
                // Update current avatar
                document.getElementById('currentAvatar').src = response.avatar_url;
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Profile photo updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Close modal
                $('#avatarModal').modal('hide');
                
                // Reset form
                document.getElementById('avatarUploadForm').reset();
                document.querySelector('.custom-file-label').textContent = 'Choose file';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message || 'Upload failed'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText);
            
            let errorMessage = 'An error occurred';
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                } else if (response.errors) {
                    errorMessage = Object.values(response.errors).flat().join(', ');
                }
            } catch (e) {
                errorMessage = 'Server error: ' + error;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed!',
                text: errorMessage
            });
        },
        complete: function() {
            // Re-enable button
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = 'Upload Photo';
        }
    });
}

// Document ready
$(document).ready(function() {
    console.log('jQuery loaded');
    
    // File input change event
    $('#avatarInput').on('change', function(e) {
        console.log('File selected');
        const file = e.target.files[0];
        if (file) {
            // Update file label
            $(this).next('.custom-file-label').html(file.name);
            
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Reset preview when modal is closed
    $('#avatarModal').on('hidden.bs.modal', function() {
        $('#avatarUploadForm')[0].reset();
        $('.custom-file-label').html('Choose file');
        $('#avatarPreview').attr('src', $('#currentAvatar').attr('src'));
    });
});
</script>
@endpush

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NXZMQSS" height="0" width="0"
        style="display: none; visibility: hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->