<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Data Siswa</title>
    <style>
        body {
            font-family: Arial;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background: #f0f0f0;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>DATA SISWA</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>JK</th>
                <th>Kelas</th>
                <th>alamat</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($siswa as $s)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->nis }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->jenis_kelamin }}</td>
                    <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $s->alamat ?? '-' }}</td>
                    <td>{{ $s->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
composer require barryvdh/laravel-dompdf
