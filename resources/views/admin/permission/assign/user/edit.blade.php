@extends('master.admin.app')

@push('plugin-styles')
  <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Role & Permission</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.assign.user.index') }}">{{ $breadcrumb }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit {{ $breadcrumb }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-header flex flex-align-center">
                <h6 class="card-title flex-full-width mb-0">Edit {{ $breadcrumb }}</h6>
                <a href="{{ route('admin.assign.user.index') }}" type="button" class="btn btn-sm btn-secondary btn-icon-text">
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
                <form action="{{ route('admin.assign.user.edit', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user" class="js-example-basic-single form-select @error('user') is-invalid @enderror" data-width="100%">
                                    <option selected disabled>-- Select User --</option>
                                    @foreach ($users as $item)
                                    <option {{ $data->id == $item->id ? 'selected' : '' }} value="{{ $item->id }}"
                                        {{ old('user') == $item->id ? 'selected' : null }}>{{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label> Roles <span class="text-danger">*</span></label>
                                <div class="checkbox">
                                    @foreach ($roles as $item)
                                    <label class="p-2 mb-3">
                                        <input type="checkbox" class="@error('roles') is-invalid @enderror" name="roles[]" 
                                            value="{{ $item->id }}" {{ $data->roles()->find($item->id) ? "checked" : "" }} > {{ $item->name }}
                                    </label>
                                    @endforeach
                                </div>
                                @error('roles')
                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
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
                        <a href="{{ route('admin.assign.user.index') }}" class="btn btn-danger" id="btnCancel">Batal</a>
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