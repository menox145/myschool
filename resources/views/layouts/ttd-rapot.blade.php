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
            @if (isset($siswa))
                <u><b>{{ optional($siswa->kelas->waliKelas)->name ?? '...........................' }}</b></u><br>
                NIP. {{ optional($siswa->kelas->waliKelas)->nip ?? '-' }}
            @elseif(isset($kelas))
                <u><b>{{ optional($kelas->waliKelas)->name ?? '...........................' }}</b></u><br>
                NIP. {{ optional($kelas->waliKelas)->nip ?? '-' }}
            @endif
        </td>
    </tr>
</table>
