<?php
require_once '../config.php';
require_once 'matapraktikum/insert.php';
require_once 'matapraktikum/update.php';
require_once 'matapraktikum/delete.php';

$pageTitle = 'Matakuliah Praktikum';
$activePage = 'mata_praktikum';
require_once 'templates/header.php';
?>

<div class="max-w-6xl py-3">
    <div class="flex justify-between items-center mb-4">
        <!-- Tombol Tambah -->
        <button onclick="openModal('insertModal')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4v16m8-8H4"></path></svg>
            Tambah
        </button>
    </div>

    <!-- Modal Tambah -->
    <div id="insertModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg relative">
            <button onclick="closeModal('insertModal')" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Tambah Mata Kuliah</h2>
            <form method="post" class="space-y-4">
                <input type="text" name="kd_matakuliah" placeholder="Kode Mata Kuliah" required class="w-full p-2 border rounded">
                <input type="text" name="nama_matakuliah" placeholder="Nama Mata Kuliah" required class="w-full p-2 border rounded">
                <input type="text" name="semester" placeholder="Semester" required class="w-full p-2 border rounded">
                <input type="text" name="sks" placeholder="SKS" required class="w-full p-2 border rounded">
                <div class="text-right">
                    <button type="submit" name="tambah" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
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
            <h2 class="text-xl font-semibold mb-4">Edit Mata Kuliah</h2>
            <form method="post" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="kd_matakuliah" id="edit_kode" required class="w-full p-2 border rounded">
                <input type="text" name="nama_matakuliah" id="edit_nama" required class="w-full p-2 border rounded">
                <input type="text" name="semester" id="edit_semester" required class="w-full p-2 border rounded">
                <input type="text" name="sks" id="edit_sks" required class="w-full p-2 border rounded">
                <div class="text-right">
                    <button type="submit" name="update" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded shadow mt-6">
        <table class="min-w-full table-auto border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-3 text-left">Kode Mata Kuliah</th>
                    <th class="border p-3 text-left">Nama Mata Kuliah</th>
                    <th class="border p-3 text-left">Semester</th>
                    <th class="border p-3 text-left">SKS</th>
                    <th class="border p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM mata_praktikum ORDER BY semester ASC";
                $result = $conn->query($query);
                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border p-3"><?= htmlspecialchars($row['kd_matakuliah']) ?></td>
                        <td class="border p-3"><?= htmlspecialchars($row['nama_matakuliah']) ?></td>
                        <td class="border p-3"><?= htmlspecialchars($row['semester']) ?></td>
                        <td class="border p-3"><?= htmlspecialchars($row['sks']) ?></td>
                        <td class="border p-3 flex space-x-2">
                            <button 
                                onclick="fillEditForm(
                                    '<?= $row['id'] ?>',
                                    '<?= htmlspecialchars($row['kd_matakuliah'], ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($row['nama_matakuliah'], ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($row['semester'], ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars($row['sks'], ENT_QUOTES) ?>'
                                )"
                                class="text-blue-500 hover:underline">Edit</button>
                            <form method="post" onsubmit="return confirm('Yakin ingin menghapus?')">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete" class="text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 p-4">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="templates/modal.js"></script>
<script>
function fillEditForm(id, kode, nama, semester, sks) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_kode').value = kode;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_semester').value = semester;
    document.getElementById('edit_sks').value = sks;
    openModal('editModal');
}
</script>

<?php require_once 'templates/footer.php'; ?>
