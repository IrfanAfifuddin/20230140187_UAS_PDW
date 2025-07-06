<?php
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $nama, $email, $role, $id);
    $stmt->execute();
}
?>
