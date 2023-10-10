@extends('master.admin.app-auth')

@push('plugin-styles')
<link href="{{ asset('assets/admin/plugins/@mdi/css/materialdesignicons.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="page-content d-flex align-items-center justify-content-center">
    <div class="row w-100 mx-0 auth-page">
        <div class="col-md-10 col-xl-8 mx-auto">
            <div class="card">
                <div class="row">
                    <div class="col-md-4 pe-md-0">
                        <div class="auth-side-wrapper" style="background-image: url({{ asset('assets/admin/images/auth-background.jpeg') }})">
                        </div>
                    </div>
                    <div class="col-md-8 ps-md-0">
                        <div class="auth-form-wrapper px-4 py-5">
                            <img class="d-block d-md-none" src="{{ asset('assets/admin/images/logo-text.png') }}" height="50px" alt="PERSI Website">
                            <a href="#!" class="d-none d-md-block noble-ui-logo d-block">Doctor <span>Directory</span></a>
                            <h5 class="text-muted fw-normal mb-3">Selamat datang kembali ~</h5>
                            @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                                {{ session()->get('error') }}
                            </div>
                            @php
                            Session::forget('error');
                            @endphp
                            @endif
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="userEmail" class="form-label">Username</label>
                                    <input required name="username" type="text" class="form-control" id="userEmail"
                                        placeholder="NIP Karyawan">
                                </div>
                                {{-- <div class="mb-3">
                                    <label for="userEmail" class="form-label">Email</label>
                                    <input required name="email" type="email" class="form-control" id="userEmail"
                                        placeholder="user@mail.com">
                                </div> --}}
                                <label for="userPassword" class="form-label" style="width: 100%">
                                  <span>Kata Sandi</span>
                                  {{-- <a href="{{ route('password.request') }}">
                                    <small id="userPasswordHelp" class="form-text text-muted float-right m-0">Lupa Kata Sandi?</small>
                                  </a> --}}
                                </label>
                                <div class="input-group mb-3">
                                    <input required name="password" type="password" class="form-control"
                                        id="userPassword" autocomplete="current-password">
                                      <span class="input-group-text input-group-addon" data-password="false" id="password-icon">
                                        <i data-feather="eye"></i>
                                      </span>
                                </div>
                                {{-- <div class="form-check mb-3">
                                    <input name="remember" type="checkbox" class="form-check-input" id="authCheck">
                                    <label class="form-check-label" for="authCheck">
                                        Ingat Saya
                                    </label>
                                </div> --}}
                                <div>
                                    <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Login</button>
                                </div>
                                {{-- <a href="{{ route('register') }}" class="d-block mt-4 text-muted text-end">Belum punya akun?
                                    <span class="text-primary">Daftar Sekarang</span></a> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
  <script>
    $("[data-password]").on('click', function () {
        if ($(this).attr('data-password') == "false") {
          $(this).siblings("input").attr("type", "text");
          $(this).attr('data-password', 'true');
          $(this).html(feather.icons['eye-off'].toSvg());
        } else {
          $(this).siblings("input").attr("type", "password");
          $(this).attr('data-password', 'false');
          $(this).html(feather.icons['eye'].toSvg());
        }
    });
  </script>
@endpush