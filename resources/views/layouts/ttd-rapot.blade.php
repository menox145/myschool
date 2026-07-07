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
                @php $kelasRapot = $riwayat?->kelas ?? $siswa->kelasAktif?->kelas @endphp
                <u><b>{{ optional($kelasRapot->waliKelas)->nama ?? '...........................' }}</b></u><br>
                NIP. {{ optional($kelasRapot->waliKelas)->nip ?? '-' }}
            @elseif(isset($kelas))
                <u><b>{{ optional($kelas->waliKelas)->nama ?? '...........................' }}</b></u><br>
                NIP. {{ optional($kelas->waliKelas)->nip ?? '-' }}
            @endif
        </td>
    </tr>
</table>
