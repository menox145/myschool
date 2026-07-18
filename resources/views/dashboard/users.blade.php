@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-center">
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

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Pengguna</h3>
                <p class="text-sm text-gray-500 mt-1">Total {{ $users->count() }} pengguna terdaftar</p>
            </div>
            <button onclick="openModalUser()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center whitespace-nowrap">
                <i class="fas fa-user-plus mr-2"></i>Tambah Pengguna
            </button>
        </div>

        {{-- Search Filter --}}
        <div class="mb-4">
            <input type="text" id="searchUser" onkeyup="filterUsers()" placeholder="Cari nama atau email..."
                class="w-full sm:w-72 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="tableUsers">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terdaftar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50 user-row">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs mr-3 flex-shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-800 user-name">{{ $u->name }}</span>
                                    @if ($u->id === auth()->id())
                                        <span class="ml-2 px-1.5 py-0.5 text-[10px] bg-blue-100 text-blue-700 rounded font-medium">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 user-email">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                @if ($u->role === 'admin')
                                    <span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800 font-medium">
                                        <i class="fas fa-shield-alt mr-1"></i>Admin
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 font-medium">
                                        <i class="fas fa-user mr-1"></i>User
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $u->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick='editUser(@json($u))'
                                    class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if ($u->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin hapus pengguna {{ addslashes($u->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-300" title="Tidak dapat menghapus akun sendiri">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-3xl text-gray-300 mb-2 block"></i>
                                Belum ada data pengguna
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modal Tambah Pengguna --}}
        <div id="modalUser" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModalUser()"></div>
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Tambah Pengguna</h3>
                        <button onclick="closeModalUser()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="contoh@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                            <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="user">User (Guru)</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password" id="add_password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                                    placeholder="Minimal 8 karakter" required>
                                <button type="button" onclick="togglePassword('add_password', 'icon_add_password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i id="icon_add_password" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModalUser()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Edit Pengguna --}}
        <div id="modalEditUser" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModalEditUser()"></div>
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Edit Pengguna</h3>
                        <button onclick="closeModalEditUser()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="formEditUser" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="edit_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="edit_email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="edit_role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="user">User (Guru)</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru
                                <span class="text-gray-400 font-normal">(kosongkan jika tidak diganti)</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="edit_password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" onclick="togglePassword('edit_password', 'icon_edit_password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i id="icon_edit_password" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModalEditUser()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModalUser() {
            document.getElementById('modalUser').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalUser() {
            document.getElementById('modalUser').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function editUser(data) {
            document.getElementById('formEditUser').action = `/users/${data.id}`;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_role').value = data.role;
            document.getElementById('edit_password').value = '';
            document.getElementById('modalEditUser').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalEditUser() {
            document.getElementById('modalEditUser').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function filterUsers() {
            const query = document.getElementById('searchUser').value.toLowerCase();
            document.querySelectorAll('.user-row').forEach(function(row) {
                const name = row.querySelector('.user-name')?.textContent.toLowerCase() ?? '';
                const email = row.querySelector('.user-email')?.textContent.toLowerCase() ?? '';
                row.style.display = (name.includes(query) || email.includes(query)) ? '' : 'none';
            });
        }

        // Auto-open modal on validation error
        @if ($errors->any() && old('_method') === 'PUT')
            editUser({
                id: '{{ old("_user_id") }}',
                name: '{{ old("name") }}',
                email: '{{ old("email") }}',
                role: '{{ old("role") }}',
            });
        @elseif ($errors->any())
            openModalUser();
        @endif
    </script>
@endsection
