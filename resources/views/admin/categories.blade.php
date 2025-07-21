@extends('layouts.layout')

@section('title', 'Kategori Yönetimi')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Kategori Yönetimi</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kategoriler</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Ana Kategori Ekleme -->
            <div class="col-md-6 mb-30">
                <div class="card-box h-100">
                    <div class="pd-20">
                        <div class="d-flex align-items-center mb-3">
                            <i class="icon-copy dw dw-folder1 text-blue mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-blue h4 mb-0">Ana Kategori Ekle</h4>
                        </div>
                        <p class="text-muted">Yeni bir ana kategori ekleyin. Ana kategoriler en üst seviyede yer alır.</p>
                    </div>
                    <form action="{{ route('admin.storeCategory') }}" method="POST">
                        @csrf
                        <div class="pd-20">
                            <div class="form-group">
                                <label for="category_name">Kategori Adı</label>
                                <input type="text" id="category_name" name="name" class="form-control" placeholder="Örn: Elektronik, Giyim, Kitaplar" required>
                                <input type="hidden" name="parent_id" value="">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="icon-copy dw dw-add"></i> Ana Kategori Ekle
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alt Kategori Ekleme -->
            <div class="col-md-6 mb-30">
                <div class="card-box h-100">
                    <div class="pd-20">
                        <div class="d-flex align-items-center mb-3">
                            <i class="icon-copy dw dw-folder-2 text-success mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-success h4 mb-0">Alt Kategori Ekle</h4>
                        </div>
                        <p class="text-muted">Mevcut bir kategorinin altına yeni alt kategori ekleyin. Önce üst kategoriyi seçin.</p>
                    </div>
                    <form action="{{ route('admin.storeSubcategory') }}" method="POST">
                        @csrf
                        <div class="pd-20">
                            <div class="form-group">
                                <label for="category_id">
                                    <i class="icon-copy dw dw-up-arrow1 mr-1"></i> Üst Kategori
                                </label>
                                <select id="category_id" name="category_id" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">Üst Kategori Seçin</option>
                                    @foreach ($allCategories as $cat)
                                        @php
                                            $prefix = '';
                                            $disabled = false;
                                            
                                            if ($cat->parent) {
                                                $parent = $cat->parent;
                                                $prefix = '-- ';
                                                
                                                while ($parent->parent) {
                                                    $prefix .= '-- ';
                                                    $parent = $parent->parent;
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $cat->id }}">{{ $prefix }}{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <i class="icon-copy dw dw-information"></i>
                                    Seçtiğiniz kategori, yeni ekleyeceğiniz alt kategorinin üst kategorisi olacaktır.
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="subcategory_name">
                                    <i class="icon-copy dw dw-tag-1 mr-1"></i> Alt Kategori Adı
                                </label>
                                <input type="text" id="subcategory_name" name="name" class="form-control" placeholder="Örn: Akıllı Telefonlar, Erkek Gömlekleri" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="icon-copy dw dw-add"></i> Alt Kategori Ekle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kategori Listesi -->
        <div class="card-box mb-30">
            <div class="pd-20">
                <div class="d-flex align-items-center mb-3">
                    <i class="icon-copy dw dw-list text-primary mr-2" style="font-size: 24px;"></i>
                    <h4 class="text-primary h4 mb-0">Kategori Listesi</h4>
                </div>
                <p class="text-muted">Tüm kategoriler ve alt kategoriler aşağıda listelenmiştir. Düzenlemek veya silmek için sağdaki butonları kullanın.</p>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="40%">Kategori Yapısı</th>
                            <th width="15%">Alt Kategori Sayısı</th>
                            <th width="15%">Oluşturulma Tarihi</th>
                            <th width="25%">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $index => $category)
                            <tr class="main-category-row">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="category-name-wrapper">
                                        <span class="badge badge-primary category-badge">Ana Kategori</span>
                                        <span class="font-weight-bold ml-2">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-info">{{ $category->children->count() }}</span>
                                </td>
                                <td>{{ $category->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            <i class="dw dw-more"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                            <button class="dropdown-item" data-toggle="modal" data-target="#editModal{{ $category->id }}">
                                                <i class="dw dw-edit2"></i> Düzenle
                                            </button>
                                            <form action="{{ route('admin.deleteCategory', $category->id) }}" method="POST" onsubmit="return confirm('Bu kategoriyi ve tüm alt kategorilerini silmek istediğinizden emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="dw dw-delete-3"></i> Sil
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @foreach ($category->children as $child)
                                <tr class="sub-category-row">
                                    <td></td>
                                    <td>
                                        <div class="category-name-wrapper pl-4 border-left">
                                            <span class="badge badge-success category-badge">Alt Kategori</span>
                                            <span class="ml-2">{{ $child->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-secondary">{{ $child->children->count() }}</span>
                                    </td>
                                    <td>{{ $child->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <button class="dropdown-item" data-toggle="modal" data-target="#editModal{{ $child->id }}">
                                                    <i class="dw dw-edit2"></i> Düzenle
                                                </button>
                                                <form action="{{ route('admin.deleteCategory', $child->id) }}" method="POST" onsubmit="return confirm('Bu alt kategoriyi silmek istediğinizden emin misiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="dw dw-delete-3"></i> Sil
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($child->children as $grandchild)
                                    <tr class="grand-child-category-row">
                                        <td></td>
                                        <td>
                                            <div class="category-name-wrapper pl-5 ml-3 border-left">
                                                <span class="badge badge-warning category-badge">Alt-Alt Kategori</span>
                                                <span class="ml-2">{{ $grandchild->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-pill badge-light">{{ $grandchild->children->count() }}</span>
                                        </td>
                                        <td>{{ $grandchild->created_at->format('d.m.Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                                    <i class="dw dw-more"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                    <button class="dropdown-item" data-toggle="modal" data-target="#editModal{{ $grandchild->id }}">
                                                        <i class="dw dw-edit2"></i> Düzenle
                                                    </button>
                                                    <form action="{{ route('admin.deleteCategory', $grandchild->id) }}" method="POST" onsubmit="return confirm('Bu alt-alt kategoriyi silmek istediğinizden emin misiniz?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="dw dw-delete-3"></i> Sil
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modals -->
        @foreach ($allCategories as $category)
            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $category->id }}">
                                @if($category->parent_id)
                                    @if($category->parent && $category->parent->parent_id)
                                        <i class="icon-copy dw dw-folder-4 text-warning mr-2"></i> Alt-Alt Kategori Düzenle
                                    @else
                                        <i class="icon-copy dw dw-folder-2 text-success mr-2"></i> Alt Kategori Düzenle
                                    @endif
                                @else
                                    <i class="icon-copy dw dw-folder1 text-primary mr-2"></i> Ana Kategori Düzenle
                                @endif
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.updateCategory', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                @if($category->parent_id)
                                    <div class="form-group">
                                        <label for="parent_category_{{ $category->id }}">Üst Kategori</label>
                                        <select id="parent_category_{{ $category->id }}" name="parent_id" class="form-control selectpicker" data-live-search="true">
                                            <option value="">Ana Kategori Olarak Ayarla</option>
                                            @foreach ($allCategories as $cat)
                                                @if($cat->id != $category->id && !$cat->isDescendantOf($category))
                                                    @php
                                                        $prefix = '';
                                                        if ($cat->parent) {
                                                            $parent = $cat->parent;
                                                            $prefix = '-- ';
                                                            
                                                            while ($parent->parent) {
                                                                $prefix .= '-- ';
                                                                $parent = $parent->parent;
                                                            }
                                                        }
                                                    @endphp
                                                    <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $prefix }}{{ $cat->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="icon-copy dw dw-information"></i>
                                            Kategoriyi başka bir kategorinin altına taşıyabilirsiniz.
                                        </small>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="category_name_{{ $category->id }}">Kategori Adı</label>
                                    <input type="text" id="category_name_{{ $category->id }}" name="name" class="form-control" value="{{ $category->name }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                <button type="submit" class="btn btn-primary">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
        
        // DataTable ayarları
        $('.data-table').DataTable({
            scrollCollapse: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tümü"]],
            "language": {
                "info": "_START_-_END_ / _TOTAL_ kayıt",
                "search": "Ara:",
                "paginate": {
                    "next": "Sonraki",
                    "previous": "Önceki"
                },
                "lengthMenu": "Sayfa başına _MENU_ kayıt göster",
                "zeroRecords": "Kayıt bulunamadı",
                "infoEmpty": "Kayıt yok",
                "infoFiltered": "(toplam _MAX_ kayıttan filtrelendi)"
            },
        });
    });
</script>
@endsection

@section('styles')
<style>
    .category-name-wrapper {
        display: flex;
        align-items: center;
    }
    
    .category-badge {
        min-width: 100px;
        text-align: center;
    }
    
    .border-left {
        border-left: 2px solid #e7e7e7;
        margin-left: 10px;
    }
    
    .main-category-row {
        background-color: rgba(240, 246, 255, 0.3);
    }
    
    .sub-category-row {
        background-color: rgba(240, 246, 255, 0.1);
    }
    
    .grand-child-category-row {
        background-color: rgba(255, 252, 240, 0.1);
    }
</style>
@endsection
