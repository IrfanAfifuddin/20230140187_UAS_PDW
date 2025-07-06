<?php
require_once '../../config.php';

if (isset($_POST['delete_laporan'])) {
    $laporan_id = $_POST['laporan_id'];
    $praktikum_id = $_POST['praktikum_id'];

    // Pastikan ID valid
    if (is_numeric($laporan_id)) {
        $stmt = $conn->prepare("DELETE FROM laporan WHERE id = ?");
        $stmt->bind_param("i", $laporan_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect kembali
    echo "<script>alert('Laporan berhasil dihapus!'); location.href='praktikum_detail.php?id=$praktikum_id';</script>";
    exit;
}
?>
