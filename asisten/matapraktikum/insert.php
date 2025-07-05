<?php
if (isset($_POST['tambah'])) {
    $kode = $_POST['kd_matakuliah'] ?? '';
    $nama = $_POST['nama_matakuliah'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $sks = $_POST['sks'] ?? '';

    if ($kode && $nama && $semester && $sks) {
        $stmt = $conn->prepare("INSERT INTO mata_praktikum (kd_matakuliah, nama_matakuliah, semester, sks) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $kode, $nama, $semester, $sks);
        $stmt->execute();
        $stmt->close();
    }
}
?>
