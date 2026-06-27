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

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #4e73df;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-blue-800 to-blue-900 text-white flex-shrink-0">
            <div class="p-4 flex items-center justify-center border-b border-blue-700">
                <i class="fas fa-school text-2xl mr-2"></i>
                <span class="text-xl font-bold">MySchool</span>
            </div>

            <nav class="mt-4">
                <a href="{{ route('dashboard') }}"
                    class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('dashboard')) active @endif">
                    <i class="fas fa-fw fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>

                {{-- AKADEMIK: Admin + User --}}
                @if (in_array(auth()->user()->role, ['admin', 'user']))
                    <div class="px-6 py-2 mt-4 text-xs font-bold text-blue-300 uppercase">Akademik</div>

                    <a href="{{ route('nilai.harian') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('nilai.harian*')) active @endif">
                        <i class="fas fa-edit mr-3"></i>
                        Nilai Harian
                    </a>

                    <a href="{{ route('nilai.uh') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('nilai.uh*')) active @endif">
                        <i class="fas fa-clipboard-check mr-3"></i>
                        Nilai UH
                    </a>

                    <a href="{{ route('nilai.index') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('nilai.index')) active @endif">
                        <i class="fas fa-calculator mr-3"></i>
                        Nilai Akhir
                    </a>

                    <a href="{{ route('rapot.cetak.index') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('rapot.cetak*')) active @endif">
                        <i class="fas fa-print mr-3"></i>
                        Cetak Rapot
                    </a>
                @endif

                {{-- MASTER DATA: Admin Only --}}
                @if (auth()->user()->role === 'admin')
                    <div class="px-6 py-2 mt-4 text-xs font-bold text-blue-300 uppercase">Master Data</div>

                    <a href="{{ route('tahun-pelajaran.index') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('tahun-pelajaran.*')) active @endif">
                        <i class="fas fa-calendar mr-3"></i>
                        Tahun Pelajaran
                    </a>

                    <a href="{{ route('user.indexs') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('guru')) active @endif">
                        <i class="fas fa-fw fa-chalkboard-teacher mr-3"></i>
                        Data Guru
                    </a>

                    <a href="{{ route('kelas') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('kelas')) active @endif">
                        <i class="fas fa-fw fa-door-open mr-3"></i>
                        Data Kelas
                    </a>

                    <a href="{{ route('siswa') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('siswa')) active @endif">
                        <i class="fas fa-fw fa-users mr-3"></i>
                        Data Siswa
                    </a>

                    <a href="{{ route('mapel.index') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('mapel.*')) active @endif">
                        <i class="fas fa-book mr-3"></i>
                        Data Matapelajaran
                    </a>

                    <a href="{{ route('kelas-mapel.index') }}"
                        class="sidebar-link flex items-center px-6 py-3 text-sm @if (request()->routeIs('kelas-mapel.*')) active @endif">
                        <i class="fas fa-link mr-3"></i>
                        Kelompok Belajar
                    </a>
                @endif
            </nav>
        </div>
        <!-- HAPUS MENU RAPOT YANG DI SINI -->


        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <nav class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
                <div class="text-lg font-semibold text-gray-700">
                    @yield('title', 'Dashboard')
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-gray-700 text-sm">
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

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSubmenu(menu) {
            const submenu = document.getElementById('submenu-' + menu);
            const icon = document.getElementById('icon-' + menu);
            submenu.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Auto buka submenu kalo lagi di halaman rapot
        @if (request()->is('nilai*') || request()->is('rapot/cetak*') || request()->is('kelas-mapel*'))
            document.getElementById('submenu-rapot').classList.remove('hidden');
            document.getElementById('icon-rapot').classList.add('rotate-180');
        @endif
    </script>
</body>

</html>
