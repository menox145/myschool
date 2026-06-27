@extends('layouts.app')

@section('title', 'Tahun Pelajaran')

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

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Tahun Pelajaran</h3>
            <button onclick="openModalTapel()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Tahun Pelajaran
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tapel as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $t->tahun }}</td>
                            <td class="px-4 py-3 text-sm">{{ $t->semester }}</td>
                            <td class="px-4 py-3 text-sm">{{ $t->tgl_mulai?->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $t->tgl_selesai?->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if ($t->aktif)
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 font-medium">
                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick='editTapel(@json($t))'
                                    class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('tahun-pelajaran.destroy', $t->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus {{ $t->tahun }} - {{ $t->semester }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data tahun pelajaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Tapel -->
        <div id="modalTapel" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModalTapel()"></div>
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Tambah Tahun Pelajaran</h3>
                        <button onclick="closeModalTapel()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('tahun-pelajaran.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pelajaran *</label>
                            <input type="text" name="tahun" class="w-full px-3 py-2 border rounded-lg text-sm"
                                placeholder="2025/2026" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                            <select name="semester" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tgl Mulai</label>
                                <input type="date" name="tgl_mulai" class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tgl Selesai</label>
                                <input type="date" name="tgl_selesai" class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="aktif" value="1" class="mr-2 rounded">
                                <span class="text-sm text-gray-700">Jadikan semester aktif</span>
                            </label>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModalTapel()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Tapel -->
        <div id="modalEditTapel" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModalEditTapel()"></div>
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Edit Tahun Pelajaran</h3>
                        <button onclick="closeModalEditTapel()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="formEditTapel" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pelajaran *</label>
                            <input type="text" name="tahun" id="edit_tahun"
                                class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                            <select name="semester" id="edit_semester" class="w-full px-3 py-2 border rounded-lg text-sm"
                                required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tgl Mulai</label>
                                <input type="date" name="tgl_mulai" id="edit_tgl_mulai"
                                    class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tgl Selesai</label>
                                <input type="date" name="tgl_selesai" id="edit_tgl_selesai"
                                    class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="aktif" id="edit_aktif" value="1"
                                    class="mr-2 rounded">
                                <span class="text-sm text-gray-700">Jadikan semester aktif</span>
                            </label>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModalEditTapel()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModalTapel() {
            document.getElementById('modalTapel').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalTapel() {
            document.getElementById('modalTapel').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function editTapel(data) {
            document.getElementById('formEditTapel').action = `/tahun-pelajaran/${data.id}`;
            document.getElementById('edit_tahun').value = data.tahun;
            document.getElementById('edit_semester').value = data.semester;
            document.getElementById('edit_tgl_mulai').value = data.tgl_mulai;
            document.getElementById('edit_tgl_selesai').value = data.tgl_selesai;
            document.getElementById('edit_aktif').checked = data.aktif == 1;
            document.getElementById('modalEditTapel').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalEditTapel() {
            document.getElementById('modalEditTapel').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
