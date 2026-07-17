@extends('layouts.app')

@section('title', 'Riwayat Perkembangan Siswa')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-2xl font-semibold text-slate-900">Riwayat Perkembangan Siswa</h3>
                    <p class="mt-2 text-sm text-slate-500">Lihat perkembangan kelas siswa dan cetak rapor langsung dari
                        daftar riwayat.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('siswa.riwayat.index') }}"
                        class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                        Reset Filter
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="GET" action="{{ route('siswa.riwayat.index') }}"
                    class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-2">Tahun Pelajaran</label>
                        <select name="tahun_pelajaran_id"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                            <option value="">Semua</option>
                            @foreach ($tahunList as $t)
                                <option value="{{ $t->id }}" @if (request('tahun_pelajaran_id') == $t->id) selected @endif>
                                    {{ $t->tahun }} - {{ $t->semester }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-2">Kelas</label>
                        <select name="kelas_id"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                            <option value="">Semua</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}" @if (request('kelas_id') == $k->id) selected @endif>
                                    {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                <div class="space-y-4">
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Siswa</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $siswa->count() }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Filter aktif</p>
                        <p class="mt-2 text-sm text-slate-800">
                            Tahun: <span
                                class="font-semibold">{{ request('tahun_pelajaran_id') ? $tahunList->firstWhere('id', request('tahun_pelajaran_id'))->tahun . ' - ' . $tahunList->firstWhere('id', request('tahun_pelajaran_id'))->semester : 'Semua' }}</span>
                        </p>
                        <p class="mt-1 text-sm text-slate-800">
                            Kelas: <span
                                class="font-semibold">{{ request('kelas_id') ? $kelasList->firstWhere('id', request('kelas_id'))->nama_kelas : 'Semua' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($siswa as $s)
                <details
                    class="group overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 shadow-sm transition hover:border-blue-300">
                    <summary
                        class="flex flex-col gap-3 px-6 py-5 md:flex-row md:items-center md:justify-between cursor-pointer">
                        <div>
                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                Siswa ID: {{ $s->id }}
                            </div>
                            <h4 class="mt-2 text-lg font-semibold text-slate-900">{{ $s->nama }}</h4>
                            <p class="text-sm text-slate-500">NIS: {{ $s->nis ?? '-' }}</p>
                        </div>
                        <div
                            class="inline-flex items-center gap-3 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                            <span>{{ $s->riwayatKelas->count() }} riwayat</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </summary>
                    <div class="border-t border-slate-200 bg-white p-6">
                        @if ($s->riwayatKelas->isEmpty())
                            <div
                                class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                Belum ada riwayat kelas untuk siswa ini.
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($s->riwayatKelas->sortBy('tahun_pelajaran_id') as $r)
                                    @php
                                        $key = $s->id . '|' . $r->tahun_pelajaran_id;
                                        $nilaiList = $nilaiIndex->get($key, collect());
                                        $avg = $nilaiList->whereNotNull('hpa')->avg('hpa');
                                        $avg = $avg ? round($avg, 2) : '-';
                                        $tahunLabel =
                                            optional($r->tahunPelajaran)->tahun ?? ($r->tahun_pelajaran_id ?? '-');
                                        $kelasLabel = optional($r->kelas)->nama_kelas ?? '-';
                                    @endphp
                                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                            <div>
                                                <p class="text-sm uppercase tracking-[0.18em] text-slate-500">
                                                    {{ $tahunLabel }} · {{ $kelasLabel }}</p>
                                                <h5 class="mt-2 text-base font-semibold text-slate-900">Status:
                                                    {{ $r->status }}</h5>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span
                                                    class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">{{ $nilaiList->count() }}
                                                    nilai</span>
                                                <span
                                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">HPA
                                                    rata-rata: {{ $avg }}</span>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex flex-wrap gap-3">
                                            <a href="{{ route('rapot.cetak.print', [$s->id, $r->tahun_pelajaran_id, 'akademik']) }}"
                                                class="inline-flex items-center rounded-full bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                <i class="fas fa-print mr-2"></i>Rapot Akademik
                                            </a>
                                            <a href="{{ route('rapot.cetak.print', [$s->id, $r->tahun_pelajaran_id, 'dinniyyah']) }}"
                                                class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                                <i class="fas fa-print mr-2"></i>Rapot Dinniyyah
                                            </a>
                                            <a href="{{ route('rapot.cetak.print', [$s->id, $r->tahun_pelajaran_id, 'tahfidz']) }}"
                                                class="inline-flex items-center rounded-full bg-purple-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-purple-700">
                                                <i class="fas fa-print mr-2"></i>Rapot Tahfidz
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </details>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500">
                    Tidak ada data.
                </div>
            @endforelse
        </div>
    </div>
@endsection
