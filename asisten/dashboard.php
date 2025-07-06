<?php
require_once '../config.php';
session_start();

// Autentikasi role admin/asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header.php';

// Data dinamis
$modul_total = $conn->query("SELECT COUNT(*) AS total FROM modul")->fetch_assoc()['total'];
$laporan_total = $conn->query("SELECT COUNT(*) AS total FROM laporan")->fetch_assoc()['total'];
$laporan_pending = $conn->query("SELECT COUNT(*) AS total FROM laporan WHERE nilai IS NULL AND feedback IS NULL")->fetch_assoc()['total'];

$latest = $conn->query("
    SELECT l.uploaded_at, u.nama, m.judul 
    FROM laporan l 
    JOIN users u ON l.mahasiswa_id = u.id 
    JOIN modul m ON l.modul_id = m.id 
    ORDER BY l.uploaded_at DESC 
    LIMIT 5
");
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12M6.75 6H18a.75.75 0 01.75.75v11.25a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V6.75A.75.75 0 016.75 6z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?= $modul_total ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?= $laporan_total ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.5m0 3.5h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?= $laporan_pending ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php while ($row = $latest->fetch_assoc()): ?>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                    <span class="font-bold text-gray-500"><?= strtoupper(substr($row['nama'], 0, 2)) ?></span>
                </div>
                <div>
                    <p class="text-gray-800">
                        <strong><?= htmlspecialchars($row['nama']) ?></strong> mengumpulkan laporan untuk <strong><?= htmlspecialchars($row['judul']) ?></strong>
                    </p>
                    <p class="text-sm text-gray-500"><?= date('d M Y H:i', strtotime($row['uploaded_at'])) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
