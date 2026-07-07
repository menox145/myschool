@extends('layouts.app')

@section('title', 'Riwayat Perkembangan Siswa')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">Riwayat Perkembangan Siswa</h3>

        <form method="GET" action="{{ route('siswa.riwayat.index') }}" class="mb-4 flex gap-3 items-end">
            <div>
                <label class="block text-sm text-gray-600">Tahun Pelajaran</label>
                <select name="tahun_pelajaran_id" class="border rounded p-2">
                    <option value="">Semua</option>
                    @foreach ($tahunList as $t)
                        <option value="{{ $t->id }}" @if (request('tahun_pelajaran_id') == $t->id) selected @endif>
                            {{ $t->tahun }} - {{ $t->semester }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600">Kelas</label>
                <select name="kelas_id" class="border rounded p-2">
                    <option value="">Semua</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" @if (request('kelas_id') == $k->id) selected @endif>
                            {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
                <a href="{{ route('siswa.riwayat.index') }}" class="ml-2 text-sm text-gray-600">Reset</a>
            </div>
        </form>

        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">NIS</th>
                    <th class="px-4 py-2 text-left">Riwayat Kelas (Tahun - Kelas - Status)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $s)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $s->nama }}</td>
                        <td class="px-4 py-2">{{ $s->nis ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if ($s->riwayatKelas->isEmpty())
                                -
                            @else
                                <ul class="list-disc ml-6">
                                    @foreach ($s->riwayatKelas->sortBy('tahun_pelajaran_id') as $r)
                                        @php
                                            $key = $s->id . '|' . $r->tahun_pelajaran_id;
                                            $nilaiList = $nilaiIndex->get($key, collect());
                                            $avg = $nilaiList->whereNotNull('hpa')->avg('hpa');
                                            $avg = $avg ? round($avg, 2) : '-';
                                        @endphp
                                        <li>
                                            {{ optional($r->tahunPelajaran)->tahun ?? ($r->tahun_pelajaran_id ?? '-') }} -
                                            {{ optional($r->kelas)->nama_kelas ?? '-' }} - {{ $r->status }}
                                            <div class="text-sm text-gray-600">Nilai terdaftar: {{ $nilaiList->count() }} |
                                                Rata-rata HPA: {{ $avg }}</div>
                                            <div class="mt-1">
                                                <a href="{{ route('rapot.cetak.print', [$s->id, $r->tahun_pelajaran_id, 'akademik']) }}"
                                                    class="text-xs text-blue-600 mr-2">Print Rapot Akademik</a>
                                                <a href="{{ route('rapot.cetak.print', [$s->id, $r->tahun_pelajaran_id, 'dinniyyah']) }}"
                                                    class="text-xs text-blue-600 mr-2">Print Rapot Dinniyyah</a>
                                                <a href="{{ route('rapot.cetak.print', [$s->id, $r->tahun_pelajaran_id, 'tahfidz']) }}"
                                                    class="text-xs text-blue-600">Print Rapot Tahfidz</a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-600">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
