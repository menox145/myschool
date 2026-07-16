@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Card Siswa -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase mb-1">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalSiswa }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Card Guru -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-green-600 uppercase mb-1">Total Guru</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalGuru }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Card Kelas -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-yellow-600 uppercase mb-1">Total Kelas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalKelas }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-door-open text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Card Mapel -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase mb-1">Mata Pelajaran</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalMapel }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-book text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-2">
            Selamat datang kembali, {{ Auth::user()->name }}! 👋
        </h3>
        <p class="text-gray-600">Ini adalah dashboard MySchool. Kamu bisa mengelola data siswa, guru, dan kelas dari menu di
            samping.</p>
    </div>

    <div class="bg-linear-to-br from-slate-50 via-white to-blue-50 rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-blue-100 p-2.5 text-blue-600">
                    <i class="fas fa-calendar-check text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Jadwal Guru Piket</h3>
                    <p class="text-sm text-gray-500">Daftar penanggung jawab piket setiap hari</p>
                </div>
            </div>
            <div
                class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">
                <i class="fas fa-clock text-xs"></i>
                Senin - Jumat
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            @forelse ($piket as $hari => $items)
                <div
                    class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-blue-300">
                    <div class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-blue-500 to-indigo-500"></div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-slate-700">{{ $hari }}</p>
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-600">
                            {{ count($items) }} orang
                        </span>
                    </div>
                    <div class="space-y-2">
                        @foreach ($items as $item)
                            <div class="flex items-start gap-2 rounded-lg bg-slate-50 px-3 py-2">
                                <div class="mt-0.5 rounded-full bg-blue-100 p-1">
                                    <i class="fas fa-user-shield text-[10px] text-blue-600"></i>
                                </div>
                                <p class="text-sm text-slate-600">{{ $item->guru->nama ?? 'Belum ada guru' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="md:col-span-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <p class="font-semibold text-slate-700">Belum ada jadwal piket.</p>
                    <p class="mt-1 text-sm text-slate-500">Jadwal akan muncul di sini setelah ditambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
