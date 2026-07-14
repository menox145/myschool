<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Audit Nilai Harian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <h3>Audit Nilai Harian</h3>

        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Kelas</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Mata Pelajaran</label>
                <select name="kelas_mapel_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Mapel --</option>
                    @foreach ($kelas as $k)
                        @if (request('kelas_id') == $k->id)
                            @foreach ($k->kelasMapel as $km)
                                <option value="{{ $km->id }}"
                                    {{ request('kelas_mapel_id') == $km->id ? 'selected' : '' }}>
                                    {{ $km->mapel->nama_mapel ?? '-' }}</option>
                            @endforeach
                        @endif
                    @endforeach
                </select>
            </div>
        </form>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Mapel</th>
                    <th>Bab</th>
                    <th>Sub Bab</th>
                    <th>Nilai</th>
                    <th>Diinput Oleh</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditData as $item)
                    <tr>
                        <td>{{ $item->siswa->nama ?? '-' }}</td>
                        <td>{{ $item->kelasMapel->mapel->nama_mapel ?? '-' }}</td>
                        <td>{{ $item->subBab->bab->nama_bab ?? '-' }}</td>
                        <td>{{ $item->subBab->nama_sub_bab ?? '-' }}</td>
                        <td>{{ $item->nilai }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->updated_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data audit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
