# Catatan untuk Dipikirkan: Ruangan

Bagian `ruangan` sudah berjalan, tetapi masih ada beberapa keputusan desain yang sebaiknya dipikirkan lebih matang sebelum sistem dianggap final.

## Kondisi saat ini

- `bidang` menjadi master data tersendiri:
  - `nama_bidang`
- `ruangan` menyimpan:
  - `kode_ruangan`
  - `nama_ruangan`
  - `lantai`
- Relasi bidang dan ruangan dicatat lewat `bidang_ruangan`:
  - `bidang_id`
  - `ruangan_id`
- `barang` menyimpan:
  - `bidang_id` sebagai bidang penanggung jawab
  - `ruangan_id` sebagai lokasi fisik aktif
- Riwayat perpindahan lokasi dicatat lewat `mutasi_barang` dengan:
  - `ruangan_asal_id`
  - `ruangan_tujuan_id`
- `ruangan` memakai soft delete, sementara relasi dari `barang` dan `mutasi_barang` memakai pembatasan delete.

## Hal yang perlu dipikirkan nanti

1. Bagaimana aturan jika penanggung jawab barang berpindah bidang: cukup edit `barang.bidang_id`, atau perlu riwayat serah terima?
2. Bagaimana aturan untuk ruangan yang sudah tidak aktif tetapi masih memiliki barang atau riwayat mutasi?
3. Apakah `lantai` perlu tetap berupa teks bebas, atau sebaiknya distandarkan?
4. Apakah pimpinan nanti membutuhkan laporan berdasarkan:
   - ruangan,
   - bidang,
   - atau keduanya?

## Arah sementara yang masih masuk akal

Untuk versi sekarang, desain yang dipakai:

- `barang.ruangan_id` = lokasi barang saat ini
- `barang.bidang_id` = bidang penanggung jawab barang
- `mutasi_barang` = riwayat perpindahan barang
- `bidang_ruangan` = pemetaan banyak-ke-banyak antara bidang dan ruangan

Desain ini menjaga ruangan sebagai lokasi fisik, sekaligus membuat laporan per bidang tetap aman karena nilai aset dihitung dari penanggung jawab barang, bukan dari ruangan bersama.
