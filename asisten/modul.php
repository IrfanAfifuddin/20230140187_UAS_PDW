<?php
$pageTitle = 'Modul';
$activePage = 'modul';
require_once '../config.php'; // sesuaikan jika perlu
require_once 'templates/header.php';

// Ambil semua mata kuliah
$query = "SELECT * FROM mata_praktikum ORDER BY semester ASC";
$result = $conn->query($query);
?>

<div class="max-w-7xl mx-auto py-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div onclick="location.href='modul/modul_detail.php?id=<?= $row['id'] ?>'"
                     class="cursor-pointer bg-white rounded-lg shadow p-5 hover:shadow-lg transition">
                    <div class="flex items-center mb-3">
                        <svg class="w-6 h-6 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6V4.5a1.5 1.5 0 00-1.5-1.5h-6A1.5 1.5 0 003 4.5V19.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V18m0-12v12m0-12v12m0-12h0a1.5 1.5 0 011.5-1.5h6A1.5 1.5 0 0121 4.5v15a1.5 1.5 0 01-1.5 1.5h-6a1.5 1.5 0 01-1.5-1.5V6z"/>
                        </svg>
                        <h2 class="text-lg font-semibold"><?= htmlspecialchars($row['nama_matakuliah']) ?></h2>
                    </div>
                    <p class="text-sm text-gray-600">Semester: <?= htmlspecialchars($row['semester']) ?></p>
                    <p class="text-sm text-gray-600">SKS: <?= htmlspecialchars($row['sks']) ?></p>
                    <p class="text-sm mt-2 text-gray-400 italic">Klik untuk mengelola modul</p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-3 text-center text-gray-500">Tidak ada mata kuliah ditemukan.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
