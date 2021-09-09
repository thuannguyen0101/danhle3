<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i
            class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('department') }}'><i class='nav-icon la la-archive'></i>
        Phòng</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('team') }}'><i class='nav-icon la la-users'></i> Nhóm </a>
</li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('profile-work') }}'><i
            class='nav-icon la la-pencil-square'></i> Thông Tin Công Việc </a></li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('request') }}'><i class='nav-icon la la-question'></i>
        Gửi Yêu Cầu</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Quản Lý Người Dùng </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i>
                <span>Người Dùng</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i
                    class="nav-icon la la-id-badge"></i> <span>Vai trò</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i
                    class="nav-icon la la-key"></i> <span>Quyền</span></a></li>
    </ul>
</li>
