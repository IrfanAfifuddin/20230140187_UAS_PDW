<?php
if (isset($_POST['delete_user'])) {
    $id = $_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
?>
