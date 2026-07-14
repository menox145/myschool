@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800"></h3>
            <div class="flex gap-2">
                <a href="{{ route('kelas.export', ['search' => request('search')]) }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <button onclick="openModal()"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center">
                    <i class="fas fa-plus mr-2"></i>Tambah Kelas
                </button>
            </div>
        </div>
        <form method="GET" action="{{ route('kelas') }}" class="mb-4">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari nama kelas / wali kelas / tahun..." class="px-3 py-2 border rounded-lg w-80">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tingkat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wali Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Siswa</th>
                        <th class="px-4 py-3 text-left">Tahun Pelajaran</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>

                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kelas as $index => $k)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ $k->nama_kelas }}</td>
                            <td class="px-4 py-3">{{ $k->tingkat }}</td>
                            <td class="px-4 py-3">{{ $k->guru->nama ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">
                                    {{ $k->jumlah_siswa }} Siswa
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $k->tahun_pelajaran }}</td>
                            <td class="px-4 py-3">
                                <button onclick="openEditModal({{ $k->id }}, @js($k))"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus kelas {{ $k->nama_kelas }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data kelas</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Modal Tambah/Edit Kelas -->
        <div id="modalKelas" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Data Kelas</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="formKelas" action="{{ route('kelas.store') }}" method="post">
                        @csrf
                        <input type="hidden" id="methodField" name="_method" value="POST">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas *</label>
                            <input type="text" name="nama_kelas" id="nama_kelas"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-500 @error('nama_kelas') border-red-500 @enderror"
                                placeholder="Contoh: X RPL 1" value="{{ old('nama_kelas') }}" required>
                            @error('nama_kelas')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat *</label>
                            <input type="number" name="tingkat" id="tingkat"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-500 @error('tingkat') border-red-500 @enderror"
                                min="1" max="6" value="{{ old('tingkat') }}" required>
                            @error('tingkat')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas *</label>
                            <select name="guru_id" id="guru_id"
                                class="w-full px-3 py-2 border rounded-lg text-sm @error('guru_id') border-red-500 @enderror"
                                required>
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach ($guru as $g)
                                    <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama }} - {{ $g->nip }}
                                    </option>
                                @endforeach
                            </select>
                            @error('guru_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4 rounded-lg bg-gray-50 border border-gray-200 p-3 text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-2 text-yellow-600"></i>
                            Jumlah siswa akan terisi otomatis saat siswa diregistrasikan atau dipindah ke kelas.
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pelajaran *</label>
                            <select name="tahun_pelajaran" id="tahun_pelajaran"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-500 @error('tahun_pelajaran') border-red-500 @enderror"
                                required>
                                <option value="">-- Pilih Tahun Pelajaran --</option>
                                @foreach ($tahunPelajaran as $tp)
                                    @php $value = $tp->tahun . ' - ' . $tp->semester; @endphp
                                    <option value="{{ $value }}"
                                        {{ old('tahun_pelajaran') == $value ? 'selected' : '' }}>
                                        {{ $value }} {{ $tp->aktif ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_pelajaran')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                                Batal
                            </button>
                            <button type="submit" id="submitBtn"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm hover:bg-yellow-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Data Kelas';
            document.getElementById('formKelas').action = "{{ route('kelas.store') }}";
            document.getElementById('methodField').value = 'POST';
            document.getElementById('formKelas').reset();
            document.getElementById('submitBtn').innerText = 'Simpan';
            document.getElementById('modalKelas').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(id, data) {
            document.getElementById('modalTitle').innerText = 'Edit Data Kelas';
            document.getElementById('formKelas').action = `/kelas/${id}`;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('nama_kelas').value = data.nama_kelas || '';
            document.getElementById('tingkat').value = data.tingkat || '';
            document.getElementById('guru_id').value = data.guru_id || '';
            document.getElementById('tahun_pelajaran').value = data.tahun_pelajaran || '';
            document.getElementById('submitBtn').innerText = 'Update';
            document.getElementById('modalKelas').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('modalKelas').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        @if ($errors->any())
            openModal();
        @endif
    </script>
@endsection
