@extends('master.admin.app')

@push('plugin-styles')
  <link href="{{ asset('assets/admin/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Main</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb }}</li>
    </ol>
</nav>
  
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header flex flex-align-center">
                <h6 class="card-title flex-full-width mb-0">Documents</h6>
                @can('create document')
                    <a id="btn-create" type="button" class="btn btn-sm btn-primary btn-icon-text">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Tambah
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table dataTable table-striped table-sm table-wrapped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 20px">NO</th>
                                <th style="width: 100px">ACTION</th>
                                <th>SIGN</th>
                                <th>NO DOCUMENT</th>
                                <th>TANGGAL</th>
                                <th>JENIS</th>
                            </tr>
                        </thead>
                        <tbody class="align-middle">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('surat.modal.upload-sign')
@include('surat.modal.signature-pad')
@include('surat.modal.pilih-jenis')
@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/admin/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/admin/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@push('custom-scripts')
<script type="text/javascript">
    $('#jenis-document').select2({
        dropdownParent: $('#modal-pilih-jenis')
    });

    //----Signature Pad initialization
    var canvas = document.getElementById('signaturePad');
    var signaturePad = new SignaturePad(canvas);

    function clearSignature() {
        signaturePad.clear();
    }

    function isSignatureEmpty() {
        return signaturePad.isEmpty();
    }
    //----End initialization

    //Preview Image
    function previewImages() {
        var preview = document.querySelector('#preview');
        preview.innerHTML = '';
        var files = document.querySelector('input[type=file]').files;
    
        function readAndPreview(file) {
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();
                reader.addEventListener('load', function() {
                    var image = new Image();
                    image.height = 150;
                    image.title = file.name;
                    image.src = this.result;
                    preview.appendChild(image);
                }, false);
    
                reader.readAsDataURL(file);
            }
        }
    
        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    };

    $(document).ready(function(){
        let dataTable = $("#datatable").DataTable({
            ...tableOptions,
            ajax: "{{ route('document.index') }}?type=datatable",
            processing: true,
            serverSide : true,
            scrollX: true,
            responsive: false,
            columns: [
                {
                    data: "id",
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false, searchable: false,
                    className: "text-center",
                },
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                { data: "sign", name: "sign", orderable: false, searchable: false, className: "text-center", },
                { data: "no_surat", name: "no_surat", orderable: true  },
                { data: "tgl_surat", name: "tgl_surat", orderable: true  },
                { data: "jenis_surat", name: "jenis_surat", orderable: true  },
            ],
            drawCallback: function( settings ) {
                feather.replace()
            }
        });

        //---Toast for session success
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session('success')) {
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}",
            });
        }
        @endif
        //--- End Toast session success

        //----form environtment
        function clearFormUploadPicture() {
            $('#picture-upload').val("");
            $('#upload-picture-form').find('.error').text("");
            var preview = document.querySelector('#preview');
            preview.innerHTML = '';
        }
        //---End Form environment

        //----Modal Upload Signature
        $(document).on('click', '.sign-document', function(e) {
            $('#modal-upload-sign').modal('show');
            $('#title-modal-sign').text('Upload Signature');
            $("#btn-submit-text").text("Sign Document");

            ajaxUrl = $(this).data('url');
            ajaxType = "PUT";
        });

        $(".btn-cancel-upload-picture").click(function() {
            $('#modal-upload-sign').modal('hide');
            clearFormUploadPicture();
        });
        //----End Modal

        //------ Submit Data upload signature
        $('#upload-picture-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#upload-picture-form').find('.error').text("");
            $('.btn-cancel-upload-picture').toggle();

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type   : "POST",
                url    : ajaxUrl,
                data   : formData,
                processData: false,
                contentType: false,
                success: function(response) {        

                    submitButton.prop('disabled',false);
                    submitButtonLoading.toggle();

                    if (response.status == 200) {
                        clearFormUploadPicture();
                        $('#modal-upload-sign').modal('hide');
                        $('#datatable').DataTable().ajax.reload();

                        $('.btn-cancel-upload-picture').toggle();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.picture, function(key, error) {
                            $('#error-picture').append(error);
                        });
                        $('.btn-cancel-upload-picture').toggle();
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                        $('.btn-cancel-upload-picture').toggle();
                    }
                },
                error: function(response){
                    submitButton.prop('disabled',false);
                    submitButtonLoading.toggle();

                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Submit Data Upload Signature


        //----form environtment signature pad
        function clearSignaturePad() {
            $('#signature-form').find('.error').text("");
            clearSignature();
        }
        //---End Form environment

        //----Modal Signature pad
        $(document).on('click', '.signature-doc', function(e) {
            $('#modal-signature-pad').modal('show');
            $("#btn-submit-signature").text("Sign Document");
            clearSignature();

            ajaxUrl = $(this).data('url');
        });

        $(".btn-cancel-signature").click(function() {
            $('#modal-signature-pad').modal('hide');
            clearSignaturePad();
        });
        //----End Modal

        //------ Submit Data Signature Pad
        $('#signature-form').on('submit', function(e) {
            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-signature-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            if (isSignatureEmpty()) {
                e.preventDefault();

                $('#error-signature').text("");
                $('#error-signature').append('Signature is empty. Please provide a signature.');
                submitButton.prop('disabled',false);
                submitButtonLoading.toggle();
            } else {
                e.preventDefault();

                var signatureInput = document.getElementById('signatureInput');
                signatureInput.value = signaturePad.toDataURL();

                $('#signature-form').find('.error').text("");
                $('.btn-cancel-signature').toggle();
                $('#clear-signature').toggle();

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type   : "POST",
                    url    : ajaxUrl,
                    data   : {
                        _token: '{{ csrf_token() }}',
                        signature: signaturePad.toDataURL()
                    },
                    success: function(response) {     

                        submitButton.prop('disabled',false);
                        submitButtonLoading.toggle();

                        if (response.status == 200) {
                            clearSignaturePad();

                            $('#modal-signature-pad').modal('hide');
                            $('#datatable').DataTable().ajax.reload();

                            $('.btn-cancel-signature').toggle();
                            $('#clear-signature').toggle();

                            Toast.fire({
                                icon: 'success',
                                title: response.message,
                            });
                        } else if (response.status == 400) {
                            $.each(response.errors.signature, function(key, error) {
                                $('#error-signature').append(error);
                            });

                            $('.btn-cancel-signature').toggle();
                            $('#clear-signature').toggle();
                        } else {
                            Toast.fire({
                                icon: 'warning',
                                title: response.message,
                            });

                            $('.btn-cancel-signature').toggle();
                            $('#clear-signature').toggle();
                        }
                    },
                    error: function(response){
                        submitButton.prop('disabled',false);
                        submitButtonLoading.toggle();

                        Toast.fire({
                            icon: 'error',
                            title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                        });

                        $('.btn-cancel-signature').toggle();
                        $('#clear-signature').toggle();
                    }
                });
            }            
        });
        //------ End Submit Data Signature pad


        //-----Modal Pilih Jenis Document
        $(document).on('click', '#btn-create', function(e) {
            $('#modal-pilih-jenis').modal('show');
            $("#btn-submit-jenis").text("Create");
        });

        $(".btn-cancel-jenis").click(function() {
            $('#modal-pilih-jenis').modal('hide');
            $('#jenis-document').val('').trigger('change');
        });

        //------ Submit Data modal pilih jenis
        $('#pilih-jenis-form').on('submit', function(e) {
            e.preventDefault();

            var jenisDocument = $('#jenis-document').val();
            var url = "{{ route('document.create') }}";

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-jenis-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            $('.btn-cancel-jenis').toggle();
            $('#pilih-jenis-form').find('.error').text("");

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type   : "POST",
                url    : "{{ route('document.jenisDocument') }}",
                data   : {
                    _token: '{{ csrf_token() }}',
                    jenis_document: jenisDocument
                },
                success: function(response) {    

                    submitButton.prop('disabled',false);
                    submitButtonLoading.toggle();

                    if (response.status == 200) {
                        window.location.href = url+'?jenis_document='+jenisDocument;

                        $('#modal-pilih-jenis').modal('hide');
                        $('.btn-cancel-jenis').toggle();
                        $('#jenis-document').val('').trigger('change');
                    } else if (response.status == 400) {
                        $.each(response.errors.jenis_document, function(key, error) {
                            $('#error-jenis-document').append(error);
                        });

                        $('.btn-cancel-jenis').toggle();
                    } else {
                        $('.btn-cancel-jenis').toggle();

                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    }
                },
                error: function(response){
                    submitButton.prop('disabled',false);
                    submitButtonLoading.toggle();

                    $('.btn-cancel-jenis').toggle();

                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Submit Data Signature pad
    });
</script>
@endpush