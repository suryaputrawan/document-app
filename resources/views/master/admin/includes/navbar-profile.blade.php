<div class="p-0 rounded-bottom">
    <ul class="align-items-center m-0 p-0" id="profile-tab">
        <li class="align-items-center p-2 p-sm-3 {{ active_primary_class(['profile/user']) }}">
            <i class="me-1 icon-md" data-feather="user"></i>
            <a class="pt-1px" href="{{ route('profile.edit') }}">Data Diri</a>
        </li>
        <div class="divider"></div>
        <li class="align-items-center p-2 p-sm-3 {{ active_primary_class(['profile/user/jabatan-user']) }}">
            <i class="me-1 icon-md" data-feather="briefcase"></i>
            <a class="pt-1px" href="{{ route('jabatan-user.index') }}">Jabatan</a>
        </li>
        <div class="divider"></div>
        <li class="align-items-center p-2 p-sm-3 {{ active_primary_class(['profile/user/diklat-user']) }}">
            <i class="me-1 icon-md" data-feather="book-open"></i>
            <a class="pt-1px" href="{{ route('diklat-user.index') }}">Diklat</a>
        </li>
        <div class="divider"></div>
        <li class="align-items-center p-2 p-sm-3 {{ active_primary_class(['profile/password/edit']) }}">
            <i class="me-1 icon-md" data-feather="key"></i>
            <a class="pt-1px" href="{{ route('profile.password.edit') }}">Pengaturan Akun</a>
        </li>
    </ul>
</div>