<?php
require_once '../../config.php';
session_start();

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID laporan tidak valid.");
}

$laporan_id = intval($_GET['id']);

// Ambil data file
$stmt = $conn->prepare("SELECT file_name, file_type, file_data FROM laporan WHERE id = ?");
$stmt->bind_param("i", $laporan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("File tidak ditemukan.");
}

$file = $result->fetch_assoc();

// Set header untuk membuka atau mengunduh file
header("Content-Type: " . $file['file_type']);
header("Content-Disposition: inline; filename=\"" . $file['file_name'] . "\"");
echo $file['file_data'];
exit;
