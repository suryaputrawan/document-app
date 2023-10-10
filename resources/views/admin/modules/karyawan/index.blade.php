@extends('master.admin.app')

@push('plugin-styles')
  <link href="{{ asset('assets/admin/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Master</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb }}</li>
    </ol>
</nav>
  
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header flex flex-align-center">
                <h6 class="card-title flex-full-width mb-0">Karyawan</h6>
                @can('create karyawan')
                    <button id="btn-add" type="button" class="btn btn-sm btn-primary btn-icon-text">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Tambah
                    </button>
                @endcan
            </div>
            <div id="item-loading" class="card-body" style="display: none">
                <div class="d-flex align-items-center">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                    <p>Load data...</p>
                </div>
            </div>
            <div id="item-layout" class="card-body" style="display: none">
                <form id="item-form">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="mb-3">
                                <label class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                                <input name="nama" id="name" type="text" class="form-control"
                                    placeholder="Masukkan nama lengkap karyawan" value="{{ old('nama') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input name="nip" id="nip" type="text" class="form-control"
                                    placeholder="Masukkan NIP karyawan" value="{{ old('nip') }}">
                                <p id="error-nip" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input name="jabatan" id="jabatan" type="text" class="form-control"
                                    placeholder="Masukkan jabatan karyawan" value="{{ old('jabatan') }}">
                                <p id="error-jabatan" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input name="email" id="email" type="email" class="form-control"
                                    placeholder="Masukkan email karyawan" value="{{ old('email') }}">
                                <p id="error-email" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Tanda Tangan </label>
                                <input name="gambar" type="file" class="form-control" accept="image/*" 
                                    value="{{ old('gambar') }}" onchange="previewImages()">
                                <p id="error-ttd" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div id="preview"></div>
                    </div>
                    <hr class="mt-2">
                    <div class="text-end">
                        <button class="btn btn-primary" type="submit">
                            <span id="submit-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="btn-submit-text">Simpan</span>
                        </button>
                        <button class="btn btn-danger btn-cancel" type="button">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table dataTable table-striped table-sm table-wrapped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 20px">NO</th>
                                <th style="width: 100px">ACTION</th>
                                <th>NAMA KARYAWAN</th>
                                <th>NIP</th>
                                <th>JABATAN</th>
                                <th>TTD</th>
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
@endsection


@push('plugin-scripts')
  {{-- <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script> --}}
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
    }

    $(document).ready(function(){
        // datatable initialization
        let dataTable = $("#datatable").DataTable({
            ...tableOptions,
            ajax: "{{ route('admin.karyawan.index') }}?type=datatable",
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
                { data: "nama", name: "nama", orderable: true  },
                { data: "nip", name: "nip", orderable: true  },
                { data: "jabatan", name: "jabatan", orderable: false, searchable:false  },
                { data: "gambar", name: "gambar", orderable: false, searchable:false  },
            ],
            drawCallback: function( settings ) {
                feather.replace()
            }
        });

        // form environtment
        let ajaxUrl = "{{ route('admin.karyawan.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#item-form").find('input').val("");
            $('#item-form').find('.error').text("");
            var preview = document.querySelector('#preview');
            preview.innerHTML = '';
        }

        //------- Show Hide Form
        $("#btn-add").click(function() {
            ajaxUrl = "{{ route('admin.karyawan.store') }}";
            ajaxType = "POST";
            $("#item-layout").show(500);
            $("#btn-add").toggle();
            $("#btn-submit-text").text("Simpan");
            clearForm();
        });

        $(".btn-cancel").click(function() {
            $("#item-layout").hide(500); 
            $("#btn-add").toggle();
            clearForm();
        });
        //------ End Show Hide Form

        //------ Submit Data
        $('#item-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#item-form').find('.error').text("");

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
                        clearForm();
                        $("#item-layout").hide(500);
                        $("#btn-add").toggle();
                        $('#datatable').DataTable().ajax.reload();
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.nama, function(key, error) {
                            $('#error-name').append(error);
                        });
                        $.each(response.errors.jabatan, function(key, error) {
                            $('#error-jabatan').append(error);
                        });
                        $.each(response.errors.nip, function(key, error) {
                            $('#error-nip').append(error);
                        });
                        $.each(response.errors.email, function(key, error) {
                            $('#error-email').append(error);
                        });
                        $.each(response.errors.gambar, function(key, error) {
                            $('#error-ttd').append(error);
                        });
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
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
        //------ End Submit Data

        //------ Load data to edit
        $(document).on('click', '#btn-edit', function(e) {
            var id = $(this).data('id');
            var url = $(this).data('url');

            $('#item-form').find('.error').text("");

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

            $('html, body').animate({
                scrollTop: eval($(".page-breadcrumb").offset().top - 80)
            }, 100);

            $("#item-loading").show(500);
            $("#item-layout").hide(500);
            $("#btn-add").hide();
            $("#btn-submit-text").text("Simpan Perubahan");

            $.ajax({
                type   : "GET",
                url    : url,
                success: function(response) {
                    
                    $("#item-loading").hide(500);
                    if (response.status == 404) {
                        $("#btn-add").show();
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.karyawan.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#name').val(response.data.nama);
                        $('#jabatan').val(response.data.jabatan);
                        $('#nip').val(response.data.nip);
                        $('#email').val(response.data.user.email);
                        $('#preview').eq(0).html('<img src="/storage/'+response.data.ttd_picture+'"height="150" alt="Preview Gambar">');

                        $("#item-layout").show(500);
                    }
                },
                error: function(response){
                    $("#item-loading").hide(500);
                    $("#btn-add").show();

                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Load data to edit

    });
</script>
@endpush