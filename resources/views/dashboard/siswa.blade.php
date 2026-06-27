@extends('layouts.app')

@section('title', 'Data Siswa')

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

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Data Siswa</h3>
            <div class="flex gap-2">
                <button onclick="openModalImport()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center">
                    <i class="fas fa-file-import mr-2"></i>Import Excel
                </button>

                <a href="{{ route('siswa.print') }}{{ request()->search ? '?search=' . request()->search : '' }}"
                    target="_blank"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center">
                    <i class="fas fa-print mr-2"></i>Print PDF
                </a>
                <button onclick="openModalSiswa()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center">
                    <i class="fas fa-plus mr-2"></i>Tambah Siswa
                </button>
            </div>
        </div>

        <div class="flex gap-2 mb-4">
            <input type="text" id="search" value="{{ request()->search }}" placeholder="Cari nama / NIS..."
                class="px-3 py-2 border rounded-lg w-60">

            <select id="filter_jk" class="px-3 py-2 border rounded-lg">
                <option value="">Semua JK</option>
                <option value="L" {{ request()->jk == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ request()->jk == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>

            <select id="filter_kelas" class="px-3 py-2 border rounded-lg">
                <option value="">Semua Kelas</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ request()->kelas_id == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>

            <select id="filter_status" class="px-3 py-2 border rounded-lg">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ request()->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Lulus" {{ request()->status == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                <option value="Pindah" {{ request()->status == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                <option value="Drop Out" {{ request()->status == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
            </select>

            <button onclick="applyFilter()" class="px-4 py-2 bg-gray-800 text-white rounded-lg">
                <i class="fas fa-filter"></i> Filter
            </button>

            <button onclick="exportData()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg">
                <i class="fas fa-file-export"></i> Export
            </button>
        </div>

        <script>
            function applyFilter() {
                const params = new URLSearchParams();
                if (document.getElementById('search').value) params.append('search', document.getElementById('search').value);
                if (document.getElementById('filter_jk').value) params.append('jk', document.getElementById('filter_jk').value);
                if (document.getElementById('filter_kelas').value) params.append('kelas_id', document.getElementById(
                    'filter_kelas').value);
                if (document.getElementById('filter_status').value) params.append('status', document.getElementById(
                    'filter_status').value);

                window.location.href = '{{ route('siswa') }}?' + params.toString();
            }

            function exportData() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route('siswa.export') }}?' + params.toString();
            }

            // Enter di search langsung filter
            document.getElementById('search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') applyFilter();
            });
        </script>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NISN</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">JK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($siswa as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if ($s->foto)
                                    <img src="{{ asset('storage/' . $s->foto) }}"
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-sm font-semibold">
                                        {{ strtoupper(substr($s->nama, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $s->nis }}</td>
                            <td class="px-4 py-3 text-sm">{{ $s->nisn ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $s->nama }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 rounded-full text-xs {{ $s->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $s->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColor = [
                                        'Aktif' => 'bg-green-100 text-green-800',
                                        'Lulus' => 'bg-blue-100 text-blue-800',
                                        'Pindah' => 'bg-yellow-100 text-yellow-800',
                                        'Drop Out' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-full text-xs {{ $statusColor[$s->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick='editSiswa(@json($s))'
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('siswa.destroy', $s->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus {{ $s->nama }}?')">
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
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data siswa</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Siswa -->
        <div id="modalSiswa" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModalSiswa()"></div>

            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Tambah Data Siswa</h3>
                        <button onclick="closeModalSiswa()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('siswa.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIS *</label>
                                <input type="text" name="nis" value="{{ old('nis') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('nis') border-red-500 @enderror"
                                    required>
                                @error('nis')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">NISN *</label>
                                <input type="text" name="nisn" value="{{ old('nisn') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('nisn') border-red-500 @enderror"
                                    required placeholder="Dari sekolah asal">
                                @error('nisn')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                <input type="text" name="nama" value="{{ old('nama') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('nama') border-red-500 @enderror"
                                    required>
                                @error('nama')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                                <select name="jenis_kelamin"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('jenis_kelamin') border-red-500 @enderror"
                                    required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                                @error('jenis_kelamin')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
                                <select name="kelas_id"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('kelas_id') border-red-500 @enderror"
                                    required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}"
                                            {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('tgl_lahir') border-red-500 @enderror">
                                @error('tgl_lahir')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">No HP</label>
                                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('no_hp') border-red-500 @enderror">
                                @error('no_hp')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4 col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                <textarea name="alamat" rows="2"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4 col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                                <input type="file" name="foto" accept="image/*"
                                    class="w-full px-3 py-2 border rounded-lg text-sm @error('foto') border-red-500 @enderror">
                                @error('foto')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Input Ortu -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Penghasilan Ayah</label>
                                <input type="number" name="penghasilan_ayah" value="{{ old('penghasilan_ayah') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="5000000">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Penghasilan Ibu</label>
                                <input type="number" name="penghasilan_ibu" value="{{ old('penghasilan_ibu') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="3000000">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Anak Ke-</label>
                                <input type="number" name="anak_ke" value="{{ old('anak_ke') }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm" min="1">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Masuk</label>
                                <input type="number" name="tahun_masuk" value="{{ old('tahun_masuk', date('Y')) }}"
                                    class="w-full px-3 py-2 border rounded-lg text-sm" min="2000"
                                    max="{{ date('Y') }}">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="Lulus" {{ old('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                    <option value="Pindah" {{ old('status') == 'Pindah' ? 'selected' : '' }}>Pindah
                                    </option>
                                    <option value="Drop Out" {{ old('status') == 'Drop Out' ? 'selected' : '' }}>Drop Out
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModalSiswa()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- Modal Edit Siswa -->
    <div id="modalEditSiswa" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModalEditSiswa()"></div>

        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Edit Data Siswa</h3>
                    <button onclick="closeModalEditSiswa()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="formEditSiswa" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIS *</label>
                            <input type="text" name="nis" id="edit_nis"
                                class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">NISN *</label>
                            <input type="text" name="nisn" id="edit_nisn"
                                class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" name="nama" id="edit_nama"
                                class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin"
                                class="w-full px-3 py-2 border rounded-lg text-sm" required>
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
                            <select name="kelas_id" id="edit_kelas_id" class="w-full px-3 py-2 border rounded-lg text-sm"
                                required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" id="edit_tgl_lahir"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">No HP</label>
                            <input type="text" name="no_hp" id="edit_no_hp"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>

                        <div class="mb-4 col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                        </div>

                        <div class="mb-4 col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                            <input type="file" name="foto" accept="image/*"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                            <img id="edit_foto_preview" class="w-20 h-20 object-cover rounded mt-2 hidden">
                        </div>
                    </div>

                    <!-- Input Ortu -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                            <input type="text" name="nama_ayah" id="edit_nama_ayah"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                            <input type="text" name="nama_ibu" id="edit_nama_ibu"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" id="edit_pekerjaan_ayah"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" id="edit_pekerjaan_ibu"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penghasilan Ayah</label>
                            <input type="number" name="penghasilan_ayah" id="edit_penghasilan_ayah"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penghasilan Ibu</label>
                            <input type="number" name="penghasilan_ibu" id="edit_penghasilan_ibu"
                                class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Anak Ke-</label>
                            <input type="number" name="anak_ke" id="edit_anak_ke"
                                class="w-full px-3 py-2 border rounded-lg text-sm" min="1">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Masuk</label>
                            <input type="number" name="tahun_masuk" id="edit_tahun_masuk"
                                class="w-full px-3 py-2 border rounded-lg text-sm" min="2000"
                                max="{{ date('Y') }}">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" id="edit_status" class="w-full px-3 py-2 border rounded-lg text-sm"
                                required>
                                <option value="Aktif">Aktif</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Pindah">Pindah</option>
                                <option value="Drop Out">Drop Out</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeModalEditSiswa()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Import -->
    <div id="modalImport" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModalImport()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Import Data Siswa</h3>
                    <button onclick="closeModalImport()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" accept=".xlsx,.xls"
                            class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        <p class="text-xs text-gray-500 mt-1">
                            <a href="{{ route('siswa.template') }}" class="text-blue-600 hover:underline">
                                <i class="fas fa-download"></i> Download Template Excel
                            </a>
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModalImport()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                            <i class="fas fa-upload mr-1"></i>Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModalImport() {
            document.getElementById('modalImport').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalImport() {
            document.getElementById('modalImport').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        // ... script modal lainnya tetap
    </script>

    <script>
        function openModalSiswa() {
            document.getElementById('modalSiswa').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalSiswa() {
            document.getElementById('modalSiswa').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function editSiswa(data) {
            document.getElementById('formEditSiswa').action = `/siswa/${data.id}`;
            document.getElementById('edit_nis').value = data.nis;
            document.getElementById('edit_nama').value = data.nama;
            document.getElementById('edit_jenis_kelamin').value = data.jenis_kelamin;
            document.getElementById('edit_kelas_id').value = data.kelas_id;
            document.getElementById('edit_tgl_lahir').value = data.tgl_lahir;
            document.getElementById('edit_no_hp').value = data.no_hp;
            document.getElementById('edit_alamat').value = data.alamat;
            document.getElementById('edit_nama_ayah').value = data.nama_ayah;
            document.getElementById('edit_nama_ibu').value = data.nama_ibu;
            document.getElementById('edit_pekerjaan_ayah').value = data.pekerjaan_ayah;
            document.getElementById('edit_pekerjaan_ibu').value = data.pekerjaan_ibu;
            document.getElementById('edit_penghasilan_ayah').value = data.penghasilan_ayah;
            document.getElementById('edit_penghasilan_ibu').value = data.penghasilan_ibu;
            document.getElementById('edit_anak_ke').value = data.anak_ke;
            document.getElementById('edit_tahun_masuk').value = data.tahun_masuk;
            document.getElementById('edit_status').value = data.status;

            if (data.foto) {
                document.getElementById('edit_foto_preview').src = `/storage/${data.foto}`;
                document.getElementById('edit_foto_preview').classList.remove('hidden');
            } else {
                document.getElementById('edit_foto_preview').classList.add('hidden');
            }

            document.getElementById('modalEditSiswa').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalEditSiswa() {
            document.getElementById('modalEditSiswa').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        @if ($errors->any())
            openModalSiswa();
        @endif
    </script>
@endsection
