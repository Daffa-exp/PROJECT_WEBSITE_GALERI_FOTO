<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>GaleriFoto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .splash {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4c1d95 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease;
        }
        .splash-logo {
            animation: float 2s ease-in-out infinite;
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.25);
        }
        .carousel-item {
            position: relative;
            overflow: hidden;
        }
        .carousel-overlay {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%);
            transition: bottom 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 30px 20px 20px 20px;
        }
        .carousel-item:hover .carousel-overlay {
            bottom: 0;
        }
        .carousel-item:hover img {
            transform: scale(1.05);
        }
        .carousel-item img {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .carousel-track {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .desc-text {
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.6;
        }
        .animate-fade-up {
            animation: fadeInUp 0.6s ease-out;
        }
        .like-btn {
            transition: all 0.2s ease;
        }
        .like-btn:active {
            transform: scale(0.95);
        }
        @media (max-width: 768px) {
            .desktop-menu {
                display: none;
            }
            .mobile-menu-open {
                display: flex;
            }
        }
        @media (min-width: 769px) {
            .mobile-menu-open {
                display: none;
            }
            .desktop-menu {
                display: flex;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-gray-50 to-indigo-50">

<div id="splash" class="splash">
    <div class="text-center splash-logo">
        <i class="fas fa-camera-retro text-7xl text-white mb-4 opacity-90" style="animation: pulse 1.5s infinite;"></i>
        <h1 class="text-3xl font-bold text-white tracking-wide">GaleriFoto</h1>
    </div>
</div>

<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md shadow-lg border-b border-gray-100">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="index.php" class="text-xl md:text-2xl font-extrabold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
            <i class="fas fa-camera-retro mr-2 text-indigo-500"></i>GaleriFoto
        </a>
        
        <button class="block md:hidden text-gray-600 focus:outline-none" id="menu-toggle">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <div class="desktop-menu space-x-6 items-center">
            <a href="index.php" class="text-gray-700 hover:text-indigo-600 font-medium transition">Beranda</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="upload.php" class="text-gray-700 hover:text-indigo-600 font-medium transition">Upload</a>
                <a href="myphotos.php" class="text-gray-700 hover:text-indigo-600 font-medium transition">Foto Saya</a>
                <a href="profile.php" class="text-gray-700 hover:text-indigo-600 font-medium transition">Profil</a>
                <a href="logout.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-1.5 rounded-full text-sm font-semibold hover:shadow-lg transition">Keluar</a>
                <div class="bg-gradient-to-r from-yellow-400 to-orange-400 px-3 py-1 rounded-full shadow-md inline-block">
                    <span class="text-white text-sm font-semibold whitespace-nowrap">
                        <i class="fas fa-hand-peace mr-1"></i> Halo, <?= htmlspecialchars($_SESSION['user_name']) ?>! <i class="fas fa-smile-wink ml-1"></i>
                    </span>
                </div>
            <?php else: ?>
                <a href="login.php" class="text-gray-700 hover:text-indigo-600 font-medium transition">Masuk</a>
                <a href="register.php" class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white px-5 py-1.5 rounded-full text-sm font-semibold hover:shadow-lg transition">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="mobile-menu-open flex-col items-center bg-white py-4 border-t border-gray-100 hidden" id="mobile-menu">
        <a href="index.php" class="py-3 text-gray-700 hover:bg-gray-100 w-full text-center transition">🏠 Beranda</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="upload.php" class="py-3 text-gray-700 hover:bg-gray-100 w-full text-center transition">📤 Upload</a>
            <a href="myphotos.php" class="py-3 text-gray-700 hover:bg-gray-100 w-full text-center transition">📸 Foto Saya</a>
            <a href="profile.php" class="py-3 text-gray-700 hover:bg-gray-100 w-full text-center transition">👤 Profil</a>
            <a href="logout.php" class="py-3 text-white bg-red-500 hover:bg-red-600 w-11/12 mx-auto rounded-full text-center transition mt-2">🚪 Keluar</a>
            <div class="bg-gradient-to-r from-yellow-400 to-orange-400 px-4 py-2 rounded-full shadow-md mt-3 mx-auto">
                <span class="text-white font-semibold">
                    ✌️ Halo, <?= htmlspecialchars($_SESSION['user_name']) ?>! 😊
                </span>
            </div>
        <?php else: ?>
            <a href="login.php" class="py-3 text-gray-700 hover:bg-gray-100 w-full text-center transition">🔐 Masuk</a>
            <a href="register.php" class="py-3 text-white bg-gradient-to-r from-indigo-600 to-purple-600 w-11/12 mx-auto rounded-full text-center transition mt-2">📝 Daftar</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if(menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    window.addEventListener('load', () => {
        setTimeout(() => {
            const splash = document.getElementById('splash');
            if(splash) {
                splash.style.opacity = '0';
                setTimeout(() => splash.style.display = 'none', 800);
            }
        }, 1000);
    });
</script>

<main class="container mx-auto px-4 py-8 animate-fade-up">