<?php
require_once 'config/database.php';
session_start();
if(!isset($_SESSION['user_id'])) header('Location: login.php');

$stmt = $pdo->prepare("
    SELECT photos.*,
    (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id) as total_likes
    FROM photos WHERE user_id = ? ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$photos = $stmt->fetchAll();
include 'includes/navbar.php';
?>
<div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-gray-100">
    <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Foto Saya</h1>
            <p class="text-gray-500 text-sm">Kelola koleksi foto Anda</p>
        </div>
        <a href="upload.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-semibold hover:shadow-md transition">+ Upload Baru</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr><th class="p-2">Foto</th><th>Nama</th><th>Deskripsi</th><th>Like</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach($photos as $p): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-2"><img src="uploads/photos/<?= $p['image_path'] ?>" width="50" height="50" class="rounded object-cover shadow"></td>
                    <td class="font-semibold"><?= htmlspecialchars($p['name']) ?></td>
                    <td class="text-gray-600"><?= htmlspecialchars(substr($p['description'] ?? '', 0, 40)) ?></td>
                    <td><span class="text-red-500"><i class="fas fa-heart"></i> <?= $p['total_likes'] ?></span></td>
                    <td>
                        <a href="editphoto.php?id=<?= $p['id'] ?>" class="text-yellow-600 mr-2 hover:text-yellow-700"><i class="fas fa-edit"></i></a>
                        <a href="deletephoto.php?id=<?= $p['id'] ?>" class="text-red-600 hover:text-red-700" onclick="return confirm('Yakin hapus foto ini?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>