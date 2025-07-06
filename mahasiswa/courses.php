<?php
require_once '../config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$isMahasiswa = ($_SESSION['role'] ?? '') === 'mahasiswa';

if (isset($_POST['daftar']) && $isMahasiswa) {
    $praktikum_id = $_POST['praktikum_id'];

    $cekStmt = $conn->prepare("SELECT * FROM krs WHERE praktikum_id = ? AND user_id = ?");
    $cekStmt->bind_param("ii", $praktikum_id, $user_id);
    $cekStmt->execute();
    $cekResult = $cekStmt->get_result();

    if ($cekResult->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO krs (praktikum_id, user_id) VALUES (?, ?)");
        $insert->bind_param("ii", $praktikum_id, $user_id);
        $insert->execute();
    }

    header("Location: courses.php");
    exit;
}

// -- Mulai Output
$pageTitle = 'Katalog Praktikum';
$activePage = 'courses';
require_once 'templates/header_mahasiswa.php';

// Handle pencarian
$keyword = $_GET['search'] ?? '';
$query = "SELECT * FROM mata_praktikum WHERE nama_matakuliah LIKE ?";
$stmt = $conn->prepare($query);
$searchTerm = "%" . $keyword . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$praktikumResult = $stmt->get_result();

// Ambil daftar praktikum yang sudah diambil user
$krsList = [];
if ($user_id) {
    $krsStmt = $conn->prepare("SELECT praktikum_id FROM krs WHERE user_id = ?");
    $krsStmt->bind_param("i", $user_id);
    $krsStmt->execute();
    $krsResult = $krsStmt->get_result();
    while ($row = $krsResult->fetch_assoc()) {
        $krsList[] = $row['praktikum_id'];
    }
}
?>

<div class="max-w-6xl mx-auto py-6">
    <h1 class="text-2xl font-semibold mb-4">Daftar Mata Kuliah Praktikum</h1>

    <!-- Search Bar -->
    <form method="GET" class="mb-6">
        <input type="text" name="search" placeholder="Cari mata kuliah..." value="<?= htmlspecialchars($keyword) ?>" class="border p-2 rounded w-full max-w-md">
    </form>

    <!-- Kartu Praktikum -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if ($praktikumResult->num_rows > 0): ?>
            <?php while ($praktikum = $praktikumResult->fetch_assoc()): ?>
                <div class="bg-white p-4 rounded shadow border hover:shadow-lg transition">
                    <h3 class="text-xl font-bold"><?= htmlspecialchars($praktikum['nama_matakuliah']) ?></h3>
                    <p class="text-sm text-gray-600">Semester: <?= htmlspecialchars($praktikum['semester']) ?> | SKS: <?= htmlspecialchars($praktikum['sks']) ?></p>
                    <div class="mt-3">
                        <?php if (in_array($praktikum['id'], $krsList)): ?>
                            <span class="text-green-600 font-semibold">✔ Anda sudah mengambil praktikum ini</span>
                        <?php elseif ($isMahasiswa): ?>
                            <form method="post" onsubmit="return confirm('Apakah Anda yakin ingin mendaftar praktikum ini?')">
                                <input type="hidden" name="praktikum_id" value="<?= $praktikum['id'] ?>">
                                <button type="submit" name="daftar" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mt-2">
                                    Ambil Praktikum
                                </button>
                            </form>
                        <?php else: ?>
                            <p class="text-gray-400 italic">Login sebagai mahasiswa untuk mendaftar</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Tidak ada mata kuliah ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
