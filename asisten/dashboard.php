<?php
require_once '../config.php';
session_start();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header.php';

// Total modul
$modul_total = $conn->query("SELECT COUNT(*) as total FROM modul")->fetch_assoc()['total'];

// Total laporan masuk
$laporan_total = $conn->query("SELECT COUNT(*) as total FROM laporan")->fetch_assoc()['total'];

// Total laporan belum dinilai
$laporan_pending = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE nilai IS NULL AND feedback IS NULL")->fetch_assoc()['total'];

// Aktivitas laporan terbaru (5 terakhir)
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
            <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75..." />
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?= $modul_total ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75..." />
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?= $laporan_total ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5..." />
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
                    <p class="text-gray-800"><strong><?= htmlspecialchars($row['nama']) ?></strong> mengumpulkan laporan untuk <strong><?= htmlspecialchars($row['judul']) ?></strong></p>
                    <p class="text-sm text-gray-500"><?= date('d M Y H:i', strtotime($row['uploaded_at'])) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
