<?php
if (isset($_POST['delete_modul'])) {
    $id = $_POST['delete_id'];
    $praktikum_id = $_POST['praktikum_id'];

    $stmt = $conn->prepare("DELETE FROM modul WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: modul_detail.php?id=$praktikum_id");
    exit;
}
