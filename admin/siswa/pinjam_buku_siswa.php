<?php
require_once __DIR__ . '/../../inc/helper_denda.php';

$id_siswa = $_SESSION["ses_username"];
$nama_siswa = $_SESSION["ses_nama"];

// Auto generate id sirkulasi
$carikode = $koneksi->query("SELECT id_sk FROM tb_sirkulasi ORDER BY id_sk DESC LIMIT 1");
$datakode = $carikode->fetch_assoc();
if ($datakode && $datakode['id_sk']) {
    $kode = $datakode['id_sk'];
} else {
    $kode = 'S000';
}
$urut = (int)substr($kode, 1, 3);
$tambah = $urut + 1;
$format = 'S' . str_pad($tambah, 3, '0', STR_PAD_LEFT);

// Ambil daftar buku dengan stok > 0
$sql_buku = $koneksi->query("SELECT * FROM tb_buku WHERE stok > 0 ORDER BY judul_buku ASC");

// Cek status akun dan denda siswa untuk ditampilkan di form
$status_siswa = cekStatusSiswa($id_siswa, $koneksi);
$denda_siswa = cekDendaSiswa($id_siswa, $koneksi);
$siswa_bisa_pinjam = ($status_siswa['status'] == 'AKTIF' && !$denda_siswa['ada_denda']);
?>

<section class="content-header">
    <h1>
        Peminjaman Buku
        <small style="color: #0073b7; font-weight: 500;">Pinjam buku dari perpustakaan</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Peminjaman</h3>
                </div>

                <?php if ($status_siswa['status'] == 'NONAKTIF'): ?>
                    <div class="box-body">
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-ban"></i> Akun Anda NONAKTIF</h4>
                            <p><strong>Alasan:</strong> <?php echo $status_siswa['alasan']; ?></p>
                            <p>Anda <strong>TIDAK DAPAT</strong> melakukan peminjaman saat ini. Silahkan hubungi admin untuk mengaktifkan kembali akun Anda.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($denda_siswa['ada_denda']): ?>
                    <div class="box-body">
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-money"></i> Denda Terlambat</h4>
                            <p><strong>Total Denda:</strong> Rp <?php echo number_format($denda_siswa['jumlah_belum_dibayar'], 0, ',', '.'); ?></p>
                            <p>Anda <strong>TIDAK DAPAT</strong> melakukan peminjaman baru sampai denda ini dibayar.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="box-body">
                        
                        <div class="form-group">
                            <label>ID Peminjaman</label>
                            <input type="text" name="id_sk" value="<?php echo $format; ?>" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>NIS / ID Siswa</label>
                            <input type="text" value="<?php echo $id_siswa; ?>" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" value="<?php echo $nama_siswa; ?>" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Buku <span class="text-danger">*</span></label>
                            <select name="id_buku" id="id_buku" class="form-control select2" style="width: 100%;" required onchange="updateStokInfo()">
                                <option value="">-- Pilih Buku --</option>
                                <?php 
                                while ($buku = $sql_buku->fetch_assoc()) {
                                    if ($buku['stok'] > 5) {
                                        $stok_class = 'text-success';
                                    } else {
                                        $stok_class = 'text-warning';
                                    }
                                    $kategori = isset($buku['kategori']) ? $buku['kategori'] : 'Pelajaran';
                                ?>
                                    <?php echo '<option value="' . $buku['id_buku'] . '" '; ?>
                                    <?php echo 'data-stok="' . $buku['stok'] . '" '; ?>
                                    <?php echo 'data-judul="' . $buku['judul_buku'] . '" '; ?>
                                    <?php echo 'data-pengarang="' . $buku['pengarang'] . '" '; ?>
                                    <?php echo 'data-kategori="' . $kategori . '">'; ?>
                                        <?php echo $buku['id_buku'] . ' - ' . $buku['judul_buku'] . ' '; ?>
                                        <?php echo '<span class="' . $stok_class . '">(Stok: ' . $buku['stok'] . ')</span>'; ?>
                                    <?php echo '</option>'; ?>
                                <?php } ?>
                            </select>
                        </div>

                        <div id="stok_info" class="alert alert-info" style="display: none;">
                            <strong>Informasi Buku:</strong><br>
                            <span id="kategori_info"></span><br>
                            <span id="judul_info"></span><br>
                            <span id="pengarang_info"></span><br>
                            <span id="stok_display" style="color: green; font-weight: bold;"></span>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Buku <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" max="99" value="1" required onchange="validateJumlah()">
                            <small id="jumlah_info" class="form-text text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-control" required 
                                   value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="alert alert-warning">
                            <strong>⚠️ Informasi Peminjaman:</strong>
                            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                                <li>Anda bisa meminjam <strong>1-99 buku</strong> sekaligus (sesuai ketersediaan stok)</li>
                                <li>Durasi peminjaman: <strong>7 hari</strong> untuk setiap buku yang dipinjam</li>
                                <li>Tanggal pengembalian otomatis: <strong id="tgl_kembali_auto">-</strong></li>
                                <li>Denda keterlambatan: Sesuai ketentuan sekolah</li>
                            </ul>
                        </div>

                    </div>
                    <div class="box-footer">
                        <?php if ($siswa_bisa_pinjam): ?>
                            <button type="submit" name="btnPinjam" class="btn btn-primary" id="btnPinjam">
                                <i class="fa fa-check"></i> Pinjam Buku
                            </button>
                        <?php else: ?>
                            <button type="submit" name="btnPinjam" class="btn btn-danger" id="btnPinjam" disabled>
                                <i class="fa fa-ban"></i> Pinjam Buku (Akun Tidak Aktif)
                            </button>
                        <?php endif; ?>
                        <a href="?page=siswa/dashboard_siswa" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Panduan Peminjaman</h3>
                </div>
                <div class="box-body">
                    <p><strong>📖 Cara Meminjam:</strong></p>
                    <ol>
                        <li>Pilih buku yang ingin dipinjam</li>
                        <li>Masukkan jumlah buku (1-99 sesuai stok)</li>
                        <li>Pastikan stok tersedia</li>
                        <li>Pilih tanggal pinjam</li>
                        <li>Klik tombol "Pinjam Buku"</li>
                    </ol>

                    <hr>

                    <p><strong>📋 Ketentuan:</strong></p>
                    <ul>
                        <li>Durasi pinjam: 7 hari</li>
                        <li>Maksimal pinjam: sesuai kebijakan sekolah</li>
                        <li>Harus dikembalikan tepat waktu</li>
                        <li>Jika terlambat akan dikenakan denda</li>
                    </ul>

                    <hr>

                    <p><strong>✓ Buku Sedang Dipinjam:</strong></p>
                    <div id="buku_dipinjam">
                        <p style="color: #999; text-align: center; font-size: 12px;">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Update stok info saat select buku
function updateStokInfo() {
    var select = document.getElementById('id_buku');
    var option = select.options[select.selectedIndex];
    
    if (option.value === '') {
        document.getElementById('stok_info').style.display = 'none';
        document.getElementById('btnPinjam').disabled = false;
        document.getElementById('jumlah').max = 1;
        return;
    }

    var stok = parseInt(option.getAttribute('data-stok'));
    var judul = option.getAttribute('data-judul');
    var pengarang = option.getAttribute('data-pengarang');
    var kategori = option.getAttribute('data-kategori') || 'Pelajaran';

    // Format kategori display dengan badge
    var kategori_display = '<span class="label label-info">' + kategori + '</span>';
    
    document.getElementById('kategori_info').innerHTML = 'Kategori: ' + kategori_display;
    document.getElementById('judul_info').textContent = 'Judul: ' + judul;
    document.getElementById('pengarang_info').textContent = 'Pengarang: ' + pengarang;
    document.getElementById('stok_display').textContent = '✓ Stok Tersedia: ' + stok + ' buku';
    document.getElementById('stok_info').style.display = 'block';
    
    // Set max untuk jumlah input
    document.getElementById('jumlah').max = stok;
    document.getElementById('jumlah').value = 1;

    // Disable jika stok habis
    if (stok <= 0) {
        document.getElementById('btnPinjam').disabled = true;
        document.getElementById('stok_display').className = 'text-danger';
        document.getElementById('stok_display').textContent = '✗ Stok Habis!';
    } else {
        document.getElementById('btnPinjam').disabled = false;
        document.getElementById('stok_display').className = 'text-success';
    }

    validateJumlah();
    updateTglKembali();
}

// Validate jumlah yang dipinjam
function validateJumlah() {
    var jumlah = parseInt(document.getElementById('jumlah').value);
    var select = document.getElementById('id_buku');
    var option = select.options[select.selectedIndex];
    
    if (option.value === '') {
        document.getElementById('jumlah_info').innerHTML = '';
        return;
    }
    
    var stok = parseInt(option.getAttribute('data-stok'));
    var jumlahInfo = document.getElementById('jumlah_info');
    
    if (jumlah > stok) {
        jumlahInfo.innerHTML = '<strong style="color: red;">✗ Jumlah melebihi stok! Maksimal: ' + stok + '</strong>';
        document.getElementById('btnPinjam').disabled = true;
    } else if (jumlah > 0) {
        jumlahInfo.innerHTML = '<strong style="color: green;">✓ Akan meminjam ' + jumlah + ' buku</strong>';
        document.getElementById('btnPinjam').disabled = false;
    }
}

// Update tanggal kembali otomatis
function updateTglKembali() {
    var tgl_pinjam = document.getElementById('tgl_pinjam').value;
    if (tgl_pinjam) {
        var date = new Date(tgl_pinjam);
        date.setDate(date.getDate() + 7);
        var tgl_kembali = date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('tgl_kembali_auto').textContent = tgl_kembali;
    }
}

// Update tanggal kembali saat tanggal pinjam berubah
document.getElementById('tgl_pinjam').addEventListener('change', updateTglKembali);

// Validate jumlah saat input berubah
document.getElementById('jumlah').addEventListener('change', validateJumlah);
document.getElementById('jumlah').addEventListener('input', validateJumlah);

// Load buku yang sedang dipinjam
function loadBukuDipinjam() {
    $.ajax({
        url: 'admin/siswa/get_pinjaman_siswa.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success && data.count > 0) {
                var html = '<ul style="list-style: none; padding: 0; margin: 0;">';
                data.data.forEach(function(item) {
                    html += '<li style="padding: 8px; border-bottom: 1px solid #eee;"><strong>' + item.judul_buku + '</strong><br><small>Kembali: ' + item.tgl_kembali + '</small></li>';
                });
                html += '</ul>';
                document.getElementById('buku_dipinjam').innerHTML = html;
            } else {
                document.getElementById('buku_dipinjam').innerHTML = '<p style="color: #999; text-align: center; font-size: 12px;">Anda tidak sedang meminjam buku</p>';
            }
        },
        error: function() {
            document.getElementById('buku_dipinjam').innerHTML = '<p style="color: red; font-size: 12px;">Error loading data</p>';
        }
    });
}

// Load on page load
$(document).ready(function() {
    updateTglKembali();
    loadBukuDipinjam();
});
</script>

<?php
// Process peminjaman
if (isset($_POST['btnPinjam'])) {
    $id_buku = $_POST['id_buku'];
    $jumlah = (int)$_POST['jumlah'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = date('Y-m-d', strtotime('+7 days', strtotime($tgl_pinjam)));

    // VALIDASI 1: Cek status akun siswa
    $status_siswa = cekStatusSiswa($id_siswa, $koneksi);
    if ($status_siswa['status'] == 'NONAKTIF') {
        echo "<script>
        Swal.fire({title: 'Peminjaman Ditolak', text: 'Akun Anda sedang NONAKTIF.\n\nAlasan: " . $status_siswa['alasan'] . "\n\nSilahkan hubungi admin untuk mengaktifkan kembali akun Anda atau melakukan pembayaran denda.', icon: 'error', confirmButtonText: 'OK'})
        </script>";
        exit;
    }

    // VALIDASI 2: Cek denda terlambat yang belum dibayar
    $denda_siswa = cekDendaSiswa($id_siswa, $koneksi);
    if ($denda_siswa['ada_denda']) {
        $total_denda = $denda_siswa['jumlah_belum_dibayar'];
        echo "<script>
        Swal.fire({title: 'Peminjaman Ditolak', text: 'Anda memiliki denda terlambat yang belum dibayar.\n\nTotal Denda: Rp " . number_format($total_denda, 0, ',', '.') . "\n\nSilahkan bayar denda terlebih dahulu sebelum meminjam buku lagi.', icon: 'error', confirmButtonText: 'OK'})
        </script>";
        exit;
    }

    // Validasi jumlah
    if ($jumlah <= 0 || $jumlah > 99) {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Jumlah buku tidak valid',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        // Cek stok sebelum pinjam
        $cek_stok = $koneksi->query("SELECT stok FROM tb_buku WHERE id_buku='$id_buku'");
        $row_stok = $cek_stok->fetch_assoc();

        if (!$row_stok || $row_stok['stok'] < $jumlah) {
            $stok_tersedia = (!$row_stok) ? 0 : $row_stok['stok'];
            echo "<script>
                Swal.fire({
                    title: 'Peminjaman Gagal',
                    text: 'Stok buku tidak cukup. Stok tersedia: " . $stok_tersedia . " buku',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        location.reload();
                    }
                });
            </script>";
        } else {
            // Get last id_sk untuk generate ID untuk multiple buku
            $carikode = $koneksi->query("SELECT id_sk FROM tb_sirkulasi ORDER BY id_sk DESC LIMIT 1");
            $datakode = $carikode->fetch_assoc();
            if ($datakode && $datakode['id_sk']) {
                $kode = $datakode['id_sk'];
            } else {
                $kode = 'S000';
            }
            $urut = (int)substr($kode, 1, 3);
            $tambah = $urut + 1;
            $id_sk_new = 'S' . str_pad($tambah, 3, '0', STR_PAD_LEFT);
            
            // OPSI 2: 1 SK = 1 Transaksi, berapapun qty
            // Insert peminjaman sekali saja (Master Transaksi)
            $sql_sk = "INSERT INTO tb_sirkulasi (id_sk, id_buku, id_anggota, tgl_pinjam, status, tgl_kembali) 
                       VALUES ('$id_sk_new', '$id_buku', '$id_siswa', '$tgl_pinjam', 'PIN', '$tgl_kembali')";
            
            // Insert detail buku dengan quantity
            $sql_detail = "INSERT INTO tb_sirkulasi_detail (id_sk, id_buku, jumlah, status) 
                          VALUES ('$id_sk_new', '$id_buku', $jumlah, 'PIN')";
            
            // Log peminjaman
            $sql_log = "INSERT INTO log_pinjam (id_buku, id_anggota, tgl_pinjam) 
                        VALUES ('$id_buku', '$id_siswa', '$tgl_pinjam')";
            
            // Log aktivitas peminjaman ke tb_kunjungan
            $nama_siswa = $_SESSION["ses_nama"];
            $sql_aktivitas = "INSERT INTO tb_kunjungan (id_anggota, nama, level, tgl_kunjungan, waktu_kunjungan, jenis_aktivitas, id_buku, id_sk, keterangan) 
                             VALUES ('$id_siswa', '$nama_siswa', 'Siswa', DATE(NOW()), TIME(NOW()), 'Peminjaman', '$id_buku', '$id_sk_new', 'Self-service $jumlah buku(s)')";
            
            // Execute semua query
            if ($koneksi->query($sql_sk) && $koneksi->query($sql_detail) && $koneksi->query($sql_log) && $koneksi->query($sql_aktivitas)) {
                // Decrement stok sebanyak jumlah yang dipinjam
                $koneksi->query("UPDATE tb_buku SET stok = stok - $jumlah WHERE id_buku='$id_buku'");
                
                echo "<script>
                    Swal.fire({
                        title: 'Peminjaman Berhasil!',
                        text: 'Berhasil meminjam $jumlah buku. Tanggal pengembalian: $tgl_kembali',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.value) {
                                window.location = '?page=siswa/riwayat_pinjam_siswa';
                            }
                        });
                    </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        title: 'Peminjaman Gagal',
                        text: 'Terjadi kesalahan saat memproses peminjaman. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
        }
    }
}
?>
