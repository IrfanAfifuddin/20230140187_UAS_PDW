<?php
require_once 'templates/header.php';
require_once '../config.php';

// Cek role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    echo "<script>alert('Akses ditolak'); location.href='../login.php';</script>";
    exit;
}

// Ambil data filter
$modul_id = $_GET['modul_id'] ?? '';
$mahasiswa_id = $_GET['mahasiswa_id'] ?? '';
$status = $_GET['status'] ?? '';

// Query laporan
$query = "SELECT laporan.*, users.nama AS nama_mahasiswa, modul.judul AS nama_modul 
          FROM laporan
          JOIN users ON laporan.mahasiswa_id = users.id
          JOIN modul ON laporan.modul_id = modul.id
          WHERE 1=1";

$params = [];
$types = "";

// Filter
if (!empty($modul_id)) {
    $query .= " AND modul_id = ?";
    $types .= "i";
    $params[] = $modul_id;
}

if (!empty($mahasiswa_id)) {
    $query .= " AND mahasiswa_id = ?";
    $types .= "i";
    $params[] = $mahasiswa_id;
}

if ($status === 'sudah') {
    $query .= " AND nilai IS NOT NULL";
} elseif ($status === 'belum') {
    $query .= " AND nilai IS NULL";
}

// Prepare
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="max-w-6xl mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Laporan Mahasiswa</h1>

    <!-- Filter -->
    <form method="GET" class="flex flex-wrap gap-4 mb-4">
        <select name="modul_id" class="p-2 border rounded">
            <option value="">-- Filter Modul --</option>
            <?php
            $modulRes = $conn->query("SELECT id, judul FROM modul");
            while ($m = $modulRes->fetch_assoc()):
            ?>
                <option value="<?= $m['id'] ?>" <?= $modul_id == $m['id'] ? 'selected' : '' ?>><?= $m['judul'] ?></option>
            <?php endwhile; ?>
        </select>

        <select name="mahasiswa_id" class="p-2 border rounded">
            <option value="">-- Filter Mahasiswa --</option>
            <?php
            $mhsRes = $conn->query("SELECT id, nama FROM users WHERE role='mahasiswa'");
            while ($mhs = $mhsRes->fetch_assoc()):
            ?>
                <option value="<?= $mhs['id'] ?>" <?= $mahasiswa_id == $mhs['id'] ? 'selected' : '' ?>><?= $mhs['nama'] ?></option>
            <?php endwhile; ?>
        </select>

        <select name="status" class="p-2 border rounded">
            <option value="">-- Semua Status --</option>
            <option value="sudah" <?= $status == 'sudah' ? 'selected' : '' ?>>Sudah Dinilai</option>
            <option value="belum" <?= $status == 'belum' ? 'selected' : '' ?>>Belum Dinilai</option>
        </select>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <!-- Tabel Laporan -->
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2 text-left">Mahasiswa</th>
                    <th class="p-2 text-left">Modul</th>
                    <th class="p-2 text-left">Catatan</th>
                    <th class="p-2 text-left">File</th>
                    <th class="p-2 text-left">Nilai</th>
                    <th class="p-2 text-left">Feedback</th>
                    <th class="p-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="p-2"><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($row['nama_modul']) ?></td>
                        <td class="p-2 text-sm"><?= nl2br(htmlspecialchars($row['text'])) ?></td>
                        <td class="p-2">
                            <a href="laporan/view_laporan.php?id=<?= $row['id'] ?>" target="_blank" class="text-blue-600 underline">
                                <?= htmlspecialchars($row['file_name']) ?>
                            </a>
                        </td>
                        <td class="p-2"><?= $row['nilai'] ?? '-' ?></td>
                        <td class="p-2"><?= $row['feedback'] ?? '-' ?></td>
                        <td class="p-2">
                            <form action="laporan/nilai.php" method="post" class="flex flex-col gap-1">
                                <input type="hidden" name="laporan_id" value="<?= $row['id'] ?>">
                                <input type="number" name="nilai" value="<?= $row['nilai'] ?>" placeholder="Nilai" class="border p-1 rounded">
                                <input type="text" name="feedback" value="<?= $row['feedback'] ?>" placeholder="Feedback" class="border p-1 rounded">
                                <div class="flex gap-1">
                                    <button name="action" value="simpan" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-sm">Simpan</button>
                                    <?php if ($row['nilai'] !== null || $row['feedback']): ?>
                                        <button name="action" value="batal" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">Batalkan</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
