@extends('layouts.app')

@section('title', 'Master Jadwal Piket')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Master Jadwal Guru Piket</h3>
                <p class="text-sm text-gray-500">Atur guru piket per hari dengan data yang bisa diedit dan dihapus.</p>
            </div>
            <button onclick="openModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Jadwal
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Hari</th>
                        <th class="px-4 py-3 text-left">Guru</th>
                        <th class="px-4 py-3 text-left">Urutan</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($piket as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">{{ $item->hari }}</td>
                            <td class="px-4 py-3">{{ $item->guru->nama ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $item->urutan }}</td>
                            <td class="px-4 py-3">
                                <button onclick="openEditModal({{ $item->id }}, @js($item))"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('guru-piket.destroy', $item->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data piket.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="modalPiket" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Jadwal Piket</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="formPiket" method="POST">
                        @csrf
                        <input type="hidden" id="methodField" name="_method" value="POST">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hari *</label>
                            <select name="hari" id="hari" class="w-full px-3 py-2 border rounded-lg" required>
                                <option value="">-- Pilih Hari --</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guru</label>
                            <select name="guru_id" id="guru_id" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($guruList as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Urutan *</label>
                            <input type="number" name="urutan" id="urutan" class="w-full px-3 py-2 border rounded-lg"
                                min="1" required>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Batal</button>
                            <button type="submit" id="submitBtn"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Jadwal Piket';
            document.getElementById('formPiket').action = '{{ route('guru-piket.store') }}';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('formPiket').reset();
            document.getElementById('hari').value = 'Senin';
            document.getElementById('submitBtn').innerText = 'Simpan';
            document.getElementById('modalPiket').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(id, data) {
            document.getElementById('modalTitle').innerText = 'Edit Jadwal Piket';
            document.getElementById('formPiket').action = `/guru-piket/${id}`;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('hari').value = data.hari || '';
            document.getElementById('guru_id').value = data.guru_id || '';
            document.getElementById('urutan').value = data.urutan || '';
            document.getElementById('submitBtn').innerText = 'Update';
            document.getElementById('modalPiket').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('modalPiket').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
