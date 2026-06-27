@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Card Siswa -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase mb-1">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800">1,250</p>
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
                    <p class="text-2xl font-bold text-gray-800">85</p>
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
                    <p class="text-2xl font-bold text-gray-800">32</p>
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
                    <p class="text-2xl font-bold text-gray-800">18</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-book text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-2">
            Selamat datang kembali, {{ Auth::user()->name }}! 👋
        </h3>
        <p class="text-gray-600">Ini adalah dashboard MySchool. Kamu bisa mengelola data siswa, guru, dan kelas dari menu di
            samping.</p>
    </div>
@endsection
