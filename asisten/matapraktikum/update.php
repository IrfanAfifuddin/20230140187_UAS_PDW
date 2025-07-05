<?php
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kode = $_POST['kd_matakuliah'];
    $nama = $_POST['nama_matakuliah'];
    $semester = $_POST['semester'];
    $sks = $_POST['sks'];

    $stmt = $conn->prepare("UPDATE mata_praktikum SET kd_matakuliah=?, nama_matakuliah=?, semester=?, sks=? WHERE id=?");
    $stmt->bind_param("ssssi", $kode, $nama, $semester, $sks, $id);
    $stmt->execute();
}
?>
