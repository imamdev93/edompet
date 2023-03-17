<div class="sidebar-collapse">
    <ul class="nav metismenu" id="side-menu">
        <li class="nav-header">
            <div class="dropdown profile-element">
                {{-- <img alt="image" class="rounded-circle" src="img/profile_small.jpg" /> --}}
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <h3 class="block m-t-xs font-bold">E-DOMPET</h3>
                    {{-- <span class="text-muted text-xs block">Art Director <b class="caret"></b></span> --}}
                </a>
                {{-- <ul class="dropdown-menu animated fadeInRight m-t-xs">
                    <li><a class="dropdown-item" href="profile.html">Profile</a></li>
                    <li><a class="dropdown-item" href="contacts.html">Contacts</a></li>
                    <li><a class="dropdown-item" href="mailbox.html">Mailbox</a></li>
                    <li class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="login.html">Logout</a></li>
                </ul> --}}
            </div>
            <div class="logo-element">
                ED
            </div>
        </li>
        <li class="{{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> <span class="nav-label">Dashboard</span></a>
        </li>
        <li class="{{ Request::routeIs('user.*') ? 'active' : '' }}">
            <a href="{{ route('user.index') }}"><i class="fa fa-users"></i> <span class="nav-label">User</span></a>
        </li>
        <li class="{{ Request::routeIs('category.*') ? 'active' : '' }}">
            <a href="{{ route('category.index') }}"><i class="fa fa-list"></i> <span class="nav-label">Kategori</span></a>
        </li>
        <li class="{{ Request::routeIs('wallet.*') ? 'active' : '' }}">
            <a href="{{ route('wallet.index') }}"><i class="fa fa-credit-card"></i> <span class="nav-label">Dompet</span></a>
        </li>
        <li class="{{ Request::routeIs('transaction.*') ? 'active' : '' }}">
            <a href="{{ route('transaction.index') }}"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Transaksi</span></a>
        </li>
        <li class="{{ Request::routeIs('transfer.*') ? 'active' : '' }}">
            <a href="{{ route('transfer.index') }}"><i class="fa fa-money"></i> <span class="nav-label">Transfer</span></a>
        </li>
        <li class="{{ Request::routeIs('payable.*') ? 'active' : '' }}">
            <a href="{{ route('payable.index') }}"><i class="fa fa-money"></i> <span class="nav-label">Hutang</span></a>
        </li>
        <li class="{{ Request::routeIs('receivable.*') ? 'active' : '' }}">
            <a href="{{ route('receivable.index') }}"><i class="fa fa-money"></i> <span class="nav-label">Piutang</span></a>
        </li>
    </ul>

</div>
