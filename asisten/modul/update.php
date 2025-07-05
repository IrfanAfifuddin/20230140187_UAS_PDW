<?php
if (isset($_POST['update_modul'])) {
    $id = $_POST['id'];
    $praktikum_id = $_POST['praktikum_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    $hasFile = !empty($_FILES['file']['tmp_name']);

    if ($hasFile) {
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_data = file_get_contents($_FILES['file']['tmp_name']);

        $stmt = $conn->prepare("UPDATE modul SET judul=?, deskripsi=?, file_data=?, file_name=?, file_type=? WHERE id=?");
        $stmt->bind_param("ssbssi", $judul, $deskripsi, $null, $file_name, $file_type, $id);
        $stmt->send_long_data(2, $file_data);
    } else {
        $stmt = $conn->prepare("UPDATE modul SET judul=?, deskripsi=? WHERE id=?");
        $stmt->bind_param("ssi", $judul, $deskripsi, $id);
    }

    if ($stmt->execute()) {
        header("Location: modul_detail.php?id=$praktikum_id");
        exit;
    } else {
        echo "<p class='text-red-500'>Gagal memperbarui data modul.</p>";
    }
}
?>
