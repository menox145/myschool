<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\TahunPelajaranController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\KelasMapelController;
use App\Http\Controllers\RapotController;
use App\Http\Controllers\NilaiHarianController;
use App\Http\Controllers\KenaikanKelasController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\GuruPiketController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // GRUP GURU: Admin + Guru bisa akses Akademik
    Route::middleware('user')->group(function () {
        // Nilai Harian
        Route::get('nilai/harian', [NilaiHarianController::class, 'index'])->name('nilai.harian');
        Route::post('nilai/harian', [NilaiHarianController::class, 'store'])->name('nilai.harian.store');
        Route::get('nilai/harian/export', [NilaiHarianController::class, 'export'])->name('nilai.harian.export');

        // Bab & Subbab
        Route::post('nilai/bab', [NilaiHarianController::class, 'storeBab'])->name('nilai.bab.store');
        Route::post('nilai/subbab', [NilaiHarianController::class, 'storeSubBab'])->name('nilai.subbab.store');
        Route::delete('nilai/bab/{id}', [NilaiHarianController::class, 'destroyBab'])->name('nilai.bab.destroy');
        Route::delete('nilai/subbab/{id}', [NilaiHarianController::class, 'destroySubBab'])->name('nilai.subbab.destroy');

        // Nilai UH
        Route::get('nilai/uh', [NilaiController::class, 'indexUh'])->name('nilai.uh');
        Route::post('nilai/uh', [NilaiController::class, 'storeUh'])->name('nilai.uh.store');

        // Nilai Akhir
        Route::get('nilai', [NilaiController::class, 'index'])->name('nilai.index');
        Route::post('nilai', [NilaiController::class, 'store'])->name('nilai.store');
        Route::get('nilai/template', [NilaiController::class, 'downloadTemplate'])->name('nilai.template');
        Route::post('nilai/import', [NilaiController::class, 'import'])->name('nilai.import');
        Route::get('nilai/audit', [NilaiHarianController::class, 'audit'])->name('nilai.audit');

        // Rapot
        Route::prefix('rapot')->name('rapot.')->group(function () {
            Route::get('/cetak', [RapotController::class, 'index'])->name('cetak.index');
            Route::get('/cetak/{siswa_id}/{tahun_pelajaran_id}/{jenis_rapot}', [RapotController::class, 'cetak'])->name('cetak.print');
            Route::get('/cetak-kelas/{tahun_pelajaran_id}/{kelas_id}/{jenis_rapot}', [RapotController::class, 'cetakKelas'])->name('cetak.kelas');
        });

        // Absen Siswa
        Route::get('absen', [AbsenController::class, 'index'])->name('absen.index');
        Route::post('absen', [AbsenController::class, 'store'])->name('absen.store');
        Route::get('absen/cetak/pdf', [AbsenController::class, 'cetak'])->name('absen.cetak.print');
    });
    // Riwayat Siswa: admin + guru dapat lihat perkembangan dari tahun ke tahun
    Route::get('siswa/riwayat', [DashboardController::class, 'riwayatIndex'])->name('siswa.riwayat.index');


    // GRUP ADMIN: Cuma Admin
    Route::middleware('admin')->group(function () {
        // Route Kelas
        Route::get('/kelas', [DashboardController::class, 'kelas'])->name('kelas');
        Route::post('/kelas', [DashboardController::class, 'storeKelas'])->name('kelas.store');
        Route::put('/kelas/{kelas}', [DashboardController::class, 'updateKelas'])->name('kelas.update');
        Route::delete('/kelas/{kelas}', [DashboardController::class, 'destroyKelas'])->name('kelas.destroy');
        Route::get('/kelas/export', [DashboardController::class, 'exportKelas'])->name('kelas.export');

        // Route Guru
        Route::get('/guru', [DashboardController::class, 'guru'])->name('guru');
        Route::post('/guru', [DashboardController::class, 'storeGuru'])->name('guru.store');
        Route::put('/guru/{id}', [DashboardController::class, 'updateGuru'])->name('guru.update');
        Route::delete('/guru/{id}', [DashboardController::class, 'destroyGuru'])->name('guru.destroy');
        Route::get('/guru/export', [DashboardController::class, 'exportGuru'])->name('guru.export'); // INI YANG KURANG

        // Route Siswa
        Route::prefix('siswa')->group(function () {
            Route::get('/', [DashboardController::class, 'siswa'])->name('siswa');
            Route::post('/', [DashboardController::class, 'storeSiswa'])->name('siswa.store');
            Route::put('/{siswa}', [DashboardController::class, 'updateSiswa'])->name('siswa.update');
            Route::delete('/{siswa}', [DashboardController::class, 'destroySiswa'])->name('siswa.destroy');
            Route::get('/export', [DashboardController::class, 'exportSiswa'])->name('siswa.export');
            Route::post('/import', [DashboardController::class, 'importSiswa'])->name('siswa.import');
            Route::get('/template', [DashboardController::class, 'downloadTemplateSiswa'])->name('siswa.template');
            Route::get('/print', [DashboardController::class, 'printSiswa'])->name('siswa.print');
        });

        // Resource Routes
        Route::resource('mapel', MapelController::class);
        Route::resource('tahun-pelajaran', TahunPelajaranController::class);
        Route::resource('kelas-mapel', KelasMapelController::class)->except(['show', 'create', 'edit']);
        Route::post('kelas-mapel/{id}/set-uh', [KelasMapelController::class, 'setJumlahUh'])->name('kelas-mapel.set-uh');

        // Resource Nilai - taro paling bawah biar nggak nabrak route spesifik di atas
        Route::resource('nilai', NilaiController::class)->except(['index', 'store']);
    });

    // GRUP ADMIN: Cuma Admin
    Route::middleware('admin')->group(function () {
        // Route Kenaikan Kelas
        Route::get('kenaikan-kelas', [KenaikanKelasController::class, 'index'])->name('kenaikan-kelas.index');
        Route::post('kenaikan-kelas', [KenaikanKelasController::class, 'store'])->name('kenaikan-kelas.store');
        Route::get('api/siswa-by-kelas', [KenaikanKelasController::class, 'getSiswaByKelas'])->name('api.siswa-by-kelas');

        // Master Jadwal Piket
        Route::resource('guru-piket', GuruPiketController::class)->except(['create', 'show', 'edit']);
    });
});
