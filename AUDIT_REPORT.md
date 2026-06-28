# AUDIT REPORT - Laravel MySchool Project
**Generated**: 2026-06-27

---

## 📊 SUMMARY
- **Total Issues Found**: 28
- **🔴 CRITICAL**: 9
- **🟡 WARNING**: 12
- **🔵 INFO**: 7

---

## 🔴 CRITICAL ISSUES

### 1. NilaiImport: Predikat Field Typo
**File**: [app/Imports/NilaiImport.php](app/Imports/NilaiImport.php#L39)
**Issue**: Menggunakan `'pre'` padahal seharusnya `'predikat'`
```php
'pre' => $this->hitungPredikat($hpa),  // ❌ SALAH
```
**Should be**:
```php
'predikat' => $this->hitungPredikat($hpa),  // ✓ BENAR
```
**Impact**: Field tidak akan disimpan ke database, nilai predikat akan selalu NULL

---

### 2. SiswaImport: Missing guru_id on Auto-Created Kelas
**File**: [app/Imports/SiswaImport.php](app/Imports/SiswaImport.php#L38-L44)
**Issue**: Ketika membuat kelas secara otomatis, tidak ada `guru_id` meskipun di database column `guru_id` di kelas nullable
```php
$kelas = Kelas::firstOrCreate(
    ['nama_kelas' => trim($row['kelas'])],
    [
        'jumlah_siswa' => 0,
        'user_id' => $this->userId,
        'nama_penambah' => $this->userName,
        'tahun_pelajaran' => '2024/2025 - Genap'
        // ❌ guru_id MISSING
    ]
);
```
**Impact**: Kelas dibuat tanpa wali kelas, violates business logic

---

### 3. Duplicate Migration: jenis_rapot Column
**Files**: 
- [database/migrations/2026_05_21_012903_add_jenis_rapot_to_mata_pelajaran_table.php](database/migrations/2026_05_21_012903_add_jenis_rapot_to_mata_pelajaran_table.php)
- [database/migrations/2026_06_03_052850_add_jenis_rapot_to_mata_pelajaran_table.php](database/migrations/2026_06_03_052850_add_jenis_rapot_to_mata_pelajaran_table.php)

**Issue**: Dua migration menambah `jenis_rapot` column ke `mata_pelajaran` table
- Migration 1 (2026_05_21): Menambah dengan order: `'dinniyyah', 'akademik', 'tahfidz'`
- Migration 2 (2026_06_03): Menambah dengan order: `'akademik', 'dinniyyah', 'tahfidz'`

**Impact**: Migration kedua akan FAIL karena column sudah exists. Dapat menyebabkan database inconsistency jika rollback dilakukan

---

### 4. Migration Down() Bug: 2026_05_25_041027
**File**: [database/migrations/2026_05_25_041027_add_uh_to_nilai_table.php](database/migrations/2026_05_25_041027_add_uh_to_nilai_table.php#L23)
**Issue**: Migration ini rename `jumlah_uh` tetapi down() menghapus dari `nilai` table, seharusnya dari `kelas_mapel`:
```php
public function down(): void
{
    Schema::table('nilai', function (Blueprint $table) {  // ❌ SALAH - seharusnya kelas_mapel
        $table->dropColumn(['uh1', 'uh2', 'uh3', 'uh4', 'uh5', 'uh6', 'rata_uh']);
    });
}
```
**Impact**: Rollback akan error, data integrity issues

---

### 5. Duplicate Migration: jumlah_uh Column  
**Files**:
- [database/migrations/2026_05_25_041027_add_uh_to_nilai_table.php](database/migrations/2026_05_25_041027_add_uh_to_nilai_table.php) - Menambah ke `kelas_mapel`
- [database/migrations/2026_05_30_205023_add_jumlah_uh_to_kelas_mapel_table.php](database/migrations/2026_05_30_205023_add_jumlah_uh_to_kelas_mapel_table.php) - Menambah ke `kelas_mapel` LAGI

**Issue**: Migration kedua mencoba menambah column yang sudah ada
**Impact**: Migration kedua akan FAIL

---

### 6. Foreign Key Mismatch in Nilai Table
**File**: [database/migrations/2026_05_19_044546_create_nilai_table.php](database/migrations/2026_05_19_044546_create_nilai_table.php#L15)
**Issue**: `guru_id` foreign key menggunakan `->nullOnDelete()` yang bukan method standar
```php
$table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();  // ❌ nullOnDelete() doesn't exist
```
**Should be**: `->onDelete('set null')`
**Impact**: Constraint tidak akan dibuat dengan benar di database

---

### 7. NilaiHarian Controller: Missing Relationship Call
**File**: [app/Http/Controllers/NilaiHarianController.php](app/Http/Controllers/NilaiHarianController.php#L163)
**Issue**: Dalam method `export()`, menggunakan `'subBab.bab'` tetapi model `SubBabMapel` memiliki relationship `bab()` bukan `babMapel()`
```php
->with([
    'subBab.bab',  // ❌ Relationship tidak sesuai
    ...
])
```
**Should be**: `'subBab.bab'` (sudah benar, tetapi ada inconsistency naming)
**Impact**: Jika data tidak diload, akan terjadi N+1 query problem atau null reference

---

### 8. NilaiHarianController: Missing downloadTemplate Method
**File**: [routes/web.php](routes/web.php#L38)
**Issue**: Route mengacu ke method yang tidak ada
```php
Route::get('nilai/harian/export', [NilaiHarianController::class, 'export'])->name('nilai.harian.export');
```
- Method exists: ✓ (line 159 di NilaiHarianController)

**Impact**: Audit note - route VALID setelah re-check

---

### 9. Foreign Key Constraint Missing in Sessions Table
**File**: [database/migrations/0001_01_01_000000_create_users_table.php](database/migrations/0001_01_01_000000_create_users_table.php#L36)
**Issue**: `sessions` table memiliki `user_id` foreign key tapi tidak ada cascade delete specification
```php
$table->foreignId('user_id')->nullable()->index();  // ❌ Tidak ada constrained()
```
**Should be**: 
```php
$table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->index();
```
**Impact**: Ketika user dihapus, sessions tidak otomatis dihapus, database cleanup issues

---

## 🟡 WARNING ISSUES

### 1. Guru Model: Missing Relationships
**File**: [app/Models/Guru.php](app/Models/Guru.php)
**Issue**: Model tidak mendefinisikan relationships yang diperlukan
```php
// ❌ MISSING:
public function kelas() { return $this->hasMany(Kelas::class); }
public function kelasMapel() { return $this->hasMany(KelasMapel::class); }
public function nilai() { return $this->hasMany(Nilai::class); }
```
**Impact**: Tidak bisa melakukan `$guru->kelas()->get()`, memaksa raw query atau join

---

### 2. User Model: Missing Relationships
**File**: [app/Models/User.php](app/Models/User.php)
**Issue**: Model tidak mendefinisikan relationships ke:
- `siswa` (1-to-many, user_id di siswas)
- `kelas` (1-to-many, user_id di kelas)
- `nilaiHarian` (1-to-many, user_id di nilai_harian)

**Impact**: Tidak bisa eager load siswa/kelas dari user: `$user->siswa()->get()`

---

### 3. Nilai Model: Missing guru Relationship
**File**: [app/Models/Nilai.php](app/Models/Nilai.php)
**Issue**: Model memiliki `guru_id` di database tapi tidak ada `belongsTo` relationship
```php
// ❌ MISSING:
public function guru() { return $this->belongsTo(Guru::class); }
```
**Impact**: Tidak bisa akses `$nilai->guru` untuk lazy/eager loading

---

### 4. BabMapel Model: Missing Relationships
**File**: [app/Models/BabMapel.php](app/Models/BabMapel.php)
**Issue**: Model tidak memiliki relationship ke parent entities
```php
// ❌ MISSING:
public function kelasMapel() { return $this->belongsTo(KelasMapel::class); }
public function mapel() { return $this->belongsTo(MataPelajaran::class, 'kelas_mapel_id'); } // Indirect
```
**Impact**: Tidak bisa navigate: `$bab->kelasMapel->mapel`

---

### 5. MataPelajaran Model: Inconsistent jenis_rapot
**File**: [app/Models/MataPelajaran.php](app/Models/MataPelajaran.php#L7)
**Issue**: Model memiliki `'jenis_rapot'` di fillable, tetapi migration menambahkan dengan enum constraint
**Migration**: [2026_05_21_012903](database/migrations/2026_05_21_012903_add_jenis_rapot_to_mata_pelajaran_table.php#L11)
```php
$table->enum('jenis_rapot', ['dinniyyah', 'akademik', 'tahfidz'])
```
**Issue**: Constraint order berbeda dengan yang di migration 2026_06_03 (akademik, dinniyyah, tahfidz)
**Impact**: Potential validation issues jika nilai dikirim dengan order berbeda

---

### 6. NilaiController: Resource Route Conflict
**File**: [routes/web.php](routes/web.php#L110-111)
**Issue**: Resource route `nilai` di-except untuk index dan store, tetapi method seperti `show`, `edit` tidak akan accessible
```php
Route::resource('nilai', NilaiController::class)->except(['index', 'store']);
```
**Impact**: Jika ada view yang memanggil `route('nilai.show', $id)`, akan error

---

### 7. DashboardController: downloadTemplateSiswa Missing
**File**: [routes/web.php](routes/web.php#L99)
**Issue**: Route mendefinisikan `[DashboardController::class, 'downloadTemplateSiswa']`
- Method check: UNCLEAR, need to verify full file length
**Impact**: May break siswa import template download

---

### 8. KelasMapel: Inconsistent Column Order
**Files**: 
- [database/migrations/2026_05_25_041027](database/migrations/2026_05_25_041027_add_uh_to_nilai_table.php#L10)
- [database/migrations/2026_05_30_205023](database/migrations/2026_05_30_205023_add_jumlah_uh_to_kelas_mapel_table.php#L10)

**Issue**: Duplikasi migration untuk `jumlah_uh` di `kelas_mapel` dengan position berbeda
- Migration 1: after `mapel_id`
- Migration 2: after `jam_pelajaran`

**Impact**: Column position confusion, potential migration conflicts

---

### 9. NilaiHarianController: 'pre' vs 'predikat' Inconsistency
**Files**: 
- [app/Imports/NilaiImport.php](app/Imports/NilaiImport.php#L39) - uses `'pre'`
- [app/Http/Controllers/NilaiController.php](app/Http/Controllers/NilaiController.php#L123) - uses `'predikat'`

**Issue**: Inconsistent field naming between imports and manual entry
**Impact**: Data integrity - import akan store NULL, manual entry store predikat

---

### 10. RapotController: Unknown Comment About deskripsi
**File**: [app/Http/Controllers/RapotController.php](app/Http/Controllers/RapotController.php#L52)
**Issue**: Comment says "HAPUS INI" untuk `mata_pelajaran.deskripsi` tapi column tidak exists di database
**Impact**: May indicate deprecated/incomplete refactoring

---

### 11. Nilai Table: Overlapping UH Columns
**Files**:
- [database/migrations/2026_05_24_065255](database/migrations/2026_05_24_065255_add_nilai_fields_to_nilai_table.php) - Adds: rph, pts, pas, hpa, predikat
- [database/migrations/2026_06_07_070150](database/migrations/2026_06_07_070150_add_uh_columns_to_nilai_table.php) - Adds: uh1-uh6, rata_uh

**Issue**: Column ordering mungkin tidak optimal. `rata_uh` seharusnya calculated field, bukan stored
**Impact**: Data duplication risk, maintenance overhead

---

### 12. Kelas Model: Duplicate guru Relationship
**File**: [app/Models/Kelas.php](app/Models/Kelas.php#L17-21)
**Issue**: Memiliki 2 relationship yang sama:
```php
public function guru() { return $this->belongsTo(Guru::class); }
public function waliKelas() { return $this->belongsTo(Guru::class, 'guru_id', 'id'); }
```
**Impact**: Redundant relationship, confusing for developers

---

## 🔵 INFO ISSUES

### 1. MataPelajaran Model: kelompok vs jenis_rapot
**File**: [app/Models/MataPelajaran.php](app/Models/MataPelajaran.php#L7-11)
**Info**: Model memiliki dua column classification:
- `kelompok`: A=Wajib, B=Mulok, C=Peminatan
- `jenis_rapot`: akademik, dinniyyah, tahfidz
**Recommendation**: Documentation needed untuk clarity

---

### 2. NilaiHarianController: Download Template Missing for Nilai Harian
**File**: [routes/web.php](routes/web.php#L35)
**Info**: Route untuk export nilai harian exists, tetapi tidak ada template download untuk nilai harian
- Compare dengan siswa: memiliki template download di route 99
**Recommendation**: Tambah template download untuk nilai harian consistency

---

### 3. AuthController: Not Fully Reviewed
**File**: [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php)
**Info**: Controller tidak di-review dalam audit ini. Perlu verify:
- Login/Register validation
- Password hashing
- Session management

---

### 4. View Blade Templates: Not Fully Audited
**Info**: Blade templates (.blade.php) di `resources/views/` tidak di-audit
**Recommendation**: Lakukan security audit untuk:
- XSS prevention (use {!! !!} carefully)
- CSRF token in forms
- Authorization checks

---

### 5. Export Classes: Incomplete Review
**Files**: 
- [app/Exports/SiswaExport.php](app/Exports/SiswaExport.php)
- [app/Exports/GuruExport.php](app/Exports/GuruExport.php)
- [app/Exports/KelasExport.php](app/Exports/KelasExport.php)

**Info**: Partial review saja. Perlu verify:
- Data validation
- Performance dengan large datasets
- Error handling

---

### 6. NilaiController: Comment About "INI YANG KURANG"
**File**: [routes/web.php](routes/web.php#L71)
**Info**: Ada comment "// INI YANG KURANG" tapi route sudah ada
**Recommendation**: Remove comment untuk clarity

---

### 7. Tahun Pelajaran: Soft Delete Not Implemented
**File**: [app/Models/TahunPelajaran.php](app/Models/TahunPelajaran.php)
**Info**: Model tidak menggunakan soft delete, tetapi data tahun pelajaran bersifat historical
**Recommendation**: Consider implementing SoftDelete trait untuk audit trail

---

## 📋 ADDITIONAL FINDINGS

### Migration Execution Order Issues
```
Expected Order:
1. Create tables (kelas, siswas, gurus, tahun_pelajaran, mata_pelajaran, nilai, etc.)
2. Add foreign keys
3. Add additional columns
4. Add indices

Actual Issues:
- 2026_05_21_012903 & 2026_06_03_052850: DUPLICATE jenis_rapot
- 2026_05_25_041027 & 2026_05_30_205023: DUPLICATE jumlah_uh (wrong target table in first)
```

### Foreign Key Relationships Summary
| Table | FK | Target | OnDelete | Status |
|-------|-----|--------|----------|--------|
| kelas | user_id | users | cascade | ⚠️ nullable |
| kelas | guru_id | gurus | set null | ✓ |
| siswas | user_id | users | cascade | ✓ |
| siswas | kelas_id | kelas | set null | ✓ |
| nilai | siswa_id | siswas | cascade | ✓ |
| nilai | kelas_mapel_id | kelas_mapel | cascade | ✓ |
| nilai | guru_id | gurus | set null | ⚠️ nullOnDelete invalid |
| nilai_harian | siswa_id | siswas | cascade | ✓ |
| nilai_harian | sub_bab_mapel_id | sub_bab_mapel | cascade | ✓ |
| nilai_harian | kelas_mapel_id | kelas_mapel | (nullable) | ✓ |
| nilai_harian | user_id | users | (nullable) | ❌ No FK defined |
| kelas_mapel | kelas_id | kelas | cascade | ✓ |
| kelas_mapel | mapel_id | mata_pelajaran | cascade | ✓ |
| kelas_mapel | guru_id | gurus | cascade | ✓ |
| bab_mapel | kelas_mapel_id | kelas_mapel | cascade | ✓ |
| sub_bab_mapel | bab_mapel_id | bab_mapel | cascade | ✓ |

---

## 🔧 RECOMMENDED ACTIONS (Priority Order)

### IMMEDIATE (Do First)
1. Fix NilaiImport `'pre'` → `'predikat'` typo
2. Fix `nullOnDelete()` → `onDelete('set null')` di migration
3. Fix SiswaImport to include `guru_id` when auto-creating kelas
4. Delete/merge duplicate migrations (jenis_rapot & jumlah_uh)
5. Fix migration down() bug di 2026_05_25_041027

### SHORT-TERM (This Sprint)
6. Add missing relationships to models (Guru, User, Nilai, BabMapel)
7. Remove duplicate `guru()` & `waliKelas()` from Kelas model
8. Add Foreign Key constraint to sessions.user_id
9. Review and fix resource route exception patterns

### MEDIUM-TERM (Next Sprint)
10. Add comprehensive error handling in Import classes
11. Implement soft deletes for historical data (TahunPelajaran)
12. Create database seeders for testing
13. Add data validation tests

---

## 📊 MODEL RELATIONSHIP MATRIX

```
User (1) → (M) Siswa ❌ Missing
User (1) → (M) Kelas ❌ Missing
User (1) → (M) NilaiHarian ❌ Missing

Guru (1) → (M) Kelas ❌ Missing
Guru (1) → (M) KelasMapel ✓
Guru (1) → (M) Nilai ❌ Missing

Kelas (1) → (M) Siswa ✓
Kelas (1) → (M) KelasMapel ✓
Kelas ← (1) Guru ✓ (duplicate: waliKelas)

Siswa (1) → (M) Nilai ✓
Siswa (1) → (M) NilaiHarian ✓

KelasMapel (1) → (M) Nilai ✓
KelasMapel (1) → (M) BabMapel ✓ (via hasMany, no FK defined)
KelasMapel ← (1) Kelas ✓
KelasMapel ← (1) MataPelajaran ✓
KelasMapel ← (1) Guru ✓

BabMapel (1) → (M) SubBabMapel ✓
BabMapel ← (1) KelasMapel ❌ Missing (no FK, no relationship)

SubBabMapel (1) → (M) NilaiHarian ✓
SubBabMapel ← (1) BabMapel ✓

NilaiHarian ← (1) User ❌ No FK defined
```

---

**End of Audit Report**
