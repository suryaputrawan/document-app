<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Ward Info BIMC KUTA">
    <meta name="author" content="Ward">
    <meta name="keywords" content="ward, bimc kuta">

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>HCDOC APP</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/favicons/favicon.ico') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/favicons/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicons/site.webmanifest') }}">
    <link rel="mask-icon" color="#5bbad5" href="{{ asset('assets/favicons/safari-pinned-tab.svg') }}">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- plugin css -->
    <link href="{{ asset('assets/admin/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/magnific-popup/magnific-popup.css') }}" rel="stylesheet" />
    <!-- end plugin css -->

    @stack('plugin-styles')

    <!-- common css -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin/custom.css') }}" rel="stylesheet" />
    <!-- end common css -->

    @stack('style')
</head>

<body data-base-url="{{url('/')}}">

    <script src="{{ asset('assets/admin/js/spinner.js') }}"></script>

    <div class="main-wrapper" id="app">
        @include('master.admin.includes.sidebar')
        <div class="page-wrapper">
            @include('master.admin.includes.header')
            <div class="page-content">
                @yield('content')
            </div>
            @include('master.admin.includes.footer')
        </div>
    </div>

    <!-- base js -->
    <script src="{{ asset('js/admin/app.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/magnific-popup/magnific-popup.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/admin/js/template.js') }}"></script>
    <!-- end common js -->

    <script>
        var tableOptions = {
            "aLengthMenu": [
                [10, 30, 50, -1],
                [10, 30, 50, "All"]
            ],
            "iDisplayLength": 10,
            "language": {
                search: ""
            }
        };

        $(document).ready(function() {
            const swalWithBootstrapButtonsConfirm = Swal.mixin();
            const swalWithBootstrapButtons = Swal.mixin();

            $(document).on('click', '.delete-item', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = $(this).data('url');
                var label = $(this).data('label');

                swalWithBootstrapButtonsConfirm.fire({
                    title: `Yakin ingin menghapus [ ${label} ] ?`,
                    text: "Data yang sudah di hapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus data',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            type: "POST",
                            dataType: "JSON",
                            url: url,
                            data: {
                                "_method": 'DELETE',
                                "_token": "{{ csrf_token() }}",
                            }
                        }).then((data) => {
                            let message = 'Data telah berhasil di hapus';
                            if (data.message) {
                                message = data.message;
                            }
                            swalWithBootstrapButtons.fire('Berhasil!', message, 'success');
                            $('#datatable').DataTable().ajax.reload();
                        }, (data) => {
                            let message = '';
                            if (data.responseJSON.message) {
                                message = data.responseJSON.message;
                            }
                            swalWithBootstrapButtons.fire('Oops!', `Gagal menghapus data, ${message}`, 'error');
                            if (data.status === 404) {
                                $('#datatable').DataTable().ajax.reload();
                            }
                        });
                    },
                    allowOutsideClick: () => !swalWithBootstrapButtons.isLoading(),
                    backdrop: true
                });
            });
        });

    </script>
    @stack('custom-scripts')
    <script>
        $(document).ready(function() {
            $('.image-link').magnificPopup({type:'image'});
            
            $('#datatable').each(function() {
                var datatable = $(this);
                // SEARCH - Add the placeholder for Search and Turn this into in-line form control
                var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
                search_input.attr('placeholder', 'Search');
                search_input.removeClass('form-control-sm');
                // LENGTH - Inline-Form control
                var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
                length_sel.removeClass('form-control-sm');
            });
        });

        //Delete Image
         $(document).ready(function() {
            const swalWithBootstrapButtonsConfirm = Swal.mixin();
            const swalWithBootstrapButtons = Swal.mixin();

            $(document).on('click', '.delete-image', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var image = $(this).data('image');

                swalWithBootstrapButtonsConfirm.fire({
                    title: `Yakin ingin menghapus gambar ini ?`,
                    text: "Gambar yang sudah di hapus tidak bisa dikembalikan!",
                    imageUrl: image,
                    imageWidth: 400,
                    imageHeight: 200,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus Gambar',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            type: "POST",
                            dataType: "JSON",
                            url: url,
                            data: {
                                "_method": 'DELETE',
                                "_token": "{{ csrf_token() }}",
                            }
                        }).then((data) => {
                            let message = 'Gambar telah berhasil di hapus';
                            if (data.message) {
                                message = data.message;
                            }
                            swalWithBootstrapButtons.fire('Berhasil!', message, 'success');
                            window.location.reload()
                        }, (data) => {
                            let message = '';
                            if (data.responseJSON.message) {
                                message = data.responseJSON.message;
                            }
                            swalWithBootstrapButtons.fire('Oops!', `Gagal menghapus gambar, ${message}`, 'error');
                            if (data.status === 404) {
                                window.location.reload()
                            }
                        });
                    },
                    allowOutsideClick: () => !swalWithBootstrapButtons.isLoading(),
                    backdrop: true
                });
            });
        });

        //Loading Progress
        $(document).ready(function () {
            $(document).on('click', '.btn-save', function () {
                Swal.fire({
                    title: 'Please Wait !',
                    html: 'processing data.......',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            });
        });
    </script>
</body>

</html>
