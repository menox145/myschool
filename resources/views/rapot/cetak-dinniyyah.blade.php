<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Rapot dinniyyah - {{ $siswa->nama }}</title>
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
        {{-- PANGGIL HEADER --}}
        @include('layouts.kop-rapot', [
            'siswa' => $siswa,
            'riwayat' => $riwayat,
            'tahun' => $tahun,
            'jenis' => 'DINIYYAH',
        ])

        {{-- JUDUL --}}

        {{-- TABEL NILAI AKADEMIK --}}
        <table class="nilai">
            <thead>
                <tr>
                    <th width="30px">No</th>
                    <th>Mata Pelajaran</th>
                    <th width="70px">KKM</th>
                    <th width="70px">Nilai</th>
                    <th width="150px">Terbilang</th>
                    <th width="60px">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($nilai as $n)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $n->mapel_nama }}</td>
                        <td class="text-center">{{ $n->kkm ?? 75 }}</td>
                        <td class="text-center"><b>{{ $n->hpa ?: '-' }}</b></td>
                        <td class="text-center">{{ \App\Helpers\NumberHelper::terbilang($n->hpa) }}</td>
                        <td class="text-center">{{ $n->predikat ?: '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-center"><b>Total Nilai</b></td>
                    <td colspan="4" class="text-center"><b>{{ $total_nilai ?? 0 }}</b></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center"><b>Rata-rata</b></td>
                    <td colspan="4" class="text-center"><b>{{ $rata_rata ?? 0 }}</b></td>
                </tr>
            </tbody>
        </table>

        {{-- PANGGIL FOOTER --}}
        @include('layouts.ttd-rapot', ['siswa' => $siswa, 'riwayat' => $riwayat])
    </div>
</body>

</html>
