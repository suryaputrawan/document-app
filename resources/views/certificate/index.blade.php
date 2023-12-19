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
                <h6 class="card-title flex-full-width mb-0">Certificates</h6>
                @can('create certificate')
                    <a id="btn-create" type="button" class="btn btn-sm btn-primary btn-icon-text">
                        <i class="btn-icon-prepend" data-feather="plus"></i>
                        Tambah
                    </a>
                @endcan
            </div>
            <div class="card-body">
                @if ($dataEndDate->count() >= 1)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <span class="mb-3">You Have {{ $dataEndDate->count() }} certificates that will expired !</span>
                        @foreach ($dataEndDate as $item)
                            <li>{{ $item->certificate_number }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}</li>
                        @endforeach
                    </div>                   
                @endif
                
                <div>
                    <table id="datatable" class="table dataTable table-sm table-wrapped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 20px">NO</th>
                                @canany(['update certificate', 'view certificate', 'delete certificate'])
                                <th style="width: 100px">ACTION</th> 
                                @endcanany
                                <th>NAME</th>
                                <th>NUMBER</th>
                                <th>START DATE</th>
                                <th>END DATE</th>
                                <th>TYPE</th>
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
@include('certificate.modal.action')
@include('certificate.modal.view')
@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/admin/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/admin/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@push('custom-scripts')
<script type="text/javascript">
    $('#type').select2({
        dropdownParent: $('#modal-create-certificate')
    });

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
            ajax: "{{ route('certificates.index') }}?type=datatable",
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
                @canany(['update certificate', 'view certificate', 'delete certificate'])
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                @endcanany
                { data: "name", name: "name", orderable: true  },
                { data: "certificate_number", name: "certificate_number", orderable: true  },
                { data: "start_date", name: "start_date", orderable: false, searchable: false, className: "text-center", },
                { data: "end_date", name: "end_date", orderable: false, searchable: false, className: "text-center", },
                { data: "type", name: "type", orderable: true, className: "text-center", },
            ],
            drawCallback: function( settings ) {
                feather.replace()
            }
        });

        //----Form environment
        function clearForm() {
            $("#certificate-form").find('input').val("");
            $('#certificate-form').find('.error').text("");

            $("#type").val("").trigger('change');
            var preview = document.querySelector('#preview');
            preview.innerHTML = '';

            ajaxUrl = "{{ route('certificates.store') }}";
            ajaxType = "POST";
        }

        //----Modal Upload Signature
        $(document).on('click', '#btn-create', function(e) {
            $('#modal-create-certificate').modal('show');
            $('#title-modal').text('Create Certificate Data');
            $("#btn-submit-text").text("Save");

            ajaxUrl = "{{ route('certificates.store') }}";
            ajaxType = "POST";
        });

        $(".btn-cancel").click(function() {
            $('#modal-create-certificate').modal('hide');
            clearForm();
        });
        //----End Modal

        //------ Submit Data
        $('#certificate-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#certificate-form').find('.error').text("");

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
                        $('#modal-create-certificate').modal('hide');
                        $('#datatable').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.certificate_number, function(key, error) {
                            $('#error-certificate-number').append(error);
                        });
                        $.each(response.errors.type, function(key, error) {
                            $('#error-type').append(error);
                        });
                        $.each(response.errors.name, function(key, error) {
                            $('#error-name').append(error);
                        });
                        $.each(response.errors.start_date, function(key, error) {
                            $('#error-start-date').append(error);
                        });
                        $.each(response.errors.end_date, function(key, error) {
                            $('#error-end-date').append(error);
                        });
                        $.each(response.errors.employee, function(key, error) {
                            $('#error-employee').append(error);
                        });
                        $.each(response.errors.file, function(key, error) {
                            $('#error-file').append(error);
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
            e.preventDefault();
            $('#modal-create-certificate').modal('show');
            $('#title').text('Edit Certificate Data');

            var id = $(this).data('id');
            var url = $(this).data('url');

            $('#certificate-form').find('.error').text("");

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

            $("#btn-submit-text").text("Save Change");

            $.ajax({
                type   : "GET",
                url    : url,
                success: function(response) {
                    if (response.status == 404) {
                        clearForm();
                        $('#modal-create-certificate').modal('hide');
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('certificates.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#certificate-number').val(response.data.certificate_number);
                        $('#type').val(response.data.certificate_type_id).trigger('change');
                        $('#name').val(response.data.name);
                        $('#start-date').val(response.data.start_date);
                        $('#end-date').val(response.data.end_date);
                        $('#employee').val(response.data.employee_name);
                        $('#preview').eq(0).html('<img src="/storage/'+response.data.file+'"height="150" alt="Preview File">');
                    }
                },
                error: function(response){
                    $('#modal-create-certificate').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Load data to edit

        //------ Modal View Detail
        $(".btn-cancel-view").click(function() {
            $('#modal-view-certificate').modal('hide');
        });

        $(document).on('click', '#btn-view', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).data('url');

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
                type   : "GET",
                url    : url,
                dataType: "json",
                success: function(response) {

                    console.log(response);

                    if (response.status == 404) {
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        $('#modal-view-certificate').modal('show');
                        $('#title-modal-view').text('Detail Certificate');

                        var arrbulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                        let tempStartDate = new Date(response.data.start_date);
                        let tempEndDate = new Date(response.data.end_date);
                        let startDate = [ tempStartDate.getDate(), arrbulan[parseInt(tempStartDate.getMonth() + 1)], tempStartDate.getFullYear()].join(' ');
                        let endDate = [ tempEndDate.getDate(), arrbulan[parseInt(tempEndDate.getMonth() + 1)], tempEndDate.getFullYear()].join(' ');

                        $('#certificate-number-view').text(response.data.certificate_number);
                        $('#certificate-name').text(response.data.name);
                        $('#certificate-type').text(response.data.certificate_type.name);
                        $('#certificate-start-date').text(startDate);
                        $('#certificate-end-date').text(endDate);
                        $('#certificate-employee').text(response.data.employee_name);
                        response.data.file ? $('#certificate-file').html('<a href="/storage/'+response.data.file+'" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">View File') : $('#certificate-file').text('');
                    }
                },
                error: function(response){
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End View Detail
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
</script>
@endpush