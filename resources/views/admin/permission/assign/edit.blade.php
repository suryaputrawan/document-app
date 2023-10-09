@extends('master.admin.app')

@push('plugin-styles')
  <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Role & Permission</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.assign.index') }}">{{ $breadcrumb }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sync {{ $breadcrumb }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-header flex flex-align-center">
                <h6 class="card-title flex-full-width mb-0">Sync {{ $breadcrumb }}</h6>
                <a href="{{ route('admin.assign.index') }}" type="button" class="btn btn-sm btn-secondary btn-icon-text">
                    <i class="btn-icon-prepend" data-feather="arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        {{ session()->get('error') }}
                    </div>
                    @php
                        Session::forget('error');
                    @endphp
                @endif
                <form action="{{ route('admin.assign.update', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="js-example-basic-single form-select @error('role') is-invalid @enderror" data-width="100%">
                                    <option selected disabled>-- Select Role --</option>
                                    @foreach ($roles as $item)
                                    <option {{ $data->id == $item->id ? 'selected' : '' }} value="{{ $item->id }}"
                                        {{ old('role') == $item->id ? 'selected' : null }}>{{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label class="form-label">Permissions <span class="text-danger">*</span></label>
                                <select name="permissions[]" class="js-example-basic-multiple form-select @error('permissions') is-invalid @enderror" data-width="100%" multiple="multiple">
                                    @foreach ($permissions as $item)
                                        <option {{ $data->permissions()->find($item->id) ? "selected" : "" }} value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('permissions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="text-end">
                        <button name="btnSimpan" class="btn btn-primary" type="submit" id="btnSave">{{ $btnSubmit }}</button>
                        <button class="btn btn-primary" type="submit" id="btnSave-loading" style="display: none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span>{{ $btnSubmit }}</span>
                        </button>
                        <a href="{{ route('admin.assign.index') }}" class="btn btn-danger" id="btnCancel">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/admin/js/select2.js') }}"></script>

<script type="text/javascript">
    //Toast for session success
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

    //environment button
    $('#btnSave').on('click', function () {
        $('#btnSave-loading').toggle();
        $('#btnSave-loading').prop('disabled',true);
        $('#btnSave').toggle();
        $('#btnCancel').toggle();
    });
    //end
</script>
@endpush