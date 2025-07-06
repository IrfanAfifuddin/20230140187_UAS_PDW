<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    echo "<script>alert('Akses ditolak'); location.href='../../login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['laporan_id'])) {
    $laporan_id = $_POST['laporan_id'];
    $action = $_POST['action'] ?? 'simpan';

    if ($action === 'batal') {
        $stmt = $conn->prepare("UPDATE laporan SET nilai = NULL, feedback = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $laporan_id);
    } else {
        $nilai = $_POST['nilai'] ?? null;
        $feedback = $_POST['feedback'] ?? null;

        $stmt = $conn->prepare("UPDATE laporan SET nilai = ?, feedback = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("isi", $nilai, $feedback, $laporan_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui'); location.href='../laporan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan'); location.href='../laporan.php';</script>";
    }
}
?>
