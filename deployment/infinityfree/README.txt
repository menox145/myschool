PAKET DEPLOYMENT INFINITYFREE

1. Buat database MySQL melalui Control Panel InfinityFree.
2. Buka phpMyAdmin untuk database tersebut.
3. Pilih tab Import lalu unggah file myschool.sql.
4. Edit file .env yang sudah berada di htdocs.
   Gunakan env.infinityfree.example sebagai contoh, tetapi pertahankan nilai
   APP_KEY dari file .env lokal.
5. Unggah isi folder htdocs dari paket ini ke folder htdocs hosting:
   - .htaccess ditempatkan langsung di htdocs
   - folder public/storage digabungkan dengan htdocs/public/storage
6. Unggah config/filesystems.php terbaru dari project lokal ke lokasi yang
   sama di hosting agar unggahan foto bekerja tanpa symbolic link.
7. Pastikan folder storage/framework dan storage/logs dapat ditulis.

Catatan:
- DB_HOST InfinityFree bukan 127.0.0.1 dan bukan localhost.
- Jangan mengimpor SQL ke database lain.
- Jangan mengaktifkan APP_DEBUG pada website publik.
