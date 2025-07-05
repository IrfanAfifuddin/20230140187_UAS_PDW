<?php
require_once '../../config.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("ID tidak valid");
}

$stmt = $conn->prepare("SELECT file_data, file_type, file_name FROM modul WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    header("Content-Type: " . $row['file_type']);
    header("Content-Disposition: inline; filename=\"" . $row['file_name'] . "\"");
    echo $row['file_data'];
} else {
    echo "File tidak ditemukan.";
}
