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

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Jadwal Guru Piket</h3>
            <span class="text-sm text-gray-500">Senin - Jumat</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @forelse ($piket as $hari => $items)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <p class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">{{ $hari }}</p>
                    <div class="space-y-2">
                        @foreach ($items as $item)
                            <p class="text-sm text-gray-600">{{ $item->guru->nama ?? 'Belum ada guru' }}</p>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="col-span-5 text-center text-gray-500 py-4">
                    Belum ada jadwal piket.
                </div>
            @endforelse
        </div>
    </div>
@endsection
