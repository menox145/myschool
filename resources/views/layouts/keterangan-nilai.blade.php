<table style="border-collapse: collapse; width: 100%; border: 1px solid #000; margin-top: 15px; max-width: 250px;">
    <tr>
        <td colspan="3">Keterangan dengan nilai</td>
    </tr>
    @php
        $keterangan = [
            ['range' => '91 – 100', 'predikat' => '(Mumtaz) ممتاز'],
            ['range' => '81 – 90', 'predikat' => '(Jayyid Jiddan) جيد جدا'],
            ['range' => '71 – 80', 'predikat' => '(Jayyid) جيد'],
            ['range' => '61 – 70', 'predikat' => '(Maqbul) مقبول'],
            ['range' => '0 – 60', 'predikat' => '(Rasib) راسب'],
        ];
    @endphp
    @foreach ($keterangan as $k)
        <tr>
            <td>{{ $k['predikat'] }}</td>
            <td style="width: 15px;">:</td>
            <td>{{ $k['range'] }}</td>
        </tr>
    @endforeach
</table>