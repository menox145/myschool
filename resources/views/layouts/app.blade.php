<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - MySchool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap');

        * {
            font-family: 'Nunito', sans-serif;
        }

        .sidebar-shell {
            width: 16rem;
            transition: width .2s ease, transform .2s ease;
        }

        .sidebar-link,
        .sidebar-group-button {
            transition: background .15s ease, color .15s ease, padding .2s ease;
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.14);
            border-left: 3px solid #93c5fd;
        }

        .sidebar-link:hover,
        .sidebar-group-button:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-collapsed .sidebar-shell {
            width: 5rem;
        }

        .sidebar-collapsed .sidebar-label,
        .sidebar-collapsed .sidebar-section-label,
        .sidebar-collapsed .sidebar-chevron {
            display: none;
        }

        .sidebar-collapsed .sidebar-brand {
            justify-content: center;
        }

        .sidebar-collapsed .sidebar-link,
        .sidebar-collapsed .sidebar-group-button {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-collapsed .sidebar-link i,
        .sidebar-collapsed .sidebar-group-button i:first-child {
            margin-right: 0;
        }

        .sidebar-collapsed .sidebar-submenu {
            display: none;
        }

        @media (max-width: 767px) {
            .sidebar-shell {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 50;
                transform: translateX(-100%);
            }

            .sidebar-mobile-open .sidebar-shell {
                transform: translateX(0);
            }

            .sidebar-collapsed .sidebar-shell {
                width: 16rem;
            }

            .sidebar-collapsed .sidebar-label,
            .sidebar-collapsed .sidebar-section-label,
            .sidebar-collapsed .sidebar-chevron {
                display: inline;
            }

            .sidebar-collapsed .sidebar-link,
            .sidebar-collapsed .sidebar-group-button {
                justify-content: flex-start;
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .sidebar-collapsed .sidebar-link i,
            .sidebar-collapsed .sidebar-group-button i:first-child {
                margin-right: .75rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    @php
        $akademikOpen =
            request()->routeIs('nilai.*') ||
            request()->routeIs('rapot.cetak*') ||
            request()->routeIs('absen*') ||
            request()->routeIs('siswa.riwayat*') ||
            request()->routeIs('kenaikan-kelas*');
        $masterOpen =
            request()->routeIs('tahun-pelajaran.*') ||
            request()->routeIs('guru') ||
            request()->routeIs('kelas') ||
            request()->routeIs('siswa') ||
            request()->routeIs('mapel.*') ||
            request()->routeIs('kelas-mapel.*');
    @endphp

    <div id="appShell" class="flex h-screen overflow-hidden">
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden md:hidden"></div>

        <aside id="sidebar"
            class="sidebar-shell bg-gradient-to-b from-blue-900 to-blue-950 text-white flex-shrink-0 shadow-xl">
            <div class="sidebar-brand h-16 px-4 flex items-center justify-between border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center min-w-0">
                    <span class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-school text-xl"></i>
                    </span>
                    <span class="sidebar-label ml-3 text-xl font-bold truncate">MySchool</span>
                </a>
                <button type="button" id="closeSidebarMobile"
                    class="md:hidden w-9 h-9 rounded hover:bg-white/10 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="h-[calc(100vh-4rem)] overflow-y-auto py-4">
                <a href="{{ route('dashboard') }}" title="Dashboard"
                    class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('dashboard')) active @endif">
                    <i class="fas fa-fw fa-tachometer-alt mr-3 w-5 text-center"></i>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                @if (in_array(auth()->user()->role, ['admin', 'user']))
                    <div class="mt-3">
                        <button type="button" data-sidebar-toggle="akademik"
                            class="sidebar-group-button w-full flex items-center px-6 py-2 text-xs font-bold text-blue-200 uppercase tracking-wide">
                            <i class="fas fa-graduation-cap mr-3 w-5 text-center"></i>
                            <span class="sidebar-label flex-1 text-left">Akademik</span>
                            <i id="icon-akademik"
                                class="sidebar-chevron fas fa-chevron-down text-[10px] transition-transform @if ($akademikOpen) rotate-180 @endif"></i>
                        </button>

                        <div id="submenu-akademik" class="sidebar-submenu mt-1 @if (!$akademikOpen) hidden @endif">
                            <a href="{{ route('nilai.harian') }}" title="Nilai Harian"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('nilai.harian*')) active @endif">
                                <i class="fas fa-edit mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Nilai Harian</span>
                            </a>

                            <a href="{{ route('nilai.uh') }}" title="Nilai UH"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('nilai.uh*')) active @endif">
                                <i class="fas fa-clipboard-check mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Nilai UH</span>
                            </a>

                            <a href="{{ route('nilai.index') }}" title="Nilai Akhir"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('nilai.index')) active @endif">
                                <i class="fas fa-calculator mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Nilai Akhir</span>
                            </a>

                            <a href="{{ route('rapot.cetak.index') }}" title="Cetak Rapot"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('rapot.cetak*')) active @endif">
                                <i class="fas fa-print mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Cetak Rapot</span>
                            </a>

                            <a href="{{ route('absen.index') }}" title="ABSEN Siswa"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('absen*')) active @endif">
                                <i class="fas fa-user-check mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">ABSEN Siswa</span>
                            </a>

                            <a href="{{ route('siswa.riwayat.index') }}" title="Riwayat Siswa"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('siswa.riwayat*')) active @endif">
                                <i class="fas fa-history mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Riwayat Siswa</span>
                            </a>

                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('kenaikan-kelas.index') }}" title="Kenaikan Kelas"
                                    class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('kenaikan-kelas*')) active @endif">
                                    <i class="fas fa-level-up-alt mr-3 w-5 text-center"></i>
                                    <span class="sidebar-label">Kenaikan Kelas</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if (auth()->user()->role === 'admin')
                    <div class="mt-3">
                        <button type="button" data-sidebar-toggle="master"
                            class="sidebar-group-button w-full flex items-center px-6 py-2 text-xs font-bold text-blue-200 uppercase tracking-wide">
                            <i class="fas fa-database mr-3 w-5 text-center"></i>
                            <span class="sidebar-label flex-1 text-left">Master Data</span>
                            <i id="icon-master"
                                class="sidebar-chevron fas fa-chevron-down text-[10px] transition-transform @if ($masterOpen) rotate-180 @endif"></i>
                        </button>

                        <div id="submenu-master" class="sidebar-submenu mt-1 @if (!$masterOpen) hidden @endif">
                            <a href="{{ route('tahun-pelajaran.index') }}" title="Tahun Pelajaran"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('tahun-pelajaran.*')) active @endif">
                                <i class="fas fa-calendar mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Tahun Pelajaran</span>
                            </a>

                            <a href="{{ route('guru') }}" title="Data Guru"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('guru')) active @endif">
                                <i class="fas fa-fw fa-chalkboard-teacher mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Data Guru</span>
                            </a>

                            <a href="{{ route('kelas') }}" title="Data Kelas"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('kelas')) active @endif">
                                <i class="fas fa-fw fa-door-open mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Data Kelas</span>
                            </a>

                            <a href="{{ route('siswa') }}" title="Data Siswa"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('siswa')) active @endif">
                                <i class="fas fa-fw fa-users mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Data Siswa</span>
                            </a>

                            <a href="{{ route('mapel.index') }}" title="Data Matapelajaran"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('mapel.*')) active @endif">
                                <i class="fas fa-book mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Data Matapelajaran</span>
                            </a>

                            <a href="{{ route('kelas-mapel.index') }}" title="Kelompok Belajar"
                                class="sidebar-link flex items-center px-6 py-2.5 text-sm @if (request()->routeIs('kelas-mapel.*')) active @endif">
                                <i class="fas fa-link mr-3 w-5 text-center"></i>
                                <span class="sidebar-label">Kelompok Belajar</span>
                            </a>
                        </div>
                    </div>
                @endif
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <nav class="bg-white shadow-sm h-16 flex items-center justify-between px-4 md:px-6">
                <div class="flex items-center min-w-0">
                    <button type="button" id="openSidebarMobile"
                        class="md:hidden mr-3 w-10 h-10 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button type="button" id="toggleSidebarDesktop"
                        class="hidden md:flex mr-3 w-10 h-10 rounded-lg text-gray-600 hover:bg-gray-100 items-center justify-center"
                        title="Buka/tutup sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="text-lg font-semibold text-gray-700 truncate">
                        @yield('title', 'Dashboard')
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-gray-700 text-sm hidden sm:inline">
                        <i class="fas fa-user-circle mr-1"></i>
                        {{ Auth::user()->name }}
                    </span>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function() {
            const shell = document.getElementById('appShell');
            const overlay = document.getElementById('sidebarOverlay');
            const desktopToggle = document.getElementById('toggleSidebarDesktop');
            const mobileOpen = document.getElementById('openSidebarMobile');
            const mobileClose = document.getElementById('closeSidebarMobile');
            const collapsedKey = 'myschool.sidebar.collapsed';

            if (localStorage.getItem(collapsedKey) === '1') {
                shell.classList.add('sidebar-collapsed');
            }

            function openMobileSidebar() {
                shell.classList.add('sidebar-mobile-open');
                overlay.classList.remove('hidden');
            }

            function closeMobileSidebar() {
                shell.classList.remove('sidebar-mobile-open');
                overlay.classList.add('hidden');
            }

            desktopToggle?.addEventListener('click', function() {
                shell.classList.toggle('sidebar-collapsed');
                localStorage.setItem(collapsedKey, shell.classList.contains('sidebar-collapsed') ? '1' : '0');
            });

            mobileOpen?.addEventListener('click', openMobileSidebar);
            mobileClose?.addEventListener('click', closeMobileSidebar);
            overlay?.addEventListener('click', closeMobileSidebar);

            document.querySelectorAll('[data-sidebar-toggle]').forEach(function(button) {
                button.addEventListener('click', function() {
                    if (shell.classList.contains('sidebar-collapsed') && window.innerWidth >= 768) {
                        shell.classList.remove('sidebar-collapsed');
                        localStorage.setItem(collapsedKey, '0');
                    }

                    const name = button.dataset.sidebarToggle;
                    const submenu = document.getElementById('submenu-' + name);
                    const icon = document.getElementById('icon-' + name);
                    submenu?.classList.toggle('hidden');
                    icon?.classList.toggle('rotate-180');
                });
            });

            document.querySelectorAll('.sidebar-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeMobileSidebar();
                    }
                });
            });
        })();
    </script>
</body>

</html>
