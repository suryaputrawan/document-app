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
@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/admin/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/admin/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
@endpush

@push('custom-scripts')
<script type="text/javascript">
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

        //---Sign Document
        const swalWithBootstrapButtonsConfirm = Swal.mixin();
        const swalWithBootstrapButtons = Swal.mixin();

        //Assign ticket
        $(document).on('click', '.sign-document', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).data('url');
            var label = $(this).data('label');

            swalWithBootstrapButtonsConfirm.fire({
                title: `Give signature on document ?`,
                text : `[ ${label} ]`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sign',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: url,
                        data: {
                            "_method": 'PUT',
                            "_token": "{{ csrf_token() }}",
                        }
                    }).then((data) => {
                        let message = 'Document has been signature..!';
                        if (data.message) {
                            message = data.message;
                        }
                        swalWithBootstrapButtons.fire('Success!', message, 'success');
                        $('#datatable').DataTable().ajax.reload();
                    }, (data) => {
                        let message = '';
                        if (data.responseJSON.message) {
                            message = data.responseJSON.message;
                        }
                        swalWithBootstrapButtons.fire('Oops!', `Signature document not work, ${message}`, 'error');
                        if (data.status === 404) {
                            $('#datatable').DataTable().ajax.reload();
                        }
                    });
                },
                allowOutsideClick: () => !swalWithBootstrapButtons.isLoading(),
                backdrop: true
            });
        });
        //---End Sign Document
    });
</script>
@endpush