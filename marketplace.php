<?php 
include 'db.php'; 

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

// Search & Filter Logic
$query = "SELECT p.*, u.company_name FROM products p JOIN users u ON p.seller_id = u.id WHERE 1=1";
$params = [];

if(!empty($_GET['search'])) {
    $query .= " AND (p.title LIKE ? OR p.category LIKE ?)";
    $params[] = "%".$_GET['search']."%";
    $params[] = "%".$_GET['search']."%";
}

if(!empty($_GET['category'])) {
    $query .= " AND p.category = ?";
    $params[] = $_GET['category'];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Global Marketplace | Browse Wholesale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .product-card { transition: all 0.3s ease; border: 1px solid #f1f5f9; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); border-color: #fdba74; }
        .sidebar-link:hover { background: #fff7ed; color: #ea580c; }
    </style>
</head>
<body class="bg-gray-50 flex">

    <div class="w-72 bg-white h-screen sticky top-0 border-r p-6 hidden md:block">
        <h2 class="text-xl font-bold text-orange-600 mb-8 italic">GlobalTrade</h2>
        
        <form method="GET" action="marketplace.php">
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Keywords..." class="w-full border p-2 rounded-lg text-sm outline-orange-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Categories</label>
                <div class="space-y-2 text-sm text-gray-600">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="category" value="" checked> All Categories</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="category" value="Electronics"> Electronics</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="category" value="Fashion & Apparel"> Fashion & Apparel</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="category" value="Home & Furniture"> Home & Furniture</label>
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-2 rounded-lg font-bold hover:bg-black">Apply Filters</button>
        </form>
        
        <div class="mt-10 pt-10 border-t">
            <a href="buyer_dashboard.php" class="text-gray-500 hover:text-orange-600 font-medium">← Back to Dashboard</a>
        </div>
    </div>

    <div class="flex-1 p-8">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-slate-800">Global Sources</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">Welcome, <b><?= $_SESSION['fullname'] ?></b></span>
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold border border-orange-200">
                    <?= substr($_SESSION['fullname'], 0, 1) ?>
                </div>
            </div>
        </header>

        <?php if(count($products) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach($products as $p): ?>
                <div class="bg-white rounded-2xl overflow-hidden product-card group">
                    <div class="h-56 bg-gray-100 overflow-hidden relative">
                        <?php 
                            $img_path = $p['image_url'];
                            // If it's a local file and exists, use it, otherwise use placeholder
                            if(!file_exists($img_path)) { $img_path = 'https://via.placeholder.com/400x300?text=No+Image'; }
                        ?>
                        <img src="<?= $img_path ?>" alt="<?= $p['title'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-2 left-2 bg-white/90 backdrop-blur px-2 py-1 rounded text-[10px] font-bold uppercase text-gray-600">
                            <?= $p['category'] ?>
                        </div>
                    </div>

                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 text-lg mb-1 truncate"><?= $p['title'] ?></h3>
                        <p class="text-xs text-gray-400 mb-3">Supplier: <span class="text-orange-500 font-medium"><?= $p['company_name'] ?? 'Verified Factory' ?></span></p>
                        
                        <div class="flex items-baseline gap-1 mb-4">
                            <span class="text-2xl font-black text-slate-900">$<?= number_format($p['price_per_unit'], 2) ?></span>
                            <span class="text-xs text-gray-400 font-bold">/ Piece</span>
                        </div>

                        <div class="bg-slate-50 p-2 rounded-lg mb-4">
                            <p class="text-[11px] text-gray-500">Min. Order: <b class="text-slate-800"><?= $p['moq'] ?> Pieces</b></p>
                        </div>

                        <button onclick="window.location.href='product_view.php?id=<?= $p['id'] ?>'" class="w-full border-2 border-slate-900 text-slate-900 font-bold py-2 rounded-xl hover:bg-slate-900 hover:text-white transition">
                            Contact Supplier
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed">
                <p class="text-gray-400 text-lg italic">No products found matching your search.</p>
                <a href="marketplace.php" class="text-orange-600 font-bold underline">Clear all filters</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
