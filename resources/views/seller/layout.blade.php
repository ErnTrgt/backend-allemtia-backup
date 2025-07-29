<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Seller Panel') - Allemtia</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Existing CSS (for backward compatibility) -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/styles/core.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/styles/icon-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/src/plugins/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/src/plugins/slick/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
    
    <!-- Modern Layout CSS -->
    @include('seller.partials.modern-styles')
    
    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        @include('seller.partials.modern-sidebar')

        <!-- Header -->
        @include('seller.partials.modern-header')

        <!-- Main Content -->
        <main class="app-main">
            <div class="main-content">
                @if(View::hasSection('page-header'))
                    <div class="page-header">
                        @yield('page-header')
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Scripts -->
    <script src="{{ asset('admin/vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('admin/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/slick/slick.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
    <script src="{{ asset('admin/src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.js"></script>

    <script>
        jQuery(document).ready(function() {
            jQuery(".product-slider").slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                infinite: true,
                speed: 1000,
                fade: true,
                asNavFor: ".product-slider-nav",
            });
            jQuery(".product-slider-nav").slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                asNavFor: ".product-slider",
                dots: false,
                infinite: true,
                arrows: false,
                speed: 1000,
                centerMode: true,
                focusOnSelect: true,
            });
            $("input[name='demo3_22']").TouchSpin({
                initval: 1,
            });
        });
    </script>
    <script>
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
                    searchPlaceholder: "Search records",
                }
            });
        });
    </script>
     <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        });
    </script>

    
    <!-- Modern Layout Scripts -->
    @include('seller.partials.modern-scripts')
    
    @stack('scripts')
</body>
</html>
