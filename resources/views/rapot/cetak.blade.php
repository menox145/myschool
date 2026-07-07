<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Rapot {{ ucfirst($jenis_rapot) }} - {{ $siswa->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
        }

        .page {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 1.5cm;
        }

        .kop {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .kop table {
            width: 100%;
            border: none;
        }

        .kop td {
            border: none;
            vertical-align: middle;
        }

        .kop .logo {
            width: 100px;
            text-align: center;
        }

        .kop .logo img {
            width: 80px;
            height: 80px;
        }

        .kop .text {
            text-align: center;
        }

        .kop .text h3 {
            font-size: 14pt;
            margin-bottom: 2px;
        }

        .kop .text h1 {
            font-size: 18pt;
            margin-bottom: 2px;
        }

        .kop .text p {
            font-size: 10pt;
        }

        .judul {
            text-align: center;
            margin: 20px 0;
        }

        .judul h3 {
            font-size: 13pt;
            text-decoration: underline;
            margin-bottom: 3px;
        }

        .judul h4 {
            font-size: 12pt;
        }

        .biodata {
            width: 100%;
            margin-bottom: 15px;
        }

        .biodata td {
            padding: 3px;
            border: 1px solid #000;
        }

        .biodata .label {
            width: 130px;
            background: #f5f5f5;
        }

        .biodata .titik {
            width: 15px;
            text-align: center;
        }

        .nilai {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .nilai th,
        .nilai td {
            border: 1px solid #000;
            padding: 5px;
        }

        .nilai th {
            background: #e0e0e0;
            text-align: center;
        }

        .nilai .kelompok {
            background: #f5f5f5;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .ttd {
            width: 100%;
            margin-top: 40px;
        }

        .ttd td {
            border: none;
            text-align: center;
            vertical-align: top;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                margin: 0;
                padding: 1cm;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="padding: 10px; background: #f0f0f0; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 20px;">Print / Simpan PDF</button>
        <a href="{{ url()->previous() }}" style="margin-left: 10px;">Kembali</a>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop">
            <table>
                <tr>
                    <td class="logo">
                        {{-- Taruh logo di public/img/logo.png --}}
                        <img src="{{ asset('img/logo.png') }}" alt="Logo">
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
            <h4>PERKEMBANGAN {{ strtoupper($jenis_rapot) }}</h4>
        </div>

        {{-- BIODATA --}}
        {{-- BIODATA --}}
        <div style="margin: 20px 0; font-size: 11pt;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td width="130" style="border: none; padding: 2px 0;">Nama Siswa</td>
                    <td width="10" style="border: none;">:</td>
                    <td style="border: none;"><b>{{ strtoupper($siswa->nama) }}</b></td>
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
                    <td style="border: none;">{{ $siswa->nisn }}</td>
                    <td style="border: none;">Tahun Pelajaran</td>
                    <td style="border: none;">:</td>
                    <td style="border: none;">{{ $tahun->tahun }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px 0;">Nama Sekolah</td>
                    <td style="border: none;">:</td>
                    <td colspan="4" style="border: none;">SDTQ Nurul Ilmi Lilhuffadz</td>
                </tr>
            </table>
        </div>

        {{-- TABEL NILAI --}}
        {{-- TABEL NILAI --}}
        <table class="nilai">
            <thead>
                <tr>
                    <th rowspan="2" width="30px">No</th>
                    <th rowspan="2">Mata Pelajaran</th>
                    <th rowspan="2" width="50px">KKM</th>
                    <th colspan="4">Nilai</th>
                    <th rowspan="2" width="60px">Predikat</th>
                </tr>
                <tr>
                    <th width="50px">RPH</th>
                    <th width="50px">PTS</th>
                    <th width="50px">PAS</th>
                    <th width="60px">Akhir</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($nilai as $n)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $n->mapel_nama }}</td>
                        <td class="text-center">{{ $n->kkm ?? 75 }}</td>
                        <td class="text-center">{{ $n->rph ?: '-' }}</td>
                        <td class="text-center">{{ $n->pts ?: '-' }}</td>
                        <td class="text-center">{{ $n->pas ?: '-' }}</td>
                        <td class="text-center"><b>{{ $n->hpa ?: '-' }}</b></td>
                        <td class="text-center">{{ $n->predikat ?: '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6" class="text-center"><b>Rata-rata</b></td>
                    <td class="text-center"><b>{{ $rata_rata }}</b></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        {{-- TTD --}}
        <table class="ttd">
            <tr>
                <td width="50%">
                    Mengetahui,<br>
                    Orang Tua/Wali Siswa<br><br><br><br><br>
                    (...........................)
                </td>
                <td width="50%">
                    Bogor, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas<br><br><br><br><br>
                    @php $kelasRapot = $riwayat?->kelas ?? $siswa->kelasAktif?->kelas @endphp
                    <u><b>{{ optional($kelasRapot->waliKelas)->nama ?? '...........................' }}</b></u><br>
                    NIP. {{ optional($kelasRapot->waliKelas)->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
