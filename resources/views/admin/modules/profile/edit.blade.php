@extends('master.admin.app')

@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="p-0 rounded-bottom">
                    <ul class="align-items-center m-0 p-0" id="profile-tab">
                        <li class="align-items-center p-2 p-sm-3 {{ active_primary_class(['profile/user']) }}">
                            <i class="me-1 icon-md" data-feather="user"></i>
                            <a class="pt-1px" href="{{ route('admin.profile.edit') }}">Data Diri & Update Password</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row profile-body">
        <div class="col-12 grid-margin">
            <div class="card">
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
                                        
                    <div class="alert alert-fill-primary p-2" role="alert">
                        Data Diri Pribadi
                    </div>
                    <form action="{{ route('admin.profile.update', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="mb-4">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input name="nama_lengkap" type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" style="text-transform: uppercase"
                                        placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap') ?? $data->name }}">
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <hr class="mt-0">

                        <div class="text-end">
                            <button class="btn btn-success" type="submit">{{ $btnSubmit }}</button>
                        </div>
                    </form>

                    <hr>

                    <div class="alert alert-fill-danger p-2" role="alert">
                        Update Password Login
                    </div>
                    <form action="{{ route('admin.password.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
    
                        <div class="alert alert-danger p-2" role="alert">
                            Hanya isi jika ingin mengubah kata sandi
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                                    <input name="current_password" id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Masukkan kata sandi saat ini" value="">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Kata Sandi Baru</label>
                                    <input name="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Masukkan kata sandi baru" value="">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Ulangi Kata Sandi</label>
                                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="Ulangi kata sandi baru" value="">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <hr class="mt-3">
    
                        <div class="text-end">
                            <button class="btn btn-danger" type="submit">Ubah Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
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
</script>
@endpush