<?php
require_once 'config/database.php';
session_start();
if(!isset($_SESSION['user_id'])) header('Location: login.php');
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM photos WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$photo = $stmt->fetch();
if(!$photo) die('Foto tidak ditemukan');
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $stmt = $pdo->prepare("UPDATE photos SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $id]);
    header('Location: myphotos.php');
    exit;
}
include 'includes/navbar.php';
?>
<div class="max-w-2xl mx-auto bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-gray-100">
    <h1 class="text-2xl font-bold mb-4 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Edit Foto</h1>
    <img src="uploads/photos/<?= $photo['image_path'] ?>" class="w-full max-h-64 object-cover rounded-xl mb-4 shadow">
    <form method="POST">
        <input type="text" name="name" value="<?= htmlspecialchars($photo['name']) ?>" class="w-full border border-gray-200 rounded-xl p-2 mb-3" required>
        <textarea name="description" rows="4" class="w-full border border-gray-200 rounded-xl p-2 mb-4"><?= htmlspecialchars($photo['description']) ?></textarea>
        <button class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white w-full py-2 rounded-xl font-bold hover:shadow-lg transition">Simpan</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>