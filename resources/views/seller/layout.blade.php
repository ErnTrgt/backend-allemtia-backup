<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ALLEMTIA Satıcı Paneli - Türkiye'nin lider B2B e-ticaret platformu">
    <meta name="theme-color" content="#A90000">
    <title>@yield('title', 'Satıcı Paneli') | ALLEMTIA</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('admin/src/images/favicon-32x32.png') }}">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    
    <!-- DM Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Modern Layout CSS -->
    <link rel="stylesheet" href="{{ asset('seller/css/modern-layout.css') }}">
    
    <!-- Legacy CSS (for compatibility) -->
    <link rel="stylesheet" href="{{ asset('admin/src/plugins/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
    
    @stack('styles')
</head>

<body class="loading">
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        <!-- Modern Header -->
        <header class="header">
            @include('seller.partials.header')
        </header>

        <!-- Modern Sidebar -->
        <aside class="left-side-bar">
            @include('seller.partials.sidebar')
        </aside>

        <!-- Mobile Menu Overlay -->
        <div class="mobile-menu-overlay"></div>

        <!-- Main Content Area -->
        <main class="main-container">
            @yield('content')
        </main>
        
    </div>

    
    <!-- jQuery (legacy support) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Modern Layout JS -->
    <script src="{{ asset('seller/js/modern-layout.js') }}"></script>
    
    <!-- Legacy Scripts (for compatibility) -->
    <script src="{{ asset('admin/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // DataTables Initialization (backward compatibility)
        $(document).ready(function() {
            $('.data-table').DataTable({
                responsive: true,
                autoWidth: false,
                columnDefs: [{
                    targets: 'datatable-nosort',
                    orderable: false,
                }],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Kayıt ara...",
                    lengthMenu: "_MENU_ kayıt göster",
                    info: "_START_ - _END_ / _TOTAL_ kayıt",
                    paginate: {
                        first: "İlk",
                        last: "Son",
                        next: "Sonraki",
                        previous: "Önceki"
                    },
                    zeroRecords: "Kayıt bulunamadı",
                    emptyTable: "Tabloda veri yok",
                    infoEmpty: "0 kayıt",
                    infoFiltered: "(_MAX_ kayıt içinden filtrelendi)"
                }
            });
        });
        
        // SweetAlert2 Session Messages
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>

</html>
