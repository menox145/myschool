@extends('layouts.app')

@section('title', 'Input Nilai Akhir')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Input Nilai PTS & PAS</h3>
            <p class="text-sm text-gray-500">
                Tahun Pelajaran: <span class="font-medium text-blue-600">{{ $tapelAktif->tahun }} -
                    {{ $tapelAktif->semester }}</span>
            </p>
        </div>

        <!-- FILTER KELAS + JENIS RAPOT -->
        <form method="GET" action="{{ route('nilai.index') }}"
            class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Kelas *</label>
                    <select name="kelas_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" @selected($kelasSelected == $k->id)>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($kelasSelected)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Jenis Rapot *</label>
                        <select name="jenis_rapot"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach ($jenisRapot as $jenis)
                                <option value="{{ $jenis }}" @selected($jenisSelected == $jenis)>
                                    {{ ucfirst($jenis) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 w-full transition">
                            <i class="fas fa-search mr-1"></i>Tampilkan
                        </button>
                    </div>
                @endif
            </div>
        </form>

        @if ($kelasMapelList->count() > 0 && $siswa->count() > 0)
            <!-- INFO JENIS RAPOT -->
            <div
                class="mb-4 px-4 py-2 rounded-lg text-sm font-semibold
                @if ($jenisSelected == 'akademik') bg-red-100 text-red-800 border border-red-200
                @elseif($jenisSelected == 'dinniyyah') bg-yellow-100 text-yellow-800 border border-yellow-200
                @else bg-blue-100 text-blue-800 border border-blue-200 @endif">
                <i class="fas fa-book mr-2"></i>Rapor {{ ucfirst($jenisSelected) }}
            </div>

            <form action="{{ route('nilai.store') }}" method="POST" id="formNilai">
                @csrf
                <input type="hidden" name="tahun_pelajaran_id" value="{{ $tapelAktif->id }}">

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th rowspan="2"
                                    class="px-2 py-2 text-xs font-semibold text-gray-600 uppercase w-12 align-middle border-r">
                                    No</th>
                                <th rowspan="2"
                                    class="px-2 py-2 text-xs font-semibold text-gray-600 uppercase w-20 align-middle border-r">
                                    NIS</th>
                                <th rowspan="2"
                                    class="px-2 py-2 text-xs font-semibold text-gray-600 uppercase w-48 align-middle border-r sticky left-0 bg-gray-100 z-10">
                                    Nama Siswa</th>
                                @foreach ($kelasMapelList as $km)
                                    <th colspan="3"
                                        class="px-2 py-2 text-xs font-semibold text-gray-700 uppercase text-center border-l
                                        @if ($jenisSelected == 'akademik') bg-red-50
                                        @elseif($jenisSelected == 'dinniyyah') bg-yellow-50
                                        @else bg-blue-50 @endif">
                                        {{ $km->mapel->nama_mapel }}
                                    </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($kelasMapelList as $km)
                                    <th class="px-1 py-1 text-xs font-semibold text-gray-600 w-16 border-l bg-blue-50">RPH
                                    </th>
                                    <th class="px-1 py-1 text-xs font-semibold text-gray-600 w-16">PTS</th>
                                    <th class="px-1 py-1 text-xs font-semibold text-gray-600 w-16">PAS</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($siswa as $sIndex => $s)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-2 py-1 text-center text-gray-500 border-r">{{ $loop->iteration }}</td>
                                    <td class="px-2 py-1 text-xs text-gray-500 font-mono border-r">{{ $s->nis }}</td>
                                    <td class="px-2 py-1 font-medium text-gray-800 text-sm border-r sticky left-0 bg-white">
                                        {{ $s->nama }}</td>

                                    @foreach ($kelasMapelList as $km)
                                        @php $n = $nilaiSiswa[$s->id][$km->id]?? null @endphp
                                        {{-- RPH DARI UH - READONLY --}}
                                        <td class="px-1 py-1 border-l text-center text-sm font-bold bg-blue-50">
                                            {{ $n->rph ?? ($n->rata_uh ?? '-') }}
                                            <input type="hidden"
                                                name="nilai[{{ $s->id }}][{{ $km->id }}][rph]"
                                                value="{{ $n->rph ?? ($n->rata_uh ?? 0) }}">
                                        </td>
                                        {{-- PTS --}}
                                        <td class="px-1 py-1">
                                            <input type="number"
                                                name="nilai[{{ $s->id }}][{{ $km->id }}][pts]"
                                                value="{{ old("nilai.{$s->id}.{$km->id}.pts", $n?->pts) }}"
                                                class="w-full px-1 py-1 border border-gray-300 rounded text-center text-xs focus:ring-1 focus:ring-blue-500"
                                                min="0" max="100" step="0.01" placeholder="0">
                                        </td>
                                        {{-- PAS --}}
                                        <td class="px-1 py-1">
                                            <input type="number"
                                                name="nilai[{{ $s->id }}][{{ $km->id }}][pas]"
                                                value="{{ old("nilai.{$s->id}.{$km->id}.pas", $n?->pas) }}"
                                                class="w-full px-1 py-1 border border-gray-300 rounded text-center text-xs focus:ring-1 focus:ring-blue-500"
                                                min="0" max="100" step="0.01" placeholder="0">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-users mr-1"></i>Total: {{ $siswa->count() }} siswa ×
                        {{ $kelasMapelList->count() }} mapel
                    </p>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 font-medium shadow-sm">
                        <i class="fas fa-save mr-2"></i>Simpan Nilai Akhir
                    </button>
                </div>
            </form>
        @elseif($kelasSelected && !$jenisSelected)
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-book text-5xl mb-3"></i>
                <p class="text-sm">Pilih Jenis Rapot dulu</p>
            </div>
        @elseif($kelasSelected && $kelasMapelList->count() == 0)
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-exclamation-triangle text-5xl mb-3"></i>
                <p class="text-sm">Belum ada mata pelajaran {{ ucfirst($jenisSelected) }} di kelas ini</p>
                <a href="{{ route('kelas-mapel.index', ['kelas_id' => $kelasSelected]) }}"
                    class="text-blue-600 text-xs hover:underline">Setting Kelompok Belajar</a>
            </div>
        @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-hand-pointer text-5xl mb-3"></i>
                <p class="text-sm">Pilih Kelas dulu untuk mulai input nilai</p>
            </div>
        @endif
    </div>

    <style>
        input[type="number"]:focus {
            background-color: #fffbeb;
            border-color: #f59e0b;
            outline: none;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .sticky {
            position: sticky;
        }

        .left-0 {
            left: 0;
        }

        .z-10 {
            z-index: 10;
        }
    </style>

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type === 'number') {
                e.preventDefault();
                const allInputs = Array.from(document.querySelectorAll('tbody input[type="number"]'));
                const currentIndex = allInputs.indexOf(e.target);
                const colsPerRow = {{ $kelasMapelList->count() * 2 }}; // PTS + PAS doang
                const nextIndex = currentIndex + colsPerRow;
                if (allInputs[nextIndex]) {
                    allInputs[nextIndex].focus();
                    allInputs[nextIndex].select();
                }
            }
        });

        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.select();
            });
            input.addEventListener('input', function() {
                if (this.value > 100) this.value = 100;
                if (this.value < 0) this.value = 0;
            });
        });
    </script>
@endsection
