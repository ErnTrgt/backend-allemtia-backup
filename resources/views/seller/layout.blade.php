<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" href="{{ asset('admin/vendors/styles/core.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/styles/icon-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/styles/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/src/plugins/sweetalert2/sweetalert2.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/src/plugins/slick/slick.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
    @stack('styles')
</head>

<body>
    {{-- @include('partials.preloader') --}}

    <div class="header">
        @include('seller.partials.header')
    </div>

    <div class="left-side-bar">
        @include('seller.partials.sidebar')
    </div>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        @yield('content')
    </div>

    <footer>
        @include('partials.footer')
    </footer>

    <script src="{{ asset('admin/vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('admin/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/scripts/process.js') }}"></script>
    <script src="{{ asset('admin/vendors/scripts/layout-settings.js') }}"></script>
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

    @stack('scripts')
</body>

</html>
