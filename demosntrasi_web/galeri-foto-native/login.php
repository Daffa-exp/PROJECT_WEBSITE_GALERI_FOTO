<?php
require_once 'config/database.php';
session_start();
if(isset($_SESSION['user_id'])) header('Location: index.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();
    if($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GaleriFoto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-card {
            animation: fadeSlide 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 min-h-screen flex items-center justify-center p-4">
    <div class="login-card bg-white/10 backdrop-blur-md p-8 rounded-2xl shadow-2xl w-full max-w-md border border-white/20">
        <div class="text-center mb-6">
            <i class="fas fa-camera-retro text-4xl text-white"></i>
            <h1 class="text-3xl font-bold text-white mt-2">Selamat Datang</h1>
        </div>
        <?php if(isset($error)): ?>
            <div class="bg-red-500/80 text-white p-2 rounded mb-3 text-center"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" class="w-full bg-white/20 text-white placeholder-white/70 rounded-xl p-3 mb-3 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50" required>
            <input type="password" name="password" placeholder="Password" class="w-full bg-white/20 text-white placeholder-white/70 rounded-xl p-3 mb-5 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50" required>
            <button class="w-full bg-white text-indigo-700 py-2 rounded-xl font-bold hover:shadow-lg transition">Masuk</button>
        </form>
        <p class="text-center text-white/80 mt-4">Belum punya akun? <a href="register.php" class="text-white font-semibold">Daftar</a></p>
    </div>
</body>
</html>