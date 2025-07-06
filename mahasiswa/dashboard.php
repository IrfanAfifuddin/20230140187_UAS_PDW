<?php
require_once '../config.php';
session_start();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php';

$mahasiswa_id = $_SESSION['user_id'] ?? null;
if (!$mahasiswa_id || $_SESSION['role'] !== 'mahasiswa') {
    echo "<p class='text-red-500'>Silakan login sebagai mahasiswa.</p>";
    require_once 'templates/footer_mahasiswa.php';
    exit;
}

// Jumlah Praktikum Diikuti (dari krs)
$krsStmt = $conn->prepare("SELECT COUNT(*) AS total FROM krs WHERE user_id = ?");
$krsStmt->bind_param("i", $mahasiswa_id);
$krsStmt->execute();
$praktikumDiikuti = $krsStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Jumlah Tugas Selesai (laporan yang sudah dinilai)
$selesaiStmt = $conn->prepare("SELECT COUNT(*) AS total FROM laporan WHERE mahasiswa_id = ? AND nilai IS NOT NULL");
$selesaiStmt->bind_param("i", $mahasiswa_id);
$selesaiStmt->execute();
$tugasSelesai = $selesaiStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Jumlah Tugas Menunggu (modul yang belum dikumpulkan atau belum dinilai)
$tugasStmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM modul 
    INNER JOIN krs ON modul.praktikum_id = krs.praktikum_id
    LEFT JOIN laporan ON laporan.modul_id = modul.id AND laporan.mahasiswa_id = ?
    WHERE krs.user_id = ? AND (laporan.id IS NULL OR (laporan.nilai IS NULL AND laporan.feedback IS NULL))
");
$tugasStmt->bind_param("ii", $mahasiswa_id, $mahasiswa_id);
$tugasStmt->execute();
$tugasMenunggu = $tugasStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Ambil notifikasi (contoh: laporan dinilai, laporan terkirim, daftar praktikum)
$notifStmt = $conn->prepare("
    SELECT 'nilai' AS tipe, m.judul, mp.id AS praktikum_id
    FROM laporan l
    JOIN modul m ON l.modul_id = m.id
    JOIN mata_praktikum mp ON l.praktikum_id = mp.id
    WHERE l.mahasiswa_id = ? AND l.nilai IS NOT NULL

    UNION

    SELECT 'upload' AS tipe, m.judul, mp.id
    FROM laporan l
    JOIN modul m ON l.modul_id = m.id
    JOIN mata_praktikum mp ON l.praktikum_id = mp.id
    WHERE l.mahasiswa_id = ?

    UNION

    SELECT 'daftar' AS tipe, mp.nama_matakuliah AS judul, mp.id
    FROM krs k
    JOIN mata_praktikum mp ON k.praktikum_id = mp.id
    WHERE k.user_id = ?

    ORDER BY tipe DESC LIMIT 5
");
$notifStmt->bind_param("iii", $mahasiswa_id, $mahasiswa_id, $mahasiswa_id);
$notifStmt->execute();
$notifikasi = $notifStmt->get_result();
?>

<!-- Tampilan Selamat Datang -->
<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?= htmlspecialchars($_SESSION['nama']) ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<!-- Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?= $praktikumDiikuti ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?= $tugasSelesai ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?= $tugasMenunggu ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
</div>

<!-- Notifikasi -->
<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        <?php if ($notifikasi->num_rows > 0): ?>
            <?php while ($n = $notifikasi->fetch_assoc()): ?>
                <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
                    <?php if ($n['tipe'] === 'nilai'): ?>
                        <span class="text-xl mr-4">🔔</span>
                        <div>
                            Nilai untuk <a href="laporan/praktikum_detail.php?id=<?= $n['praktikum_id'] ?>" class="font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($n['judul']) ?></a> telah diberikan.
                        </div>
                    <?php elseif ($n['tipe'] === 'upload'): ?>
                        <span class="text-xl mr-4">✅</span>
                        <div>
                            Anda telah mengumpulkan laporan <a href="laporan/praktikum_detail.php?id=<?= $n['praktikum_id'] ?>" class="font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($n['judul']) ?></a>.
                        </div>
                    <?php elseif ($n['tipe'] === 'daftar'): ?>
                        <span class="text-xl mr-4">📚</span>
                        <div>
                            Anda berhasil mendaftar pada mata praktikum <a href="laporan/praktikum_detail.php?id=<?= $n['praktikum_id'] ?>" class="font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($n['judul']) ?></a>.
                        </div>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="text-gray-600">Tidak ada notifikasi terbaru.</li>
        <?php endif; ?>
    </ul>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
