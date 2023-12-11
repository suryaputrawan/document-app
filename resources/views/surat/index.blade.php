@extends('master.admin.app')

@push('plugin-styles')
  <link href="{{ asset('assets/admin/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
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
                    <a href="{{ route('document.create') }}" type="button" class="btn btn-sm btn-primary btn-icon-text">
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
@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/admin/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/admin/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
@endpush

@push('custom-scripts')
<script type="text/javascript">
    //Preview Image
    function previewImages() {
        var preview = document.querySelector('#preview');
        preview.innerHTML = '';
        var files = document.querySelector('input[type=file]').files;
    
        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
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

        // datatable initialization
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

        //----Modal
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
    });
</script>
@endpush