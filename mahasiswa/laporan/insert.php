<?php
require_once '../../config.php';
session_start();

$mahasiswa_id = $_SESSION['user_id'] ?? null;

if (isset($_POST['submit_laporan']) && $mahasiswa_id) {
    $modul_id = $_POST['modul_id'];
    $praktikum_id = $_POST['praktikum_id'];
    $text = $_POST['text'] ?? '';

    // Pastikan file diunggah
    if (!empty($_FILES['file']['tmp_name'])) {
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_data = file_get_contents($_FILES['file']['tmp_name']);
        $null = null;

        // Cek apakah laporan sudah pernah dikumpulkan
        $cek = $conn->prepare("SELECT id, nilai, feedback FROM laporan WHERE mahasiswa_id=? AND praktikum_id=? AND modul_id=?");
        $cek->bind_param("iii", $mahasiswa_id, $praktikum_id, $modul_id);
        $cek->execute();
        $result = $cek->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Tidak bisa ubah jika sudah dinilai/feedback
            if (!empty($row['nilai']) || !empty($row['feedback'])) {
                echo "<script>alert('Laporan sudah dinilai dan tidak bisa diedit.');location.href='praktikum_detail.php?id=$praktikum_id';</script>";
                exit;
            }

            // Update laporan yang sudah ada
            $update = $conn->prepare("UPDATE laporan SET text=?, file_name=?, file_type=?, file_data=? WHERE id=?");
            $update->bind_param("ssssi", $text, $file_name, $file_type, $null, $row['id']);
            $update->send_long_data(3, $file_data);
            $update->execute();
        } else {
            // Insert baru
            $insert = $conn->prepare("INSERT INTO laporan (mahasiswa_id, praktikum_id, modul_id, text, file_name, file_type, file_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("iiisssb", $mahasiswa_id, $praktikum_id, $modul_id, $text, $file_name, $file_type, $null);
            $insert->send_long_data(6, $file_data);
            $insert->execute();
        }

        echo "<script>alert('Laporan berhasil dikumpulkan!');location.href='praktikum_detail.php?id=$praktikum_id';</script>";
    } else {
        echo "<script>alert('File tidak boleh kosong.');location.href='praktikum_detail.php?id=$praktikum_id';</script>";
    }
}
?>
