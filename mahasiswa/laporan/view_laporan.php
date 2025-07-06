<?php
require_once '../../config.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("ID tidak valid.");
}

$stmt = $conn->prepare("SELECT file_name, file_type, file_data FROM laporan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

$stmt->bind_result($file_name, $file_type, $file_data);
$stmt->fetch();

if (!$file_data) {
    die("File tidak ditemukan.");
}

header("Content-Type: " . $file_type);
header("Content-Disposition: inline; filename=\"" . $file_name . "\"");
echo $file_data;
exit;
?>
