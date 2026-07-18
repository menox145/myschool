{{-- KOP SURAT --}}
<div class="kop">
    <table>
        <tr>
            <td class="logo">
                <img src="{{ asset('logo.png') }}" alt="Logo">
            </td>
            <td class="text">
                <h3>YAYASAN TAZKIYATUN NUFUS LITTAWWABIN</h3>
                <h1>SDTQ NURUL ILMI LILHUFFADZ</h1>
                <p>Jl. Kamp. Sawah RT 03 RW 01 Kel. Ragajaya, Kec. Bojonggede, Kab. Bogor</p>
                <p>Email : sdtahfidznurulilmi@gmail.com</p>
            </td>
        </tr>
    </table>
</div>

{{-- JUDUL --}}
<div class="judul">
    <h3>CAPAIAN PEMBELAJARAN</h3>
    <h4>PERKEMBANGAN {{ strtoupper($jenis ?? 'SISWA') }}</h4>
</div>

{{-- BIODATA: PASTIKAN CUMA 1 YANG KEPRINT --}}
@if (isset($siswa) && ($mode ?? 'siswa') == 'siswa')
    <div style="margin: 20px 0; font-size: 11pt;">
        <table style="width: 100%; border: none;">
            <tr>
                <td width="130" style="border: none; padding: 2px 0;">Nama Siswa</td>
                <td width="10" style="border: none;">:</td>
                <td style="border: none;"><b>{{ strtoupper($siswa->nama ?? '-') }}</b></td>
                <td width="130" style="border: none;">Kelas</td>
                <td width="10" style="border: none;">:</td>
                <td style="border: none;">
                    <b>{{ $riwayat?->kelas?->nama_kelas ?? $siswa->kelasAktif?->kelas?->nama_kelas ?? '-' }}</b>
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">Nomor Induk</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $siswa->nis ?? '-' }}</td>
                <td style="border: none;">Semester</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $tahun->semester == 'Ganjil' ? 'I / Ganjil' : 'II / Genap' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">NISN</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $siswa->nisn ?? '-' }}</td>
                <td style="border: none;">Tahun Pelajaran</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $tahun->tahun ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">Nama Sekolah</td>
                <td style="border: none;">:</td>
                <td colspan="4" style="border: none;">SDTQ Nurul Ilmi Lilhuffadz</td>
            </tr>
        </table>
    </div>
@elseif(isset($kelas) && ($mode ?? '') == 'kelas')
    <div style="margin: 20px 0; font-size: 11pt;">
        <table style="width: 100%; border: none;">
            <tr>
                <td width="130" style="border: none; padding: 2px 0;">Kelas</td>
                <td width="10" style="border: none;">:</td>
                <td style="border: none;"><b>{{ $kelas->nama_kelas }}</b></td>
                <td width="130" style="border: none;">Wali Kelas</td>
                <td width="10" style="border: none;">:</td>
                <td style="border: none;"><b>{{ optional($kelas->waliKelas)->nama ?? '-' }}</b></td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">Semester</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $tahun->semester == 'Ganjil' ? 'I / Ganjil' : 'II / Genap' }}</td>
                <td style="border: none;">Tahun Pelajaran</td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $tahun->tahun }}</td>
            </tr>
        </table>
    </div>
@endif
