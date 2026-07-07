@extends('layouts.app')

@section('title', 'Kenaikan Kelas')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Registrasi / Kenaikan Kelas</h3>
            <p class="text-sm text-gray-500">Registrasikan siswa baru, pindahkan kelas, atau proses kenaikan kelas</p>
        </div>

        <form action="{{ route('kenaikan-kelas.store') }}" method="POST" id="form-kenaikan">
            @csrf
            <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <label class="block text-xs font-medium text-blue-800 mb-1">Mode Proses *</label>
                <select name="mode" id="mode"
                    class="w-full px-3 py-2 border border-blue-300 rounded-lg text-sm @error('mode') border-red-500 @enderror"
                    required>
                    <option value="registrasi" @selected(old('mode', 'registrasi') == 'registrasi')>
                        Registrasi Awal Siswa ke Kelas
                    </option>
                    <option value="pindah" @selected(old('mode') == 'pindah')>
                        Pindah / Koreksi Kelas Tahun Ini
                    </option>
                    <option value="kenaikan" @selected(old('mode') == 'kenaikan')>
                        Kenaikan Kelas ke Tahun Berikutnya
                    </option>
                </select>
                <p class="text-xs text-blue-700 mt-2" id="mode-info">
                    Pilih siswa yang belum punya kelas pada tahun tujuan, lalu masukkan ke kelas.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border">
                <div id="group-tahun-lama">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Pelajaran Asal *</label>
                    <select name="tahun_lama_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('tahun_lama_id') border-red-500 @enderror"
                        >
                        <option value="">-- Pilih Tahun Lama --</option>
                        @foreach ($allTapel as $tp)
                            <option value="{{ $tp->id }}" @selected(old('tahun_lama_id') == $tp->id)>{{ $tp->tahun }} -
                                {{ $tp->semester }}</option>
                        @endforeach
                    </select>
                    @error('tahun_lama_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div id="group-kelas-lama">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kelas Asal *</label>
                    <select name="kelas_lama_id" id="kelas_lama"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('kelas_lama_id') border-red-500 @enderror"
                        >
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" data-tingkat="{{ $k->tingkat }}"
                                @selected(old('kelas_lama_id') == $k->id)>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('kelas_lama_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div id="group-tahun-baru">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Pelajaran Tujuan *</label>
                    <select name="tahun_baru_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('tahun_baru_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Tahun Baru --</option>
                        @foreach ($allTapel as $tp)
                            <option value="{{ $tp->id }}" @selected(old('tahun_baru_id', $tp->aktif ? $tp->id : '') == $tp->id)>{{ $tp->tahun }} -
                                {{ $tp->semester }} @if ($tp->aktif)
                                    (Aktif)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('tahun_baru_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="list-siswa" class="hidden">
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                    <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                        <h4 class="text-sm font-semibold">Daftar Siswa</h4>
                        <label class="text-xs flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="check-all"> Pilih Semua
                        </label>
                    </div>
                    <div class="divide-y max-h-96 overflow-y-auto" id="siswa-container"></div>
                </div>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                    <label class="block text-xs font-medium text-blue-800 mb-1">Pindahkan Ke Kelas *</label>
                    <select name="kelas_baru_id" id="kelas_baru"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('kelas_baru_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Kelas Tujuan --</option>
                    </select>
                    <p class="text-xs text-blue-600 mt-1" id="info-kelas-tujuan">Siswa yang dicentang akan dipindah ke kelas
                        ini</p>
                    @error('kelas_baru_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @error('siswa_ids')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror

                <button type="submit"
                    class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:bg-gray-400"
                    id="btn-submit" disabled>
                    <i class="fas fa-arrow-right mr-2"></i>Proses Kenaikan Kelas
                </button>
            </div>
        </form>
    </div>

    <script>
        const semuaKelas = @json($kelas);
        const modeSelect = document.getElementById('mode');
        const kelasLamaSelect = document.getElementById('kelas_lama');
        const tahunLamaSelect = document.querySelector('[name="tahun_lama_id"]');
        const tahunBaruSelect = document.querySelector('[name="tahun_baru_id"]');
        const kelasBaruSelect = document.getElementById('kelas_baru');
        const listSiswa = document.getElementById('list-siswa');
        const siswaContainer = document.getElementById('siswa-container');
        const btnSubmit = document.getElementById('btn-submit');
        const infoKelasTujuan = document.getElementById('info-kelas-tujuan');
        const modeInfo = document.getElementById('mode-info');
        const groupTahunLama = document.getElementById('group-tahun-lama');
        const groupKelasLama = document.getElementById('group-kelas-lama');

        function setModeUi() {
            const mode = modeSelect.value;
            const pakaiAsal = mode !== 'registrasi';

            groupTahunLama.classList.toggle('hidden', !pakaiAsal);
            groupKelasLama.classList.toggle('hidden', !pakaiAsal);
            tahunLamaSelect.disabled = !pakaiAsal;
            kelasLamaSelect.disabled = !pakaiAsal;
            tahunLamaSelect.required = pakaiAsal && mode === 'kenaikan';
            kelasLamaSelect.required = pakaiAsal;

            if (mode === 'registrasi') {
                modeInfo.textContent = 'Menampilkan siswa aktif yang belum punya kelas pada tahun tujuan.';
                btnSubmit.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Registrasikan Siswa';
            } else if (mode === 'pindah') {
                modeInfo.textContent = 'Memindahkan siswa dari kelas asal ke kelas tujuan pada tahun yang sama.';
                btnSubmit.innerHTML = '<i class="fas fa-exchange-alt mr-2"></i>Pindahkan Kelas';
            } else {
                modeInfo.textContent = 'Memproses kenaikan dari tahun/kelas asal ke tahun/kelas tujuan.';
                btnSubmit.innerHTML = '<i class="fas fa-arrow-right mr-2"></i>Proses Kenaikan Kelas';
            }

            listSiswa.classList.add('hidden');
            siswaContainer.innerHTML = '';
            kelasBaruSelect.innerHTML = '<option value="">-- Pilih Kelas Tujuan --</option>';
            kelasBaruSelect.disabled = false;
            kelasBaruSelect.required = true;
            updateSubmitButton();
        }

        function loadSiswaDanFilterKelas() {
            const mode = modeSelect.value;
            let kelasId = kelasLamaSelect.value;
            let tahunId = mode === 'kenaikan' ? tahunLamaSelect.value : tahunBaruSelect.value;

            if ((!kelasId && mode !== 'registrasi') || !tahunId) {
                listSiswa.classList.add('hidden');
                return;
            }

            // 1. Filter kelas tujuan
            let kelasLama = semuaKelas.find(k => k.id == kelasId);
            let kelasTujuan = [];

            kelasBaruSelect.innerHTML = '<option value="">-- Pilih Kelas Tujuan --</option>';

            if (mode === 'registrasi') {
                infoKelasTujuan.innerHTML = 'Pilih kelas untuk siswa yang belum punya kelas pada tahun tujuan';
                kelasBaruSelect.disabled = false;
                kelasBaruSelect.required = true;
                kelasTujuan = semuaKelas;
                kelasTujuan.forEach(k => {
                    kelasBaruSelect.innerHTML += `<option value="${k.id}">${k.nama_kelas}</option>`;
                });
            } else if (mode === 'pindah') {
                infoKelasTujuan.innerHTML = 'Pilih kelas tujuan koreksi/pindah kelas pada tahun yang sama';
                kelasBaruSelect.disabled = false;
                kelasBaruSelect.required = true;
                kelasTujuan = semuaKelas.filter(k => k.id != kelasId);
                kelasTujuan.forEach(k => {
                    kelasBaruSelect.innerHTML += `<option value="${k.id}">${k.nama_kelas}</option>`;
                });
            } else if (kelasLama.tingkat == 6) {
                infoKelasTujuan.innerHTML =
                    '<span class="text-amber-600">Siswa kelas 6 akan diluluskan, tidak pindah ke kelas baru.</span>';
                kelasBaruSelect.disabled = true;
                kelasBaruSelect.required = false;
            } else {
                kelasTujuan = semuaKelas.filter(k => k.tingkat > kelasLama.tingkat);
                if (kelasTujuan.length === 0) {
                infoKelasTujuan.innerHTML =
                    '<span class="text-red-600">Tidak ada kelas dengan tingkat lebih tinggi.</span>';
                kelasBaruSelect.disabled = true;
                } else {
                infoKelasTujuan.innerHTML = 'Siswa yang dicentang akan dipindah ke kelas ini';
                kelasBaruSelect.disabled = false;
                kelasBaruSelect.required = true;
                kelasTujuan.forEach(k => {
                    kelasBaruSelect.innerHTML += `<option value="${k.id}">${k.nama_kelas}</option>`;
                });
                }
            }

            // 2. Load siswa
            siswaContainer.innerHTML = '<p class="p-4 text-sm text-gray-400 text-center">Loading...</p>';
            listSiswa.classList.remove('hidden');

            fetch(`{{ route('api.siswa-by-kelas') }}?mode=${mode}&tahun_id=${tahunId}&kelas_id=${kelasId || ''}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(s => {
                            html += `
                        <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="siswa_ids[]" value="${s.siswa.id}" class="siswa-check">
                            <div class="flex-1">
                                <p class="text-sm font-medium">${s.siswa.nama}</p>
                                <p class="text-xs text-gray-500">NIS: ${s.siswa.nis}</p>
                            </div>
                        </label>`;
                        });
                    } else {
                        html = mode === 'registrasi'
                            ? '<p class="p-4 text-sm text-gray-400 text-center">Tidak ada siswa aktif yang belum punya kelas di tahun tujuan</p>'
                            : '<p class="p-4 text-sm text-gray-400 text-center">Tidak ada siswa aktif di kelas ini</p>';
                    }
                    siswaContainer.innerHTML = html;
                    updateSubmitButton();
                });
        }

        function updateSubmitButton() {
            const checkedSiswa = document.querySelectorAll('.siswa-check:checked').length;
            const kelasBaruValid = kelasBaruSelect.disabled || kelasBaruSelect.value;
            btnSubmit.disabled = !(checkedSiswa > 0 && kelasBaruValid);
        }

        modeSelect.addEventListener('change', function() {
            setModeUi();
            loadSiswaDanFilterKelas();
        });
        kelasLamaSelect.addEventListener('change', loadSiswaDanFilterKelas);
        tahunLamaSelect.addEventListener('change', loadSiswaDanFilterKelas);
        tahunBaruSelect.addEventListener('change', loadSiswaDanFilterKelas);
        kelasBaruSelect.addEventListener('change', updateSubmitButton);

        document.getElementById('check-all').addEventListener('change', function() {
            document.querySelectorAll('.siswa-check').forEach(cb => cb.checked = this.checked);
            updateSubmitButton();
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('siswa-check')) {
                updateSubmitButton();
            }
        });

        setModeUi();
        loadSiswaDanFilterKelas();
    </script>
@endsection
