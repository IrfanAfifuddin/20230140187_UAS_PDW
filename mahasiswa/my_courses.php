<?php
require_once '../config.php';
session_start();

$pageTitle = 'My Course';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';

// Cek login mahasiswa
$mahasiswa_id = $_SESSION['user_id'] ?? null;
if (!$mahasiswa_id || $_SESSION['role'] !== 'mahasiswa') {
    echo "<p class='text-red-500'>Silakan login sebagai mahasiswa.</p>";
    require_once 'templates/footer_mahasiswa.php';
    exit;
}

// Ambil mata kuliah yang sudah diikuti
$stmt = $conn->prepare("
    SELECT mp.id, mp.nama_matakuliah, mp.semester, mp.sks 
    FROM krs k
    JOIN mata_praktikum mp ON k.praktikum_id = mp.id
    WHERE k.user_id = ?
");
$stmt->bind_param("i", $mahasiswa_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="max-w-6xl mx-auto py-6">
    <h1 class="text-2xl font-semibold mb-4">Praktikum Saya</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-white p-4 shadow rounded">
                    <h2 class="text-xl font-bold"><?= htmlspecialchars($row['nama_matakuliah']) ?></h2>
                    <p class="text-sm text-gray-600">Semester: <?= $row['semester'] ?> | SKS: <?= $row['sks'] ?></p>
                    <a href="laporan/praktikum_detail.php?id=<?= $row['id'] ?>" class="inline-block mt-3 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        Lihat Detail
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600">Belum ada praktikum yang diikuti.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
