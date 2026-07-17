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
            <div class="grid gap-4 lg:grid-cols-[1.3fr_0.7fr] mb-6">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="rounded-2xl bg-blue-100 p-3 text-blue-600 shadow-inner">
                            <i class="fas fa-clipboard-list text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Tahun Pelajaran Aktif</p>
                            <h2 class="text-xl font-semibold text-slate-900">{{ $tahunAktif->tahun }} -
                                {{ $tahunAktif->semester }}</h2>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600">Pilih kelas dan tanggal untuk melihat daftar absen siswa. Semua
                        perubahan akan tersimpan ke dalam catatan harian.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Tanggal tampilan</p>
                            <p class="text-lg font-semibold text-slate-900">
                                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div
                            class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">
                            <i class="fas fa-calendar-day"></i>
                            Senin - Jumat
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm text-slate-600">
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <p class="font-semibold text-slate-800">Total Kelas</p>
                            <p>{{ $kelas->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <p class="font-semibold text-slate-800">Kelas dipilih</p>
                            <p>{{ $kelasSelected ? $kelas->firstWhere('id', $kelasSelected)->nama_kelas : 'Belum dipilih' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2 mb-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800 uppercase tracking-wide">Input Absen</h4>
                            <p class="text-xs text-slate-500">Pilih kelas dan tanggal untuk mulai mengisi.</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">Cepat & mudah</span>
                    </div>
                    <form method="GET" class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Kelas *</label>
                            <select name="kelas_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-blue-400 focus:outline-none"
                                required onchange="this.form.submit()">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}"
                                        {{ (string) $kelasSelected === (string) $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal *</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-blue-400 focus:outline-none"
                                required>
                        </div>
                        <div class="sm:col-span-2 flex items-end justify-end">
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                <i class="fas fa-filter mr-2"></i>Tampilkan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-slate-800 uppercase tracking-wide">Cetak Absen</h4>
                        <span class="text-xs text-slate-500">Export</span>
                    </div>
                    <form method="GET" action="{{ route('absen.cetak.print') }}" target="_blank" class="grid gap-4">
                        <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahunAktif->id }}">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Kelas *</label>
                            <select name="kelas_id"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 transition focus:border-blue-400 focus:outline-none"
                                required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}"
                                        {{ (string) $kelasSelected === (string) $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Dari</label>
                                <input type="date" name="tanggal_mulai"
                                    value="{{ now()->startOfMonth()->toDateString() }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 transition focus:border-blue-400 focus:outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Sampai</label>
                                <input type="date" name="tanggal_selesai" value="{{ $tanggal }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 transition focus:border-blue-400 focus:outline-none"
                                    required>
                            </div>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                            <i class="fas fa-file-pdf mr-2"></i>Cetak PDF
                        </button>
                    </form>
                </div>
            </div>

            @if ($kelasSelected)
                @php
                    $statusSummary = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
                    foreach ($siswa as $s) {
                        $ab = $absenBySiswa->get($s->id);
                        $statusSummary[$ab->status ?? 'Hadir']++;
                    }
                @endphp

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Total Siswa</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $siswa->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-blue-50 p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.16em] text-blue-600">Hadir</p>
                        <p class="mt-2 text-2xl font-semibold text-blue-700">{{ $statusSummary['Hadir'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-amber-50 p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.16em] text-amber-700">Sakit</p>
                        <p class="mt-2 text-2xl font-semibold text-amber-900">{{ $statusSummary['Sakit'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-rose-50 p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.16em] text-rose-600">Izin / Alpa</p>
                        <p class="mt-2 text-2xl font-semibold text-rose-700">
                            {{ $statusSummary['Izin'] + $statusSummary['Alpa'] }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('absen.store') }}">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelasSelected }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                    <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-100 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em]">No
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em]">NIS
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em]">Nama
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em]">Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em]">
                                        Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($siswa as $s)
                                    @php $absen = $absenBySiswa->get($s->id); @endphp
                                    <tr class="transition hover:bg-slate-50">
                                        <td class="px-4 py-4 text-sm text-slate-700">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-700">{{ $s->nis }}</td>
                                        <td class="px-4 py-4 text-sm font-medium text-slate-900">{{ $s->nama }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                <select name="absen[{{ $s->id }}][status]"
                                                    class="w-full max-w-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-blue-400 focus:outline-none">
                                                    @foreach (['Hadir', 'Sakit', 'Izin', 'Alpa'] as $status)
                                                        <option value="{{ $status }}"
                                                            {{ ($absen->status ?? 'Hadir') === $status ? 'selected' : '' }}>
                                                            {{ $status }}</option>
                                                    @endforeach
                                                </select>
                                                <span
                                                    class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">{{ $absen->status ?? 'Hadir' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="text" name="absen[{{ $s->id }}][keterangan]"
                                                value="{{ $absen->keterangan ?? '' }}"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 transition focus:border-blue-400 focus:outline-none"
                                                placeholder="Opsional">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                            Belum ada siswa aktif di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($siswa->count() > 0)
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-slate-500">Pastikan status dan keterangan sudah benar sebelum menyimpan.
                            </p>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Simpan Absen
                            </button>
                        </div>
                    @endif
                </form>
            @endif
        @endif
    </div>
@endsection
