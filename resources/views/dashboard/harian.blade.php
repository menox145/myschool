@extends('layouts.app')
@section('title', 'Input Nilai Harian')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if (!$tahunAktif)
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>Belum ada Tahun Pelajaran yang aktif. Set dulu di Data Tahun
                Pelajaran.
            </div>
        @else
            <div class="mb-4 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-calendar mr-1"></i> Tahun Pelajaran: <strong>{{ $tahunAktif->tahun }} -
                        {{ $tahunAktif->semester }}</strong>
                </div>
                <a href="{{ route('nilai.harian.export', ['kelas_id' => $kelasSelected, 'kelas_mapel_id' => $mapelSelected]) }}"
                    class="px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                    <i class="fas fa-file-excel mr-1"></i> Export CSV
                </a>
            </div>




            <form method="GET" class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Kelas *</label>
                    <select name="kelas_id" class="w-full px-3 py-2 border rounded text-sm" required
                        onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ $kelasSelected == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if ($allMapelKelas->count() > 0)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Mapel (Opsional)</label>
                        <select name="kelas_mapel_id" class="w-full px-3 py-2 border rounded text-sm"
                            onchange="this.form.submit()">
                            <option value="">-- Tampilkan Semua Mapel --</option>
                            @foreach ($allMapelKelas as $km)
                                <option value="{{ $km->id }}" {{ $mapelSelected == $km->id ? 'selected' : '' }}>
                                    {{ $km->mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </form>

            {{-- TAMPILIN SEMUA MAPEL KAYAK EXCEL KALO BELUM PILIH MAPEL SPESIFIK --}}
            @if ($kelasSelected && !$mapelSelected && $allMapelKelas->count() > 0)
                <div class="space-y-6">
                    @foreach ($allMapelKelas as $km)
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <div class="bg-blue-600 text-white px-4 py-2 font-bold text-center">
                                {{ strtoupper($km->mapel->nama_mapel) }}
                            </div>
                            <div class="p-2 bg-gray-50 border-b flex justify-between items-center">
                                <span class="text-xs text-gray-600">Kelas:
                                    {{ $kelas->find($kelasSelected)->nama_kelas }}</span>
                                <button type="button" onclick="openBabModal({{ $km->id }})"
                                    class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600">
                                    <i class="fas fa-plus"></i> Tambah Bab
                                </button>
                            </div>
                            @if ($km->babData->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th rowspan="2" class="border px-2 py-1 w-8">No</th>
                                                <th rowspan="2" class="border px-2 py-1 w-48 sticky left-0 bg-gray-100">
                                                    Nama</th>
                                                @foreach ($km->babData as $b)
                                                    <th colspan="{{ $b->subBab->count() ?: 1 }}" class="border px-2 py-1">
                                                        {{ $b->nama_bab }}
                                                        <button type="button"
                                                            onclick="openSubBabModal({{ $b->id }})"
                                                            class="ml-1 text-blue-600 hover:text-blue-800"
                                                            title="Tambah Sub Bab">+</button>
                                                        <button type="button"
                                                            onclick="hapusBab({{ $b->id }}, '{{ addslashes($b->nama_bab) }}')"
                                                            class="ml-1 text-red-600 hover:text-red-800"
                                                            title="Hapus Bab">×</button>
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr class="bg-gray-50">
                                                @foreach ($km->babData as $b)
                                                    @forelse ($b->subBab as $sb)
                                                        <th class="border px-1 py-1 text-xs font-normal">
                                                            {{ $sb->nama_sub_bab }}
                                                            <button type="button"
                                                                onclick="hapusSubBab({{ $sb->id }}, '{{ addslashes($sb->nama_sub_bab) }}')"
                                                                class="ml-1 text-red-500 hover:text-red-700"
                                                                title="Hapus Sub Bab">×</button>
                                                        </th>
                                                    @empty
                                                        <th class="border px-1 py-1 text-xs text-gray-400">Belum ada sub bab
                                                        </th>
                                                    @endforelse
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($siswa as $s)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="border px-2 py-1 text-center">{{ $loop->iteration }}</td>
                                                    <td class="border px-2 py-1 sticky left-0 bg-white">{{ $s->nama }}
                                                    </td>
                                                    @foreach ($km->babData as $b)
                                                        @foreach ($b->subBab as $sb)
                                                            @php
                                                                $nilai =
                                                                    $sb->nilaiHarian->where('siswa_id', $s->id)->first()
                                                                        ->nilai ?? '';
                                                            @endphp
                                                            <td class="border p-0">
                                                                <input type="number"
                                                                    class="nilai-input w-full px-1 py-1 text-center border-0 text-xs"
                                                                    data-siswa="{{ $s->id }}"
                                                                    data-subbab="{{ $sb->id }}"
                                                                    value="{{ $nilai }}" min="0"
                                                                    max="100">
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-400 text-sm">
                                    Belum ada Bab. Klik "Tambah Bab" untuk mulai.
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- KALO UDAH PILIH MAPEL SPESIFIK --}}
            @if ($mapelSelected && $siswa->count() > 0)
                <div class="mb-3 flex justify-between items-center">
                    <h4 class="font-semibold text-sm">Mapel: {{ $allMapelKelas->find($mapelSelected)->mapel->nama_mapel }}
                    </h4>
                    <button type="button" onclick="openBabModal({{ $mapelSelected }})"
                        class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-plus"></i> Tambah Bab
                    </button>
                </div>

                @if ($bab->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th rowspan="2" class="border px-2 py-1 w-8">No</th>
                                    <th rowspan="2" class="border px-2 py-1 w-48 sticky left-0 bg-gray-100">Nama</th>
                                    @foreach ($bab as $b)
                                        <th colspan="{{ $b->subBab->count() ?: 1 }}" class="border px-2 py-1">
                                            {{ $b->nama_bab }}
                                            <button type="button" onclick="openSubBabModal({{ $b->id }})"
                                                class="ml-1 text-blue-600 hover:text-blue-800"
                                                title="Tambah Sub Bab">+</button>
                                            <button type="button"
                                                onclick="hapusBab({{ $b->id }}, '{{ addslashes($b->nama_bab) }}')"
                                                class="ml-1 text-red-600 hover:text-red-800" title="Hapus Bab">×</button>
                                        </th>
                                    @endforeach
                                </tr>
                                <tr class="bg-gray-50">
                                    @foreach ($bab as $b)
                                        @forelse ($b->subBab as $sb)
                                            <th class="border px-1 py-1 text-xs font-normal">
                                                {{ $sb->nama_sub_bab }}
                                                <button type="button"
                                                    onclick="hapusSubBab({{ $sb->id }}, '{{ addslashes($sb->nama_sub_bab) }}')"
                                                    class="ml-1 text-red-500 hover:text-red-700"
                                                    title="Hapus Sub Bab">×</button>
                                            </th>
                                        @empty
                                            <th class="border px-1 py-1 text-xs text-gray-400">Belum ada sub bab</th>
                                        @endforelse
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswa as $s)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-2 py-1 text-center">{{ $loop->iteration }}</td>
                                        <td class="border px-2 py-1 sticky left-0 bg-white">{{ $s->nama }}</td>
                                        @foreach ($bab as $b)
                                            @foreach ($b->subBab as $sb)
                                                @php
                                                    $nilai =
                                                        $sb->nilaiHarian->where('siswa_id', $s->id)->first()->nilai ??
                                                        '';
                                                @endphp
                                                <td class="border p-0">
                                                    <input type="number"
                                                        class="nilai-input w-full px-1 py-1 text-center border-0 text-xs"
                                                        data-siswa="{{ $s->id }}"
                                                        data-subbab="{{ $sb->id }}" value="{{ $nilai }}"
                                                        min="0" max="100">
                                                </td>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400 text-sm">
                        Belum ada Bab. Klik "Tambah Bab" untuk mulai.
                    </div>
                @endif
            @endif
        @endif
    </div>

    {{-- MODAL TAMBAH BAB --}}
    <div id="modalBab" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeBabModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                <h3 class="text-lg font-bold mb-4">Tambah Bab Baru</h3>
                <form id="formBab">
                    @csrf
                    <input type="hidden" name="kelas_mapel_id" id="bab_kelas_mapel_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nama Bab *</label>
                        <input type="text" name="nama_bab" class="w-full px-3 py-2 border rounded text-sm"
                            placeholder="Contoh: Bab 1, Bab 2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Urutan</label>
                        <input type="number" name="urutan" value="1"
                            class="w-full px-3 py-2 border rounded text-sm" min="1">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeBabModal()"
                            class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH SUB BAB --}}
    <div id="modalSubBab" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeSubBabModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                <h3 class="text-lg font-bold mb-4">Tambah Sub Bab</h3>
                <form id="formSubBab">
                    @csrf
                    <input type="hidden" name="bab_mapel_id" id="subbab_bab_mapel_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nama Sub Bab *</label>
                        <input type="text" name="nama_sub_bab" class="w-full px-3 py-2 border rounded text-sm"
                            placeholder="Contoh: tulis abja, tarjim 1, hal 3" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Urutan</label>
                        <input type="number" name="urutan" value="1"
                            class="w-full px-3 py-2 border rounded text-sm" min="1">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeSubBabModal()"
                            class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openBabModal(kelasMapelId) {
            document.getElementById('bab_kelas_mapel_id').value = kelasMapelId;
            document.getElementById('modalBab').classList.remove('hidden');
        }

        function closeBabModal() {
            document.getElementById('modalBab').classList.add('hidden');
        }

        function openSubBabModal(babId) {
            document.getElementById('subbab_bab_mapel_id').value = babId;
            document.getElementById('modalSubBab').classList.remove('hidden');
        }

        function closeSubBabModal() {
            document.getElementById('modalSubBab').classList.add('hidden');
        }

        document.getElementById('formBab').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('{{ route('nilai.bab.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(this)))
            }).then(res => res.json()).then(data => {
                if (data.success) location.reload();
            });
        });

        document.getElementById('formSubBab').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('{{ route('nilai.subbab.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(this)))
            }).then(res => res.json()).then(data => {
                if (data.success) location.reload();
            });
        });

        function hapusBab(id, nama) {
            if (confirm(`Yakin hapus Bab "${nama}"? Semua sub bab + nilai di dalamnya akan ikut terhapus.`)) {
                fetch(`{{ url('nilai/bab') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) location.reload();
                    else alert('Gagal hapus bab');
                });
            }
        }

        function hapusSubBab(id, nama) {
            if (confirm(`Yakin hapus Sub Bab "${nama}"? Semua nilai di dalamnya akan ikut terhapus.`)) {
                fetch(`{{ url('nilai/subbab') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) location.reload();
                    else alert('Gagal hapus sub bab');
                });
            }
        }

        let saveTimeout;
        document.querySelectorAll('.nilai-input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value > 100) this.value = 100;
                if (this.value < 0) this.value = 0;

                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    fetch('{{ route('nilai.harian.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            siswa_id: this.dataset.siswa,
                            sub_bab_mapel_id: this.dataset.subbab,
                            nilai: this.value
                        })
                    }).then(res => res.json()).then(data => {
                        if (data.nilai !== undefined) this.value = data.nilai;
                    });
                }, 800);
            });
        });
    </script>
@endsection
