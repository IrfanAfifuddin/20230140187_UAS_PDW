<?php
if (isset($_POST['tambah_user'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $password, $role);
    $stmt->execute();
}
?>
