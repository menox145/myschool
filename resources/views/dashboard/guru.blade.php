@extends('layouts.app')

@section('title', 'Data Guru')

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
            <form method="GET" action="{{ route('guru') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama/NIP/email..."
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    <i class="fas fa-search mr-1"></i>Cari
                </button>
                @if ($search)
                    <a href="{{ route('guru') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                        Reset
                    </a>
                @endif
            </form>

            <div class="flex gap-2">
                <a href="{{ route('guru.export', ['search' => $search]) }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-all">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <button onclick="openModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all">
                    <i class="fas fa-plus mr-2"></i>Tambah Guru
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No HP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($guru as $index => $g)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                @if ($g->foto)
                                    <img src="{{ asset('storage/' . $g->foto) }}"
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $g->nip }}</td>
                            <td class="px-4 py-3">{{ $g->nama }}</td>
                            <td class="px-4 py-3">{{ $g->no_hp ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $g->email ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <button onclick="openEditModal({{ $g->id }}, @js($g))"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('guru.destroy', $g->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus data?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data guru</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah/Edit Guru -->
        <div id="modalGuru" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Data Guru</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="formGuru" action="{{ route('guru.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="methodField" name="_method" value="POST">

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIP *</label>
                                <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('nip') border-red-500 @enderror"
                                    required>
                                @error('nip')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('nama') border-red-500 @enderror"
                                    required>
                                @error('nama')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" id="tgl_lahir" value="{{ old('tgl_lahir') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('tgl_lahir') border-red-500 @enderror">
                                @error('tgl_lahir')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">No HP</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('no_hp') border-red-500 @enderror">
                                @error('no_hp')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                                <input type="text" name="nik" id="nik" maxlength="16"
                                    value="{{ old('nik') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('nik') border-red-500 @enderror">
                                @error('nik')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">No KK</label>
                                <input type="text" name="no_kk" id="no_kk" maxlength="16"
                                    value="{{ old('no_kk') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('no_kk') border-red-500 @enderror">
                                @error('no_kk')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto</label>
                                <input type="file" name="foto" id="foto" accept="image/*"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('foto') border-red-500 @enderror">
                                @error('foto')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <img id="preview" class="mt-2 w-20 h-20 rounded object-cover hidden">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">
                                Batal
                            </button>
                            <button type="submit" id="submitBtn"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Data Guru';
            document.getElementById('formGuru').action = "{{ route('guru.store') }}";
            document.getElementById('methodField').value = 'POST';
            document.getElementById('formGuru').reset();
            document.getElementById('preview').classList.add('hidden');
            document.getElementById('submitBtn').innerText = 'Simpan';
            document.getElementById('modalGuru').classList.remove('hidden');
        }

        function openEditModal(id, data) {
            document.getElementById('modalTitle').innerText = 'Edit Data Guru';
            document.getElementById('formGuru').action = `/guru/${id}`;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('nip').value = data.nip || '';
            document.getElementById('nama').value = data.nama || '';
            document.getElementById('tgl_lahir').value = data.tgl_lahir || '';
            document.getElementById('no_hp').value = data.no_hp || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('nik').value = data.nik || '';
            document.getElementById('no_kk').value = data.no_kk || '';
            if (data.foto) {
                document.getElementById('preview').src = `/storage/${data.foto}`;
                document.getElementById('preview').classList.remove('hidden');
            }
            document.getElementById('submitBtn').innerText = 'Update';
            document.getElementById('modalGuru').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalGuru').classList.add('hidden');
        }

        document.getElementById('foto').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                document.getElementById('preview').src = URL.createObjectURL(e.target.files[0]);
                document.getElementById('preview').classList.remove('hidden');
            }
        });

        @if ($errors->any())
            openModal();
        @endif
    </script>
@endsection
