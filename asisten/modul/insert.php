<?php
if (isset($_POST['tambah_modul'])) {
    $praktikum_id = $_POST['praktikum_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    $file_data = null;
    $file_name = null;
    $file_type = null;

    if (!empty($_FILES['file']['tmp_name'])) {
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_data = file_get_contents($_FILES['file']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO modul (praktikum_id, judul, deskripsi, file_data, file_name, file_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issbss", $praktikum_id, $judul, $deskripsi, $null, $file_name, $file_type);
    
    $stmt->send_long_data(3, $file_data);

    if ($stmt->execute()) {
        header("Location: modul_detail.php?id=$praktikum_id");
        exit;
    } else {
        echo "<p class='text-red-500'>Gagal menambah modul.</p>";
    }
}
?>
