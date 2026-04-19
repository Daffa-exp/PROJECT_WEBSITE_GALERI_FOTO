<?php
require_once 'config/database.php';
include 'includes/navbar.php';

$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;

if($search) {
    $stmt = $pdo->prepare("
        SELECT photos.*, users.name as user_name, users.avatar,
        (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id) as total_likes
        FROM photos
        JOIN users ON photos.user_id = users.id
        WHERE photos.name LIKE :search OR users.name LIKE :search
        ORDER BY photos.created_at DESC
    ");
    $stmt->execute(['search' => $search]);
} else {
    $stmt = $pdo->query("
        SELECT photos.*, users.name as user_name, users.avatar,
        (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id) as total_likes
        FROM photos
        JOIN users ON photos.user_id = users.id
        ORDER BY photos.created_at DESC
    ");
}
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$carouselStmt = $pdo->query("
    SELECT photos.*, users.name as user_name,
    (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id) as total_likes
    FROM photos
    JOIN users ON photos.user_id = users.id
    ORDER BY total_likes DESC
    LIMIT 5
");
$carousel = $carouselStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mb-10 overflow-hidden rounded-2xl shadow-2xl relative">
    <div id="carousel" class="relative w-full">
        <div class="overflow-hidden rounded-2xl">
            <div id="carousel-track" class="flex carousel-track">
                <?php if(count($carousel) > 0): ?>
                    <?php foreach($carousel as $index => $item): ?>
                    <div class="w-full flex-shrink-0 carousel-item">
                        <img src="uploads/photos/<?= htmlspecialchars($item['image_path']) ?>" class="w-full h-80 md:h-96 object-cover">
                        <div class="carousel-overlay">
                            <h3 class="text-xl md:text-2xl font-bold text-white"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="text-sm text-white/80 mt-1"><?= htmlspecialchars(substr($item['description'] ?? '', 0, 100)) ?></p>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-xs text-white/60"><i class="fas fa-user mr-1"></i><?= htmlspecialchars($item['user_name']) ?></p>
                                <p class="text-xs text-red-400"><i class="fas fa-heart mr-1"></i><span class="carousel-like-<?= $item['id'] ?>"><?= $item['total_likes'] ?></span> likes</p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="w-full flex-shrink-0 carousel-item">
                        <img src="https://via.placeholder.com/1200x400?text=Belum+Ada+Foto" class="w-full h-80 md:h-96 object-cover">
                        <div class="carousel-overlay">
                            <h3 class="text-xl md:text-2xl font-bold text-white">Belum Ada Foto</h3>
                            <p class="text-sm text-white/80 mt-1">Jadi yang pertama upload foto!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <button id="prevBtn" class="absolute left-3 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 transition z-10"><i class="fas fa-chevron-left"></i></button>
        <button id="nextBtn" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 transition z-10"><i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<div class="flex justify-between items-center mb-8 flex-wrap gap-4">
    <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Jelajahi Koleksi</h2>
    <form method="GET" class="flex">
        <input type="text" name="search" placeholder="Cari foto atau pengguna..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="border border-gray-300 rounded-l-full px-5 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2 rounded-r-full hover:shadow-md transition"><i class="fas fa-search"></i></button>
    </form>
</div>

<?php if(empty($photos)): ?>
    <div class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-2xl shadow">
        <i class="fas fa-camera-slash text-5xl text-gray-400 mb-3"></i>
        <p class="text-gray-500">Belum ada foto. Jadi yang pertama upload!</p>
    </div>
<?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
    <?php foreach($photos as $photo): ?>
    <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
        <img src="uploads/photos/<?= htmlspecialchars($photo['image_path']) ?>" class="w-full h-52 object-cover cursor-pointer" onclick="openModal(<?= htmlspecialchars(json_encode($photo)) ?>)">
        <div class="p-3">
            <h3 class="font-bold text-sm truncate cursor-pointer" onclick="openModal(<?= htmlspecialchars(json_encode($photo)) ?>)"><?= htmlspecialchars($photo['name']) ?></h3>
            <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                <span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($photo['user_name']) ?></span>
                <span class="text-red-500"><i class="fas fa-heart"></i> <span id="like-count-<?= $photo['id'] ?>"><?= $photo['total_likes'] ?></span></span>
            </div>
            <button class="like-btn w-full text-sm bg-gray-100 hover:bg-red-100 rounded-full py-1.5 transition mt-2" data-id="<?= $photo['id'] ?>">❤️ Like</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div id="modal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4" onclick="closeModal()">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex flex-col md:flex-row">
            <img id="modal-img" src="" class="w-full md:w-1/2 object-cover rounded-l-2xl">
            <div class="p-6 md:w-1/2">
                <h3 id="modal-name" class="text-2xl font-bold mb-2"></h3>
                <div class="flex items-center gap-2 mb-4">
                    <img id="modal-avatar" src="" class="w-8 h-8 rounded-full object-cover">
                    <span id="modal-user" class="font-semibold"></span>
                </div>
                <hr class="my-3">
                <p class="font-semibold">Deskripsi</p>
                <p id="modal-desc" class="text-gray-600 mt-1 desc-text"></p>
                <hr class="my-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-heart text-red-500 text-2xl"></i>
                    <span id="modal-likes" class="text-xl font-bold"></span>
                    <span class="text-gray-500">menyukai ini</span>
                </div>
                <button onclick="closeModal()" class="mt-5 bg-indigo-600 text-white px-5 py-2 rounded-full hover:bg-indigo-700 transition">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(photo) {
    document.getElementById('modal-img').src = 'uploads/photos/' + photo.image_path;
    document.getElementById('modal-name').innerText = photo.name;
    document.getElementById('modal-desc').innerText = photo.description || 'Tidak ada deskripsi';
    document.getElementById('modal-likes').innerText = photo.total_likes;
    document.getElementById('modal-user').innerText = photo.user_name;
    let avatarUrl = photo.avatar ? 'uploads/avatars/' + photo.avatar : 'https://ui-avatars.com/api/?background=6366f1&color=fff&name=' + encodeURIComponent(photo.user_name);
    document.getElementById('modal-avatar').src = avatarUrl;
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modal').classList.add('flex');
}
function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.getElementById('modal').classList.remove('flex');
}

document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.stopPropagation();
        const photoId = this.dataset.id;
        const likeSpan = document.getElementById('like-count-' + photoId);
        const carouselSpan = document.querySelector('.carousel-like-' + photoId);
        
        const formData = new FormData();
        formData.append('photo_id', photoId);
        
        const response = await fetch('like_ajax.php', {
            method: 'POST',
            body: formData
        });
        const newCount = await response.text();
        
        if(likeSpan) likeSpan.innerText = newCount;
        if(carouselSpan) carouselSpan.innerText = newCount;
        
        let rect = this.getBoundingClientRect();
        let heart = document.createElement('div');
        heart.innerHTML = '❤️';
        heart.style.position = 'fixed';
        heart.style.left = rect.left + rect.width/2 + 'px';
        heart.style.top = rect.top + rect.height/2 + 'px';
        heart.style.fontSize = '30px';
        heart.style.opacity = '1';
        heart.style.transition = 'all 0.5s ease';
        heart.style.pointerEvents = 'none';
        heart.style.zIndex = '9999';
        document.body.appendChild(heart);
        setTimeout(() => {
            heart.style.transform = 'translateY(-50px)';
            heart.style.opacity = '0';
            setTimeout(() => heart.remove(), 500);
        }, 10);
    });
});

let current = 0;
const track = document.getElementById('carousel-track');
const slides = track ? track.children.length : 0;
if(track && slides > 0) {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    if(prevBtn) {
        prevBtn.addEventListener('click', () => {
            current = (current - 1 + slides) % slides;
            track.style.transform = `translateX(-${current * 100}%)`;
        });
    }
    if(nextBtn) {
        nextBtn.addEventListener('click', () => {
            current = (current + 1) % slides;
            track.style.transform = `translateX(-${current * 100}%)`;
        });
    }
    setInterval(() => {
        current = (current + 1) % slides;
        track.style.transform = `translateX(-${current * 100}%)`;
    }, 5000);
}
</script>

<?php include 'includes/footer.php'; ?>