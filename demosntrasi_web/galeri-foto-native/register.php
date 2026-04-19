<?php
require_once 'config/database.php';
session_start();
if(isset($_SESSION['user_id'])) header('Location: index.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
    try {
        $stmt->execute([$name, $email, $password]);
        $_SESSION['success'] = 'Register berhasil! Silakan login.';
        header('Location: login.php');
        exit;
    } catch(PDOException $e) {
        $error = 'Email sudah terdaftar!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - GaleriFoto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 to-purple-800 h-screen flex items-center justify-center">
    <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl shadow-2xl w-full max-w-md border border-white/20">
        <h1 class="text-3xl font-bold text-center text-white mb-6">Daftar Akun</h1>
        <?php if(isset($error)): ?>
            <div class="bg-red-500/80 text-white p-2 rounded mb-3 text-center"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Nama Lengkap" class="w-full bg-white/20 text-white placeholder-white/70 rounded-xl p-3 mb-3 border border-white/30 focus:outline-none" required>
            <input type="email" name="email" placeholder="Email" class="w-full bg-white/20 text-white placeholder-white/70 rounded-xl p-3 mb-3 border border-white/30 focus:outline-none" required>
            <input type="password" name="password" placeholder="Password" class="w-full bg-white/20 text-white placeholder-white/70 rounded-xl p-3 mb-5 border border-white/30 focus:outline-none" required>
            <button class="bg-white text-indigo-700 w-full py-2 rounded-xl font-bold hover:shadow-lg transition">Daftar</button>
        </form>
        <p class="text-center text-white/80 mt-3">Sudah punya akun? <a href="login.php" class="text-white font-semibold">Login</a></p>
    </div>
</body>
</html>