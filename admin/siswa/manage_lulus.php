<?php
/**
 * ============================================
 * ADMIN SISWA - MANAGE LULUS/NONAKTIF
 * ============================================
 * File: /admin/siswa/manage_lulus.php
 * 
 * Fitur:
 * 1. View list siswa aktif
 * 2. View list siswa lulus/nonaktif
 * 3. Luluskan siswa
 * 4. Restore siswa
 * 5. Soft delete siswa
 * 6. Reuse ID statistics
 */

include "../../inc/koneksi.php";
include "../../inc/helper_id_siswa.php";

// ==== SESSION CHECK ====
session_start();
if (!isset($_SESSION['ses_username']) || $_SESSION['ses_role'] != 'Admin') {
    header("Location: ../../login.php");
    exit;
}

// ==== PROSES ACTION ====
$action = $_GET['action'] ?? '';
$id_siswa = $_POST['id_anggota'] ?? $_GET['id_anggota'] ?? '';
$response = ['success' => false, 'message' => ''];

if ($action == 'lulus' && $id_siswa) {
    $result = luluskanSiswa($koneksi, $id_siswa, $_SESSION['ses_username']);
    $response = $result;
    $action_done = true;
} elseif ($action == 'nonaktif' && $id_siswa) {
    $alasan = $_POST['alasan'] ?? '';
    $result = nonaktifkanSiswa($koneksi, $id_siswa, $alasan, $_SESSION['ses_username']);
    $response = $result;
    $action_done = true;
} elseif ($action == 'restore' && $id_siswa) {
    $result = restoreSiswa($koneksi, $id_siswa, $_SESSION['ses_username']);
    $response = $result;
    $action_done = true;
}

// Redirect untuk GET action
if (isset($action_done)) {
    if ($response['success']) {
        header("Location: manage_lulus.php?tab=aktif&success=" . urlencode($response['message']));
    } else {
        header("Location: manage_lulus.php?tab=aktif&error=" . urlencode($response['message']));
    }
    exit;
}

// ==== GET DATA ====
$tab = $_GET['tab'] ?? 'aktif';
$stat = getStatistikID($koneksi);
$siswa_bebas = getSiswaBebasID($koneksi);

// Query siswa aktif
$query_aktif = "SELECT * FROM tb_anggota WHERE status = 'AKTIF' ORDER BY id_anggota";
$result_aktif = mysqli_query($koneksi, $query_aktif);

// Query siswa lulus/nonaktif
$query_lulus = "SELECT * FROM tb_anggota WHERE status IN ('LULUS', 'PINDAH', 'NONAKTIF') ORDER BY tgl_lulus DESC, tgl_nonaktif DESC";
$result_lulus = mysqli_query($koneksi, $query_lulus);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Manage Siswa Lulus | Perpus Pelita</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/css/dataTables.bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <style>
        .card-stat {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .card-stat h4 {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .card-stat .number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .card-stat .percentage {
            font-size: 12px;
            opacity: 0.8;
        }
        .nav-tabs {
            border-bottom: 2px solid #ddd;
        }
        .nav-tabs > li > a {
            border: none;
            color: #333;
            font-weight: 500;
        }
        .nav-tabs > li.active > a {
            background-color: transparent;
            border-bottom: 3px solid #0066cc;
            color: #0066cc;
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .btn-action {
            padding: 4px 8px;
            font-size: 12px;
            margin-right: 3px;
        }
    </style>
</head>
<body class="skin-blue sidebar-mini">
<div class="wrapper">
    <?php include "../../_inc/sidebar.php"; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>Manajemen Siswa (Lulus/Nonaktif)</h1>
        </section>

        <section class="content">
            <!-- Alert Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="fa fa-times-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card-stat">
                        <h4>Total Siswa</h4>
                        <div class="number"><?php echo number_format($stat['total_siswa']); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-stat" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h4>Siswa Aktif</h4>
                        <div class="number"><?php echo number_format($stat['aktif']); ?></div>
                        <div class="percentage"><?php echo $stat['persentase_aktif']; ?>% dari total</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-stat" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <h4>Siswa Lulus</h4>
                        <div class="number"><?php echo number_format($stat['lulus']); ?></div>
                        <div class="percentage">ID tersedia untuk reuse</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-stat" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <h4>Kapasitas Tersisa</h4>
                        <div class="number"><?php echo number_format($stat['sisa_kapasitas']); ?>/9999</div>
                        <div class="percentage">Dari max 9999 ID</div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Data Siswa</h3>
                </div>
                <div class="box-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="<?php echo ($tab == 'aktif' ? 'active' : ''); ?>">
                            <a href="#tab-aktif" data-toggle="tab">Siswa Aktif (<?php echo $stat['aktif']; ?>)</a>
                        </li>
                        <li role="presentation" class="<?php echo ($tab == 'lulus' ? 'active' : ''); ?>">
                            <a href="#tab-lulus" data-toggle="tab">Siswa Lulus/Nonaktif (<?php echo $stat['lulus'] + $stat['nonaktif']; ?>)</a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" style="padding-top: 20px;">
                        <!-- Siswa Aktif Tab -->
                        <div role="tabpanel" class="tab-pane <?php echo ($tab == 'aktif' ? 'active' : ''); ?>" id="tab-aktif">
                            <table class="table table-hover" id="table-aktif">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Anggota</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result_aktif)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['id_anggota']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['nama_anggota']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['jekel']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['alamat'], 0, 30)); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-action" onclick="showLulusModal('<?php echo $row['id_anggota']; ?>', '<?php echo $row['nama_anggota']; ?>')">
                                                <i class="fa fa-graduation-cap"></i> Lulus
                                            </button>
                                            <button class="btn btn-danger btn-action" onclick="showNonaktifModal('<?php echo $row['id_anggota']; ?>', '<?php echo $row['nama_anggota']; ?>')">
                                                <i class="fa fa-ban"></i> Nonaktif
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Siswa Lulus Tab -->
                        <div role="tabpanel" class="tab-pane <?php echo ($tab == 'lulus' ? 'active' : ''); ?>" id="tab-lulus">
                            <table class="table table-hover" id="table-lulus">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Anggota</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>Tgl Lulus</th>
                                        <th>Alasan Nonaktif</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result_lulus)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['id_anggota']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['nama_anggota']); ?></td>
                                        <td>
                                            <span class="label label-<?php echo $row['status'] == 'LULUS' ? 'success' : 'danger'; ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['tgl_lulus'] ? date('d/m/Y', strtotime($row['tgl_lulus'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($row['alasan_nonaktif'] ?? '-'); ?></td>
                                        <td>
                                            <button class="btn btn-info btn-action" onclick="restoreSiswa('<?php echo $row['id_anggota']; ?>', '<?php echo $row['nama_anggota']; ?>')">
                                                <i class="fa fa-undo"></i> Restore
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ID Reuse Info -->
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Informasi ID yang Tersedia untuk Reuse</h3>
                </div>
                <div class="box-body">
                    <p>ID-ID berikut ini dapat di-reuse ketika siswa baru mendaftar:</p>
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Anggota</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th>Tanggal Lulus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($siswa_bebas as $siswa): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><code><?php echo htmlspecialchars($siswa['id_anggota']); ?></code></td>
                                <td><?php echo htmlspecialchars($siswa['nama_anggota']); ?></td>
                                <td><span class="label label-<?php echo $siswa['status'] == 'LULUS' ? 'success' : 'danger'; ?>"><?php echo $siswa['status']; ?></span></td>
                                <td><?php echo $siswa['tgl_lulus'] ? date('d/m/Y', strtotime($siswa['tgl_lulus'])) : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (empty($siswa_bebas)): ?>
                        <p class="text-muted">Tidak ada siswa yang tersedia untuk reuse ID.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal Lulus -->
<div class="modal fade" id="modalLulus" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_lulus.php?action=lulus" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Luluskan Siswa</h4>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin meluluskan siswa berikut?</p>
                    <p><strong id="modal-lulus-nama"></strong></p>
                    <input type="hidden" id="modal-lulus-id" name="id_anggota">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Ya, Lulus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nonaktif -->
<div class="modal fade" id="modalNonaktif" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_lulus.php?action=nonaktif" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Non-aktifkan Siswa</h4>
                </div>
                <div class="modal-body">
                    <p>Non-aktifkan siswa: <strong id="modal-nonaktif-nama"></strong></p>
                    <div class="form-group">
                        <label>Alasan:</label>
                        <textarea class="form-control" name="alasan" rows="3" placeholder="Contoh: Pindah sekolah, Keluar, dll"></textarea>
                    </div>
                    <input type="hidden" id="modal-nonaktif-id" name="id_anggota">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Non-aktifkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Restore -->
<div class="modal fade" id="modalRestore" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_lulus.php?action=restore" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Restore Siswa</h4>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin me-restore siswa berikut ke status AKTIF?</p>
                    <p><strong id="modal-restore-nama"></strong></p>
                    <input type="hidden" id="modal-restore-id" name="id_anggota">
                </div>
                <div class="modal-footer">
                    <button type="button" class="close" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Ya, Restore</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../plugins/jQuery/jquery-1.12.4.min.js"></script>
<script src="../../bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/dataTables.bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#table-aktif, #table-lulus').DataTable({
        searching: true,
        paging: true,
        pageLength: 20,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian.json'
        }
    });
});

function showLulusModal(id, nama) {
    $('#modal-lulus-id').val(id);
    $('#modal-lulus-nama').text(nama);
    $('#modalLulus').modal('show');
}

function showNonaktifModal(id, nama) {
    $('#modal-nonaktif-id').val(id);
    $('#modal-nonaktif-nama').text(nama);
    $('#modalNonaktif').modal('show');
}

function restoreSiswa(id, nama) {
    if (confirm('Restore siswa ' + nama + ' ke status AKTIF?')) {
        window.location.href = 'manage_lulus.php?action=restore&id_anggota=' + id;
    }
}
</script>
</body>
</html>
