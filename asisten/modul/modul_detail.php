<?php
require_once '../../config.php';
require_once 'insert.php';
require_once 'update.php';
require_once 'delete.php';

$praktikumId = $_GET['id'] ?? null;

if (!$praktikumId || !is_numeric($praktikumId)) {
    die("ID tidak valid");
}

// Ambil data mata kuliah
$stmt = $conn->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
$stmt->bind_param("i", $praktikumId);
$stmt->execute();
$mataKuliah = $stmt->get_result()->fetch_assoc();

$pageTitle = 'Modul - ' . htmlspecialchars($mataKuliah['nama_matakuliah']);
$activePage = 'modul';
require_once '../templates/header.php';

// Ambil modul
$stmt = $conn->prepare("SELECT * FROM modul WHERE praktikum_id = ?");
$stmt->bind_param("i", $praktikumId);
$stmt->execute();
$modulResult = $stmt->get_result();
?>

<div class="max-w-6xl py-3">
    <div class="flex justify-between items-center mb-4">
        <button onclick="openModal('insertModal')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded">
            Tambah Modul
        </button>
    </div>

    <!-- Modal Tambah -->
    <div id="insertModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg relative">
            <button onclick="closeModal('insertModal')" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Tambah Modul</h2>
            <form method="post" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="praktikum_id" value="<?= $praktikumId ?>">
                <input type="text" name="judul" placeholder="Judul Modul" required class="w-full p-2 border rounded">
                <textarea name="deskripsi" placeholder="Deskripsi" required class="w-full p-2 border rounded"></textarea>
                <input type="file" name="file" accept=".pdf,.doc,.docx" required class="w-full">
                <div class="text-right">
                    <button type="submit" name="tambah_modul" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg relative">
            <button onclick="closeModal('editModal')" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Edit Modul</h2>
            <form method="post" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="praktikum_id" value="<?= $praktikumId ?>">
                <input type="text" name="judul" id="edit_judul" required class="w-full p-2 border rounded">
                <textarea name="deskripsi" id="edit_deskripsi" required class="w-full p-2 border rounded"></textarea>
                <input type="file" name="file" accept=".pdf,.doc,.docx" class="w-full">
                <div class="text-right">
                    <button type="submit" name="update_modul" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Modul -->
    <div class="bg-white rounded shadow mt-6">
        <table class="min-w-full table-auto border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-3 text-left">Judul</th>
                    <th class="border p-3 text-left">Deskripsi</th>
                    <th class="border p-3 text-left">File</th>
                    <th class="border p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($modulResult->num_rows > 0): ?>
                    <?php while ($modul = $modulResult->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-3"><?= htmlspecialchars($modul['judul']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($modul['deskripsi']) ?></td>
                            <td class="border p-3">
                            <?php if (!empty($modul['file_data']) && !empty($modul['file_name'])): ?>
                                <a href="view_file.php?id=<?= $modul['id'] ?>" target="_blank" class="text-blue-600 hover:underline">
                                    <?= htmlspecialchars($modul['file_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400 italic">Tidak ada file</span>
                            <?php endif; ?>
                        </td>

                            <td class="border p-3 flex space-x-2">
                                <button onclick="fillEditForm('<?= $modul['id'] ?>', '<?= htmlspecialchars($modul['judul'], ENT_QUOTES) ?>', '<?= htmlspecialchars($modul['deskripsi'], ENT_QUOTES) ?>')" class="text-blue-500 hover:underline">Edit</button>
                                <form method="post" onsubmit="return confirm('Hapus modul ini?')">
                                    <input type="hidden" name="delete_id" value="<?= $modul['id'] ?>">
                                    <input type="hidden" name="praktikum_id" value="<?= $praktikumId ?>">
                                    <button type="submit" name="delete_modul" class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-gray-500 p-4">Belum ada modul.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../templates/modal.js"></script>
<script>
function fillEditForm(id, judul, deskripsi) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_judul').value = judul;
    document.getElementById('edit_deskripsi').value = deskripsi;
    openModal('editModal');
}
</script>

<?php require_once '../templates/footer.php'; ?>
