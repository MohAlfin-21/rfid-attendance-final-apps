# Deploy PostgreSQL di Railway

Proyek ini sudah disiapkan untuk PostgreSQL. Konfigurasi database membaca `DATABASE_URL`, `DB_URL`, atau variabel `PGHOST`, `PGPORT`, `PGUSER`, `PGPASSWORD`, dan `PGDATABASE`.

## Environment Railway

Set variabel berikut pada App Service:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:isi_dari_php_artisan_key_generate
APP_URL=https://domain-railway-kamu.up.railway.app
LOG_CHANNEL=stderr
DB_CONNECTION=pgsql
DB_URL=${{Postgres.DATABASE_URL}}
DB_SSLMODE=prefer
SEED_DEMO_DATA=false
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
CACHE_STORE=database
```

Jika memakai database eksternal seperti Aiven, Neon, atau Supabase, isi `DB_URL` dengan connection string dari provider tersebut dan set `DB_SSLMODE=require` bila provider mewajibkan SSL.

Jika kamu juga membuat Worker Service sesuai panduan Railway Laravel, ganti `QUEUE_CONNECTION=database`. Untuk setup paling sederhana tanpa worker terpisah, `sync` lebih aman.

## Railway deploy

1. Tambahkan PostgreSQL service di Railway atau siapkan database eksternal.
   Jika pgAdmin sudah bisa terhubung ke database Railway, berarti database instance-nya sudah ada. Kamu tidak perlu membuat tabel manual di pgAdmin.
2. Pada App Service, gunakan build command `npm run build`.
3. Pada App Service, gunakan pre-deploy command:

```sh
chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh
```

4. Deploy ulang App Service.

## Migrasi dari lokal

Kalau ingin menjalankan migrasi ke database real dari laptop Windows/XAMPP:

1. Aktifkan extension PostgreSQL di `C:\xampp\php\php.ini`:
   - ubah `;extension=pdo_pgsql` menjadi `extension=pdo_pgsql`
   - ubah `;extension=pgsql` menjadi `extension=pgsql`
2. Restart Apache atau terminal PHP yang sedang dipakai.
3. Pastikan `.env` mengarah ke database Railway/public PostgreSQL yang benar.
4. Jalankan:

```sh
php artisan config:clear
php artisan migrate --force
```

Gunakan `php artisan db:seed` hanya saat memang ingin mengisi data referensi atau demo. Seeder demo tidak akan berjalan kecuali `SEED_DEMO_DATA=true`.

Catatan: fitur upload file absen masih memakai disk lokal Laravel. Filesystem container Railway bersifat sementara, jadi untuk pemakaian serius file upload perlu dipindah ke object storage seperti S3-compatible storage.
