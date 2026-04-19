<?php
require_once 'config/database.php';
session_start();
if(!isset($_SESSION['user_id'])) header('Location: login.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $file = $_FILES['photo'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . rand(1000,9999) . '.' . $ext;
    move_uploaded_file($file['tmp_name'], 'uploads/photos/' . $filename);
    $stmt = $pdo->prepare("INSERT INTO photos (user_id, name, description, image_path) VALUES (?,?,?,?)");
    $stmt->execute([$_SESSION['user_id'], $name, $desc, $filename]);
    header('Location: myphotos.php');
    exit;
}
include 'includes/navbar.php';
?>
<div class="max-w-2xl mx-auto bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-gray-100">
    <div class="text-center mb-4">
        <i class="fas fa-cloud-upload-alt text-4xl text-indigo-500"></i>
        <h1 class="text-2xl font-bold mt-2 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Upload Foto Baru</h1>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*" class="w-full border border-gray-200 rounded-xl p-2 mb-3" required>
        <input type="text" name="name" placeholder="Nama Foto" class="w-full border border-gray-200 rounded-xl p-2 mb-3" required>
        <textarea name="description" placeholder="Deskripsi" class="w-full border border-gray-200 rounded-xl p-2 mb-4" rows="4"></textarea>
        <button class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white w-full py-2 rounded-xl font-bold hover:shadow-lg transition">Upload</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>