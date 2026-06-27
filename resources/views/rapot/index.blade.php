@extends('layouts.app')
@section('title', 'Cetak Rapot')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Cetak Rapot Siswa</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Pelajaran *</label>
                        <select name="tahun_pelajaran_id" class="form-select w-full" onchange="this.form.submit()" required>
                            <option value="">-- Pilih Tahun Pelajaran --</option>
                            @foreach ($allTapel as $tp)
                                <option value="{{ $tp->id }}" @selected($tapelSelected == $tp->id)>
                                    {{ $tp->tahun }} - {{ ucfirst($tp->semester) }} {{ $tp->aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($tapelSelected && $kelas->count() > 0)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Kelas *</label>
                            <select name="kelas_id" class="form-select w-full" onchange="this.form.submit()" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </form>

            @if ($siswas->isNotEmpty())
                @php $tp = $allTapel->find($tapelSelected) @endphp

                {{-- HEADER CUMA 1x, NGGAK DOUBLE --}}
                <div
                    class="mb-4 flex justify-between items-center px-4 py-3 rounded-lg text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                    <div>
                        <i class="fas fa-calendar mr-2"></i>TA: {{ $tp->tahun }} - {{ ucfirst($tp->semester) }}
                        <span class="ml-3"><i class="fas fa-users mr-1"></i>{{ $siswas->count() }} siswa</span>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('rapot.cetak.kelas', [$tapelSelected, request('kelas_id'), 'dinniyyah']) }}"
                            target="_blank" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                            <i class="fas fa-print"></i> Cetak Semua Dinniyyah
                        </a>
                        <a href="{{ route('rapot.cetak.kelas', [$tapelSelected, request('kelas_id'), 'akademik']) }}"
                            target="_blank" class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                            <i class="fas fa-print"></i> Cetak Semua Akademik
                        </a>
                        <a href="{{ route('rapot.cetak.kelas', [$tapelSelected, request('kelas_id'), 'tahfidz']) }}"
                            target="_blank"
                            class="px-3 py-1.5 bg-yellow-600 text-white rounded text-xs hover:bg-yellow-700">
                            <i class="fas fa-print"></i> Cetak Semua Tahfidz
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NISN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cetak Rapot
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($siswas as $s)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $s->nisn ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $s->nama }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex gap-1 justify-center">
                                            <a href="{{ route('rapot.cetak.print', [$s->id, $tapelSelected, 'dinniyyah']) }}"
                                                target="_blank"
                                                class="px-3 py-1.5 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                                <i class="fas fa-print"></i> Dinniyyah
                                            </a>
                                            <a href="{{ route('rapot.cetak.print', [$s->id, $tapelSelected, 'akademik']) }}"
                                                target="_blank"
                                                class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                                <i class="fas fa-print"></i> Akademik
                                            </a>
                                            <a href="{{ route('rapot.cetak.print', [$s->id, $tapelSelected, 'tahfidz']) }}"
                                                target="_blank"
                                                class="px-3 py-1.5 bg-yellow-600 text-white rounded text-xs hover:bg-yellow-700">
                                                <i class="fas fa-print"></i> Tahfidz
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                @if ($tapelSelected && request()->filled('kelas_id'))
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-yellow-700">
                            Tidak ada siswa dengan nilai di kelas ini pada tahun pelajaran tersebut.
                        </p>
                    </div>
                @elseif ($tapelSelected && $kelas->count() == 0)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-yellow-700">
                            Tidak ada kelas yang aktif di tahun pelajaran ini.
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
