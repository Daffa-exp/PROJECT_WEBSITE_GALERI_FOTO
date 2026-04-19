<?php
require_once 'config/database.php';
session_start();
if(!isset($_SESSION['user_id'])) header('Location: index.php');

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$edit_mode = isset($_GET['edit']);
$share = isset($_GET['share']);

if(!is_dir('uploads/avatars')) {
    mkdir('uploads/avatars', 0777, true);
}

if($share) {
    $user_id = $_SESSION['user_id'];
    $photos_count = $pdo->prepare("SELECT COUNT(*) FROM photos WHERE user_id = ?");
    $photos_count->execute([$user_id]);
    $total_photos = $photos_count->fetchColumn();
    $likes_count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE photo_id IN (SELECT id FROM photos WHERE user_id = ?)");
    $likes_count->execute([$user_id]);
    $total_likes = $likes_count->fetchColumn();
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bagikan Profil - <?= htmlspecialchars($user['name']) ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            @keyframes fadeScale {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
            .share-card {
                animation: fadeScale 0.5s ease-out;
            }
        </style>
    </head>
    <body class="bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 min-h-screen flex items-center justify-center p-4">
        <div class="share-card bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl max-w-md w-full p-6 text-center border border-white/20">
            <?php if($user['avatar'] && file_exists('uploads/avatars/' . $user['avatar'])): ?>
                <img src="uploads/avatars/<?= $user['avatar'] ?>" class="w-28 h-28 rounded-full mx-auto object-cover border-4 border-white shadow-lg">
            <?php else: ?>
                <div class="w-28 h-28 rounded-full mx-auto bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center border-4 border-white shadow-lg">
                    <i class="fas fa-user fa-3x text-white"></i>
                </div>
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-white mt-4"><?= htmlspecialchars($user['name']) ?></h1>
            <p class="text-white/70 mt-1"><?= htmlspecialchars($user['bio'] ?? 'Belum ada bio') ?></p>
            <div class="flex justify-center gap-6 mt-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white"><?= $total_photos ?></div>
                    <div class="text-white/60 text-sm">Foto</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white"><?= $total_likes ?></div>
                    <div class="text-white/60 text-sm">Like Diterima</div>
                </div>
            </div>
            <a href="profile.php" class="mt-6 inline-block bg-white text-indigo-700 px-6 py-2 rounded-full font-semibold hover:shadow-lg transition">Kembali ke Profil</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $edit_mode) {
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $avatar = $user['avatar'];
    
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $avatar = time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = 'uploads/avatars/' . $avatar;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target);
        
        if($user['avatar'] && file_exists('uploads/avatars/' . $user['avatar'])) {
            unlink('uploads/avatars/' . $user['avatar']);
        }
    }
    
    $upd = $pdo->prepare("UPDATE users SET name = ?, bio = ?, avatar = ? WHERE id = ?");
    $upd->execute([$name, $bio, $avatar, $_SESSION['user_id']]);
    $_SESSION['user_name'] = $name;
    header('Location: profile.php');
    exit;
}

include 'includes/navbar.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 text-white">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Profil Saya</h1>
                <a href="profile.php?share=1" target="_blank" class="bg-white/20 hover:bg-white/30 px-4 py-1.5 rounded-full text-sm transition"><i class="fas fa-share-alt mr-1"></i> Bagikan Profil</a>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col items-center mb-6">
                <?php if($user['avatar'] && file_exists('uploads/avatars/' . $user['avatar'])): ?>
                    <img src="uploads/avatars/<?= $user['avatar'] ?>?t=<?= time() ?>" class="w-24 h-24 rounded-full object-cover shadow-lg border-4 border-white">
                <?php else: ?>
                    <div class="w-24 h-24 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg border-4 border-white">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if($edit_mode): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Profil</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full border border-gray-200 rounded-xl p-2">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG (Max 2MB)</p>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="w-full border border-gray-200 rounded-xl p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" rows="3" class="w-full border border-gray-200 rounded-xl p-2" placeholder="Cerita tentang dirimu..."><?= htmlspecialchars($user['bio']) ?></textarea>
                </div>
                <button class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white w-full py-2 rounded-xl font-bold hover:shadow-lg transition">Simpan Perubahan</button>
                <a href="profile.php" class="block text-center text-gray-500 mt-3 text-sm">Batal</a>
            </form>
            <?php else: ?>
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['name']) ?></h2>
                    <p class="text-gray-500 mt-1"><?= htmlspecialchars($user['bio'] ?? 'Belum ada bio') ?></p>
                    <p class="text-gray-400 text-sm mt-2"><i class="fas fa-envelope mr-1"></i> <?= htmlspecialchars($user['email']) ?></p>
                    <a href="profile.php?edit=1" class="mt-4 inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full font-semibold hover:shadow-lg transition"><i class="fas fa-edit mr-1"></i> Edit Profil</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>