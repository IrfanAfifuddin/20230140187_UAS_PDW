<?php
require_once '../../config.php';
session_start();

$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once '../templates/header_mahasiswa.php';

$mahasiswa_id = $_SESSION['user_id'] ?? null;
if (!$mahasiswa_id || $_SESSION['role'] !== 'mahasiswa') {
    echo "<p class='text-red-500'>Silakan login sebagai mahasiswa.</p>";
    require_once '../templates/footer_mahasiswa.php';
    exit;
}

$praktikum_id = $_GET['id'] ?? null;
if (!$praktikum_id || !is_numeric($praktikum_id)) {
    echo "<p class='text-red-500'>ID praktikum tidak valid.</p>";
    require_once '../templates/footer_mahasiswa.php';
    exit;
}

// Ambil data praktikum & modul
$stmt = $conn->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
$stmt->bind_param("i", $praktikum_id);
$stmt->execute();
$praktikum = $stmt->get_result()->fetch_assoc();

$modulStmt = $conn->prepare("SELECT * FROM modul WHERE praktikum_id = ?");
$modulStmt->bind_param("i", $praktikum_id);
$modulStmt->execute();
$modulResult = $modulStmt->get_result();
?>

<div class="max-w-6xl mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($praktikum['nama_matakuliah']) ?></h1>

    <?php if ($modulResult->num_rows > 0): ?>
        <div class="space-y-6">
            <?php while ($modul = $modulResult->fetch_assoc()): ?>
                <div class="bg-white shadow rounded p-4">
                    <h2 class="text-lg font-semibold"><?= htmlspecialchars($modul['judul']) ?></h2>
                    <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($modul['deskripsi']) ?></p>

                    <?php if (!empty($modul['file_data'])): ?>
                        <a href="../../asisten/modul/view_file.php?id=<?= $modul['id'] ?>" target="_blank" class="text-blue-600 hover:underline mb-2 inline-block">
                            📄 Unduh Materi: <?= htmlspecialchars($modul['file_name']) ?>
                        </a>
                    <?php endif; ?>

                    <?php
                    $laporanStmt = $conn->prepare("SELECT * FROM laporan WHERE mahasiswa_id=? AND praktikum_id=? AND modul_id=?");
                    $laporanStmt->bind_param("iii", $mahasiswa_id, $praktikum_id, $modul['id']);
                    $laporanStmt->execute();
                    $laporan = $laporanStmt->get_result()->fetch_assoc();
                    ?>

                    <?php if (!$laporan): ?>
                        <!-- Belum upload -->
                        <form method="post" action="insert.php" enctype="multipart/form-data" class="mt-3 space-y-2">
                            <input type="hidden" name="modul_id" value="<?= $modul['id'] ?>">
                            <input type="hidden" name="praktikum_id" value="<?= $praktikum_id ?>">
                            <textarea name="text" placeholder="Catatan atau jawaban (opsional)" class="w-full p-2 border rounded"></textarea>
                            <input type="file" name="file" accept=".pdf,.doc,.docx" required class="w-full">
                            <button type="submit" name="submit_laporan" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                                Upload Laporan
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="mt-3 bg-gray-100 p-3 rounded">
                            <p class="text-sm text-green-600 font-semibold">✅ Laporan telah dikumpulkan</p>
                            <?php if (!empty($laporan['text'])): ?>
                                <p class="mt-2 text-sm text-gray-700">📝 Catatan Mahasiswa: <?= nl2br(htmlspecialchars($laporan['text'])) ?></p>
                            <?php endif; ?>
                            <p>Nilai: <strong><?= $laporan['nilai'] ?? 'Belum dinilai' ?></strong></p>
                            <p>Feedback: <em><?= $laporan['feedback'] ?? '-' ?></em></p>
                            <a href="view_laporan.php?id=<?= $laporan['id'] ?>" target="_blank" class="text-blue-600">
                                📄 <?= htmlspecialchars($laporan['file_name']) ?>
                            </a>

                            <?php if (is_null($laporan['nilai']) && is_null($laporan['feedback'])): ?>
                                <div class="mt-2 flex space-x-2">
                                    <button onclick="openEditModal(<?= $laporan['id'] ?>, '<?= htmlspecialchars($laporan['text'], ENT_QUOTES) ?>')" class="bg-yellow-500 text-white px-3 py-1 rounded">
                                        ✏️ Edit
                                    </button>
                                    <form method="post" action="delete.php" onsubmit="return confirm('Yakin hapus laporan ini?')">
                                        <input type="hidden" name="laporan_id" value="<?= $laporan['id'] ?>">
                                        <input type="hidden" name="praktikum_id" value="<?= $praktikum_id ?>">
                                        <button type="submit" name="delete_laporan" class="bg-red-500 text-white px-3 py-1 rounded">🗑️ Hapus</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">Belum ada modul tersedia.</p>
    <?php endif; ?>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg relative">
        <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Edit Laporan</h2>
        <form method="post" action="update.php" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="laporan_id" id="edit_laporan_id">
            <input type="hidden" name="praktikum_id" id="edit_praktikum_id" value="<?= $praktikum_id ?>">
            <textarea name="text" id="edit_text" class="w-full p-2 border rounded" placeholder="Catatan atau jawaban (opsional)"></textarea>
            <input type="file" name="file" accept=".pdf,.doc,.docx" class="w-full">
            <div class="text-right">
                <button type="submit" name="update_laporan" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>


<script>
function openEditModal(id, text) {
    document.getElementById('edit_laporan_id').value = id;
    document.getElementById('edit_text').value = text;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php require_once '../templates/footer_mahasiswa.php'; ?>
