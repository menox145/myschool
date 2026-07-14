<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Cetak Absen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        h2,
        h4 {
            margin: 0;
            text-align: center;
        }

        .meta {
            margin: 16px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background: #f0f0f0;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>REKAP ABSEN SISWA</h2>
    <h4>{{ $kelas->nama_kelas }} - {{ $tahun->tahun }} {{ $tahun->semester }}</h4>

    <table class="meta">
        <tr>
            <td><strong>Periode</strong></td>
            <td>{{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</td>
            <td><strong>Wali Kelas</strong></td>
            <td>{{ $kelas->waliKelas->nama ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alpa</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswa as $s)
                @php
                    $data = $absens->get($s->id, collect());
                    $catatan = $data->whereNotNull('keterangan')->pluck('keterangan')->filter()->implode('; ');
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $s->nis }}</td>
                    <td>{{ $s->nama }}</td>
                    <td class="center">{{ $data->where('status', 'Hadir')->count() }}</td>
                    <td class="center">{{ $data->where('status', 'Sakit')->count() }}</td>
                    <td class="center">{{ $data->where('status', 'Izin')->count() }}</td>
                    <td class="center">{{ $data->where('status', 'Alpa')->count() }}</td>
                    <td>{{ $catatan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
