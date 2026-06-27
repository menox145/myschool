@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('content')
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-book text-blue-600 mr-2"></i>Data Mata Pelajaran
                </h2>
                @empty($mapel)
                    <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-2"></i>Tambah Mapel
                    </button>
                @endempty
            </div>
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-6 mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FILTER --}}
        <div class="p-6 border-b">
            <form method="GET" action="{{ route('mapel.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode/nama mapel..."
                    class="px-3 py-2 border rounded-lg text-sm">
                <select name="kelompok" class="px-3 py-2 border rounded-lg text-sm">
                    <option value="">Semua Kelompok</option>
                    <option value="A" @selected(request('kelompok') == 'A')>Kelompok A</option>
                    <option value="B" @selected(request('kelompok') == 'B')>Kelompok B</option>
                    <option value="C" @selected(request('kelompok') == 'C')>Kelompok C</option>
                </select>
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Mapel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelompok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">KKM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Rapot</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mapels as $m)
                        {{-- PAKE $mapels, LOOP $m --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $m->kode_mapel }}</td>
                            <td class="px-6 py-4 text-sm">{{ $m->nama_mapel }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-2 py-1 text-xs rounded-full 
                            @if ($m->kelompok == 'A') bg-blue-100 text-blue-800
                            @elseif($m->kelompok == 'B') bg-green-100 text-green-800
                            @else bg-purple-100 text-purple-800 @endif">
                                    {{ $m->kelompok }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $m->kkm }}</td>
                            <td class="px-6 py-4 text-sm">{{ ucfirst($m->jenis_rapot) }}</td>
                            <td class="px-6 py-4 text-sm flex gap-2">
                                <a href="{{ route('mapel.edit', $m->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('mapel.destroy', $m->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus {{ $m->nama_mapel }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREATE / EDIT - 1 FILE BUAT 2 FUNGSI --}}
    <div id="modalMapel" class="fixed inset-0 bg-gray-600 bg-opacity-50 {{ isset($mapel) ? '' : 'hidden' }} z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">
                        {{ isset($mapel) ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
                    </h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ isset($mapel) ? route('mapel.update', $mapel->id) : route('mapel.store') }}"
                    method="POST">
                    @csrf
                    @isset($mapel)
                        @method('PUT')
                    @endisset

                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kode Mapel *</label>
                            <input type="text" name="kode_mapel"
                                value="{{ old('kode_mapel', $mapel->kode_mapel ?? '') }}" required maxlength="10"
                                class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Mapel *</label>
                            <input type="text" name="nama_mapel"
                                value="{{ old('nama_mapel', $mapel->nama_mapel ?? '') }}" required
                                class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">KKM *</label>
                                <input type="number" name="kkm" value="{{ old('kkm', $mapel->kkm ?? 75) }}" required
                                    min="0" max="100"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Kelompok *</label>
                                <select name="kelompok" required
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                    <option value="A" @selected(old('kelompok', $mapel->kelompok ?? '') == 'A')>A</option>
                                    <option value="B" @selected(old('kelompok', $mapel->kelompok ?? '') == 'B')>B</option>
                                    <option value="C" @selected(old('kelompok', $mapel->kelompok ?? '') == 'C')>C</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Jenis Rapot *</label>
                            <select name="jenis_rapot" required
                                class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="dinniyyah" @selected(old('jenis_rapot', $mapel->jenis_rapot ?? '') == 'dinniyyah')>Dinniyyah</option>
                                <option value="akademik" @selected(old('jenis_rapot', $mapel->jenis_rapot ?? '') == 'akademik')>Akademik</option>
                                <option value="tahfidz" @selected(old('jenis_rapot', $mapel->jenis_rapot ?? '') == 'tahfidz')>Tahfidz</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t flex justify-end gap-2">
                        @isset($mapel)
                            <a href="{{ route('mapel.index') }}"
                                class="px-4 py-2 border rounded-md hover:bg-gray-50">Batal</a>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                        @else
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 border rounded-md hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                        @endisset
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalMapel').classList.remove('hidden');
        }

        function closeModal() {
            @isset($mapel)
                window.location.href = "{{ route('mapel.index') }}";
            @else
                document.getElementById('modalMapel').classList.add('hidden');
            @endisset
        }

        // Auto open modal kalo lagi edit
        @isset($mapel)
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalMapel').classList.remove('hidden');
            });
        @endisset
    </script>
@endsection
