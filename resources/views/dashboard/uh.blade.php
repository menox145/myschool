@extends('layouts.app')
@section('title', 'Input Nilai UH')
@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold mb-1">Input Nilai Ulangan Harian</h1>
            <p class="text-sm text-gray-500">
                Tahun Pelajaran: <span class="font-medium text-blue-600">{{ $tapelAktif->tahun }} -
                    {{ $tapelAktif->semester }}</span>
            </p>
        </div>

        <form method="GET" class="mb-6">
            <div class="w-64">
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
        </form>

        @if ($kelasSelected && $siswa->count() > 0 && $allMapelKelas->count() > 0)
            <form action="{{ route('nilai.uh.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_pelajaran_id" value="{{ $tapelAktif->id }}">

                <div class="space-y-6">
                    @foreach ($allMapelKelas as $km)
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <div
                                class="bg-blue-600 text-white px-4 py-2 font-bold text-center flex justify-between items-center">
                                <span>{{ strtoupper($km->mapel->nama_mapel) }}</span>
                                <button type="button"
                                    onclick="setJumlahUh({{ $km->id }}, '{{ $km->mapel->nama_mapel }}', {{ $km->jumlah_uh ?? 0 }})"
                                    class="px-2 py-1 bg-white text-blue-600 rounded text-xs hover:bg-blue-50">
                                    <i class="fas fa-cog"></i> Set UH: {{ $km->jumlah_uh ?? 0 }}
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-2 w-8">No</th>
                                            <th class="px-2 py-2 w-48 sticky left-0 bg-gray-50">Nama Siswa</th>
                                            @for ($i = 1; $i <= $km->jumlah_uh; $i++)
                                                <th class="px-1 py-2 w-16">UH{{ $i }}</th>
                                            @endfor
                                            <th class="px-2 py-2 w-20 bg-gray-100">Rata UH</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($siswa as $s)
                                            @php
                                                $n = $km->nilai->where('siswa_id', $s->id)->first();
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-2 py-1 text-center">{{ $loop->iteration }}</td>
                                                <td class="px-2 py-1 sticky left-0 bg-white font-medium">
                                                    {{ $s->nama }}</td>
                                                @for ($i = 1; $i <= $km->jumlah_uh; $i++)
                                                    <td class="px-1 py-1">
                                                        <input type="number"
                                                            name="nilai[{{ $s->id }}][{{ $km->id }}][uh{{ $i }}]"
                                                            value="{{ $n->{'uh' . $i} ?? '' }}"
                                                            class="uh-input w-full px-1 py-1 border rounded text-center text-xs"
                                                            data-siswa="{{ $s->id }}"
                                                            data-mapel="{{ $km->id }}" min="0" max="100"
                                                            step="0.01">
                                                    </td>
                                                @endfor
                                                <td class="px-2 py-1 text-center font-bold bg-gray-50">
                                                    <span
                                                        class="rata-uh-{{ $s->id }}-{{ $km->id }}">{{ $n->rata_uh ?? '-' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Simpan Semua Nilai UH
                    </button>
                </div>
            </form>
        @elseif($kelasSelected)
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-exclamation-triangle text-5xl mb-3"></i>
                <p class="text-sm">Belum ada mata pelajaran di kelas ini</p>
            </div>
        @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-hand-pointer text-5xl mb-3"></i>
                <p class="text-sm">Pilih Kelas dulu untuk mulai input UH</p>
            </div>
        @endif


        <!-- Modal Set Jumlah UH -->
        <div id="modalUh" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeUhModal()"></div>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6 relative">
                    <h3 class="text-lg font-bold mb-4">Set Jumlah UH: <span id="modal_mapel_nama"></span></h3>
                    <form id="formUh">
                        @csrf
                        <input type="hidden" id="uh_kelas_mapel_id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Jumlah UH *</label>
                            <input type="number" id="jumlah_uh_input" class="w-full px-3 py-2 border rounded text-sm"
                                min="1" max="6" required>
                            <p class="text-xs text-gray-500 mt-1">Max 6. Contoh: 3 = UH1, UH2, UH3</p>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="closeUhModal()"
                                class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function setJumlahUh(id, nama, current) {
                document.getElementById('uh_kelas_mapel_id').value = id;
                document.getElementById('modal_mapel_nama').textContent = nama;
                document.getElementById('jumlah_uh_input').value = current || 3;
                document.getElementById('modalUh').classList.remove('hidden');
            }

            function closeUhModal() {
                document.getElementById('modalUh').classList.add('hidden');
            }

            document.getElementById('formUh').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('uh_kelas_mapel_id').value;
                const jumlah = document.getElementById('jumlah_uh_input').value;

                fetch(`/kelas-mapel/${id}/set-uh`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        jumlah_uh: jumlah
                    })
                }).then(res => res.json()).then(data => {
                    if (data.success) location.reload();
                    else alert('Gagal update');
                });
            });
        </script>
    </div>

    <script>
        document.querySelectorAll('.uh-input').forEach(input => {
            input.addEventListener('input', function() {
                if (parseFloat(this.value) > 100) this.value = 100;
                if (parseFloat(this.value) < 0) this.value = 0;

                const siswaId = this.dataset.siswa;
                const mapelId = this.dataset.mapel;
                const inputs = document.querySelectorAll(
                    `.uh-input[data-siswa="${siswaId}"][data-mapel="${mapelId}"]`);
                let total = 0,
                    count = 0;

                inputs.forEach(inp => {
                    if (inp.value !== '' && inp.value !== null) {
                        total += parseFloat(inp.value);
                        count++;
                    }
                });

                const rataEl = document.querySelector(`.rata-uh-${siswaId}-${mapelId}`);
                rataEl.textContent = count > 0 ? (total / count).toFixed(2) : '-';
            });

            input.addEventListener('focus', function() {
                this.select();
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type === 'number') {
                e.preventDefault();
                const inputs = Array.from(document.querySelectorAll('input[type="number"]'));
                const idx = inputs.indexOf(e.target);
                if (inputs[idx + 1]) {
                    inputs[idx + 1].focus();
                    inputs[idx + 1].select();
                }
            }
        });
    </script>
@endsection
