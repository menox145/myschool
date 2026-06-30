@extends('layouts.app')

@section('title', 'Kelompok Belajar')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Setting Kelompok Belajar</h3>
            <p class="text-sm text-gray-500">
                Tahun Pelajaran:
                <span class="font-medium text-blue-600">
                    @if ($tahunAktif)
                        {{ $tahunAktif->tahun }} - {{ $tahunAktif->semester }}
                    @else
                        <span class="text-red-600 font-bold">Belum ada tahun aktif!</span>
                    @endif
                </span>
            </p>
        </div>

        <!-- FILTER KELAS -->
        <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg border">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Kelas *</label>
                    <select name="kelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required
                        onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" @selected($kelasSelected == $k->id)>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if ($kelasSelected)
            <form action="{{ route('kelas-mapel.store') }}" method="POST"
                class="mb-6 bg-blue-50 border border-blue-200 p-4 rounded-lg">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelasSelected }}">
                <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahunAktif->id }}">

                <h4 class="font-semibold text-sm text-blue-800 mb-3"><i class="fas fa-plus-circle mr-1"></i>Tambah Mata
                    Pelajaran</h4>

                @foreach ($allMapel as $jenis => $mapels)
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-700 mb-2 uppercase">{{ $jenis }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach ($mapels as $m)
                                <div class="bg-white border rounded-lg p-3">
                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" name="mapel_ids[]" value="{{ $m->id }}"
                                            class="mt-1" @checked($kelasMapel->has($m->id))>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium">{{ $m->nama_mapel }}</p>
                                            <p class="text-xs text-gray-500">KKM: {{ $m->kkm }}</p>
                                        </div>
                                    </label>
                                    <div class="mt-2 pl-6 space-y-1">
                                        <select name="guru_id[{{ $m->id }}]"
                                            class="w-full text-xs border rounded px-2 py-1 @error('guru_id.' . $m->id) border-red-500 @enderror">
                                            <option value="">-- Pilih Guru --</option>
                                            @foreach ($guru as $g)
                                                <option value="{{ $g->id }}" @selected(optional($kelasMapel->get($m->id))->guru_id == $g->id)>
                                                    {{ $g->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('guru_id.' . $m->id)
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror

                                        <input type="number" name="jam_pelajaran[{{ $m->id }}]"
                                            value="{{ $kelasMapel->get($m->id)->jam_pelajaran ?? '' }}"
                                            class="w-full text-xs border rounded px-2 py-1 @error('jam_pelajaran.' . $m->id) border-red-500 @enderror"
                                            placeholder="Jam" min="1">
                                        @error('jam_pelajaran.' . $m->id)
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Simpan Kelompok Belajar
                </button>
            </form>

            {{-- Tabel list mapel yang udah ada --}}
            @if ($kelasMapel->count() > 0)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Mata Pelajaran</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Guru Pengampu</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold">Jam</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($kelasMapel as $km)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span
                                            class="text-xs px-2 py-1 rounded-full 
                                    @if ($km->mapel->jenis_rapot == 'dinniyyah') bg-green-100 text-green-700
                                    @elseif($km->mapel->jenis_rapot == 'tahfidz') bg-purple-100 text-purple-700
                                    @else bg-blue-100 text-blue-700 @endif">
                                            {{ ucfirst($km->mapel->jenis_rapot) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-medium">{{ $km->mapel->nama_mapel }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $km->guru->nama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $km->jam_pelajaran }} JP</td>
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('kelas-mapel.destroy', $km->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Hapus mapel ini dari kelas?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-book-open text-5xl mb-3"></i>
                    <p class="text-sm">Belum ada mata pelajaran di kelas ini</p>
                </div>
            @endif
        @endif
    </div>
@endsection
