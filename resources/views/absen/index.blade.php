@extends('layouts.app')

@section('title', 'ABSEN Siswa')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-semibold text-gray-800">ABSEN Siswa</h3>
        </div>

        @if (!$tahunAktif)
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>Belum ada Tahun Pelajaran yang aktif.
            </div>
        @else
            <div class="mb-4 text-sm text-gray-600">
                <i class="fas fa-calendar mr-1"></i>
                Tahun Pelajaran: <strong>{{ $tahunAktif->tahun }} - {{ $tahunAktif->semester }}</strong>
            </div>

            <div class="border border-gray-200 bg-gray-50 rounded p-3 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-700 text-xs uppercase">
                        <i class="fas fa-print mr-2 text-red-600"></i>Cetak Absen
                    </h4>
                </div>
                <form method="GET" action="{{ route('absen.cetak.print') }}" target="_blank"
                    class="grid grid-cols-4 gap-3">
                    <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahunAktif->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kelas *</label>
                        <select name="kelas_id" class="w-full px-2 py-1.5 border rounded text-xs" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ (string) $kelasSelected === (string) $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal *</label>
                        <input type="date" name="tanggal_mulai" value="{{ now()->startOfMonth()->toDateString() }}"
                            class="w-full px-2 py-1.5 border rounded text-xs" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal *</label>
                        <input type="date" name="tanggal_selesai" value="{{ $tanggal }}"
                            class="w-full px-2 py-1.5 border rounded text-xs" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                            <i class="fas fa-file-pdf mr-1"></i>Cetak PDF
                        </button>
                    </div>
                </form>
            </div>

            <div class="mb-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-3">Input Absen</h4>
                <form method="GET" class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kelas *</label>
                        <select name="kelas_id" class="w-full px-3 py-2 border rounded text-sm" required
                            onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ (string) $kelasSelected === (string) $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal *</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                            class="w-full px-3 py-2 border rounded text-sm" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded text-sm">
                            <i class="fas fa-filter mr-1"></i>Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            @if ($kelasSelected)
                <form method="POST" action="{{ route('absen.store') }}">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelasSelected }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($siswa as $s)
                                    @php $absen = $absenBySiswa->get($s->id); @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $s->nis }}</td>
                                        <td class="px-4 py-3 text-sm font-medium">{{ $s->nama }}</td>
                                        <td class="px-4 py-3">
                                            <select name="absen[{{ $s->id }}][status]"
                                                class="px-3 py-2 border rounded text-sm w-32">
                                                @foreach (['Hadir', 'Sakit', 'Izin', 'Alpa'] as $status)
                                                    <option value="{{ $status }}"
                                                        {{ ($absen->status ?? 'Hadir') === $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="absen[{{ $s->id }}][keterangan]"
                                                value="{{ $absen->keterangan ?? '' }}"
                                                class="w-full px-3 py-2 border rounded text-sm"
                                                placeholder="Opsional">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada siswa aktif di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($siswa->count() > 0)
                        <div class="flex justify-end mt-5">
                            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Simpan Absen
                            </button>
                        </div>
                    @endif
                </form>
            @endif
        @endif
    </div>
@endsection
