<aside class="main-sidebar sidebar-dark-danger elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url(auth()->user()->role == 'mahasiswa' ? 'setprivilege' : '') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/favicon-32x32.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold">KEUANGAN UKM INSTIKI</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image my-auto">
                @if (auth()->user()->foto == null)
                    <i class="fas fa-user-circle text-white fa-3x"></i>
                @else
                    <img src="{{ asset('users/' . auth()->user()->foto . '') }}" class="brand-image img-circle">
                @endif
            </div>
            <div class="info">
                <a href="#" class="d-block font-weight-bold">
                    @auth
                        {{ strtoupper(auth()->user()->role == 'mahasiswa' ? auth()->user()->mhs->name : auth()->user()->name) }}
                    @endauth
                </a>
                <span class="badge badge-pill badge-danger">
                    {{ auth()->user()->role == 'mahasiswa' ? Session::get('jabatan') : auth()->user()->role }}
                </span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item has-treeview {{ request()->is('dashboard') ? 'menu-open' : '' }}">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if (in_array(auth()->user()->role, ['mahasiswa', 'pembina']))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('anggota.ukm') }}"
                            class="nav-link {{ request()->is('anggota-ukm') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Anggota
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{ route('akun') }}" class="nav-link {{ request()->is('akun') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-list"></i>
                            <p>
                                Data Akun
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{ route('jurnal') }}"
                            class="nav-link {{ request()->is('jurnal') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-book"></i>
                            <p>
                                Jurnal Umum
                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('transaksi/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('transaksi/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-edit"></i>
                            <p>
                                Transaksi
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('pembayaran') }}"
                                    class="nav-link {{ request()->is('transaksi/pembayaran') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    {{-- <p>Pembayaran</p> --}}
                                    <p>Iuran UKM</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('pemasukan') }}"
                                    class="nav-link {{ request()->is('transaksi/pemasukan') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Pemasukan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('pengeluaran') }}"
                                    class="nav-link {{ request()->is('transaksi/pengeluaran') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Pengeluaran</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{ route('kegiatan') }}"
                            class="nav-link {{ request()->is('kegiatan') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-list"></i>
                            <p>
                                Rincian Kegiatan
                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('laporan/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('laporan/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-print"></i>
                            <p>
                                Laporan UKM
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('laporan/jurnal') }}"
                                    class="nav-link {{ request()->is('laporan/jurnal') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Jurnal Umum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('laporan/lpj') }}"
                                    class="nav-link {{ request()->is('laporan/lpj') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan LPJ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('laporan/aruskas') }}"
                                    class="nav-link {{ request()->is('laporan/aruskas') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Arus Kas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- @elseif (auth()->user()->role == 'pembina')
                    <li class="nav-item has-treeview">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Menu
                            </p>
                        </a>
                    </li> --}}
                @elseif (auth()->user()->role == 'kemahasiswaan')
                    <li class="nav-item has-treeview">
                        <a href="{{ route('mahasiswa') }}"
                            class="nav-link {{ request()->is('mahasiswa') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>
                                Mahasiswa
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{ route('pembina') }}"
                            class="nav-link {{ request()->is('pembina') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Pembina UKM
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{ route('ukms') }}" class="nav-link {{ request()->is('ukms') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-list"></i>
                            <p>
                                UKM
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{ route('user') }}" class="nav-link {{ request()->is('user') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-key"></i>
                            <p>
                                User Setting
                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('laporan/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('laporan/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-print"></i>
                            <p>
                                Laporan UKM
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href="{{ url('laporan/aruskas') }}" class="nav-link">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Arus Kas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('profile') }}" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-user"></i>
                        <p>
                            Profile
                        </p>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
