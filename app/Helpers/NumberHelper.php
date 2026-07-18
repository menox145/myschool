<?php

namespace App\Helpers;

class NumberHelper
{
    public static function terbilang(int|float $nilai): string
    {
        $nilai = (int) $nilai;

        if ($nilai === 0) {
            return 'Nol';
        }

        $hasil = trim(self::penyebut(abs($nilai)));

        if ($nilai < 0) {
            $hasil = 'Minus ' . $hasil;
        }

        return ucwords($hasil);
    }

    private static function penyebut(int $nilai): string
    {
        $huruf = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($nilai < 12) {
            return ' ' . $huruf[$nilai];
        }

        if ($nilai < 20) {
            return self::penyebut($nilai - 10) . ' belas';
        }

        if ($nilai < 100) {
            return self::penyebut(intdiv($nilai, 10))
                . ' puluh'
                . self::penyebut($nilai % 10);
        }

        if ($nilai < 200) {
            return ' seratus' . self::penyebut($nilai - 100);
        }

        if ($nilai < 1000) {
            return self::penyebut(intdiv($nilai, 100))
                . ' ratus'
                . self::penyebut($nilai % 100);
        }

        if ($nilai < 2000) {
            return ' seribu' . self::penyebut($nilai - 1000);
        }

        if ($nilai < 1_000_000) {
            return self::penyebut(intdiv($nilai, 1000))
                . ' ribu'
                . self::penyebut($nilai % 1000);
        }

        if ($nilai < 1_000_000_000) {
            return self::penyebut(intdiv($nilai, 1_000_000))
                . ' juta'
                . self::penyebut($nilai % 1_000_000);
        }

        if ($nilai < 1_000_000_000_000) {
            return self::penyebut(intdiv($nilai, 1_000_000_000))
                . ' miliar'
                . self::penyebut($nilai % 1_000_000_000);
        }

        if ($nilai < 1_000_000_000_000_000) {
            return self::penyebut(intdiv($nilai, 1_000_000_000_000))
                . ' triliun'
                . self::penyebut($nilai % 1_000_000_000_000);
        }

        return '';
    }
}