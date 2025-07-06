<?php
require_once '../../config.php';
session_start();

$mahasiswa_id = $_SESSION['user_id'] ?? null;

if (isset($_POST['update_laporan']) && $mahasiswa_id) {
    $id = $_POST['laporan_id'];
    $text = $_POST['text'];
    $praktikum_id = $_POST['praktikum_id'];

    // Cek apakah laporan sudah dinilai atau diberi feedback
    $cek = $conn->prepare("SELECT nilai, feedback FROM laporan WHERE id=? AND mahasiswa_id=?");
    $cek->bind_param("ii", $id, $mahasiswa_id);
    $cek->execute();
    $result = $cek->get_result();
    $laporan = $result->fetch_assoc();

    if (!empty($laporan['nilai']) || !empty($laporan['feedback'])) {
        echo "<script>alert('Laporan sudah dinilai dan tidak bisa diedit.');location.href='praktikum_detail.php?id=$praktikum_id';</script>";
        exit;
    }

    // Jika ada file baru diunggah, update semuanya
    if (!empty($_FILES['file']['tmp_name'])) {
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_data = file_get_contents($_FILES['file']['tmp_name']);

        $stmt = $conn->prepare("UPDATE laporan SET text=?, file_name=?, file_type=?, file_data=? WHERE id=? AND mahasiswa_id=?");
        $stmt->bind_param("ssssii", $text, $file_name, $file_type, $file_data, $id, $mahasiswa_id);
        $stmt->send_long_data(3, $file_data); // ini hanya efektif jika bind_param gunakan $file_data juga
    }
        else {
        // Jika tidak ada file baru, hanya update teks
        $stmt = $conn->prepare("UPDATE laporan SET text=? WHERE id=? AND mahasiswa_id=?");
        $stmt->bind_param("sii", $text, $id, $mahasiswa_id);
    }

    // Eksekusi dan redirect
    if ($stmt->execute()) {
        echo "<script>alert('Laporan berhasil diperbarui');location.href='praktikum_detail.php?id=$praktikum_id';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui laporan');location.href='praktikum_detail.php?id=$praktikum_id';</script>";
    }
}
?>
