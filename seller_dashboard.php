<?php 
include 'db.php'; 

// Security: Ensure only sellers can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$seller_id = $_SESSION['user_id'];

// 1. Fetch Real Stats
// Count Products
$stmtProd = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ?");
$stmtProd->execute([$seller_id]);
$total_products = $stmtProd->fetchColumn();

// Count Pending Inquiries
$stmtInq = $pdo->prepare("SELECT COUNT(*) FROM inquiries WHERE seller_id = ? AND status = 'pending'");
$stmtInq->execute([$seller_id]);
$active_inquiries = $stmtInq->fetchColumn();

// Calculate Revenue (Sum of accepted proposed prices)
$stmtRev = $pdo->prepare("SELECT SUM(proposed_price) FROM inquiries WHERE seller_id = ? AND status = 'accepted'");
$stmtRev->execute([$seller_id]);
$revenue = $stmtRev->fetchColumn() ?: 0;

// 2. Fetch Product List for the table
$stmtList = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC");
$stmtList->execute([$seller_id]);
$my_products = $stmtList->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Control Panel | GlobalTrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar-link:hover { background-color: #1e293b; color: #f97316; transition: 0.3s; }
        .stat-card { border-top: 4px solid #f97316; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-slate-100 flex">

    <div class="w-64 bg-slate-900 h-screen text-white p-6 sticky top-0 shadow-xl">
        <h2 class="text-2xl font-black text-orange-500 italic border-b border-slate-700 pb-4">SellerHub</h2>
        <nav class="mt-8 space-y-2">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">Management</p>
            <a href="seller_dashboard.php" class="block p-3 bg-orange-600 rounded-xl font-bold">📊 Dashboard</a>
            <a href="add_product.php" class="block p-3 rounded-xl sidebar-link">➕ Add Product</a>
            <a href="seller_orders.php" class="block p-3 rounded-xl sidebar-link">📦 Orders & RFQs</a>
            
            <div class="pt-20">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">System</p>
                <a href="logout.php" class="block p-3 text-red-400 hover:bg-red-900/20 rounded-xl transition">Logout</a>
            </div>
        </nav>
    </div>

    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Welcome, <?= $_SESSION['fullname'] ?></h1>
                <p class="text-slate-500">Here is what's happening with your wholesale business today.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase">Status</p>
                    <p class="text-sm font-bold text-green-600">Verified Supplier</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold border border-orange-200">
                    <?= strtoupper(substr($_SESSION['fullname'], 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-8 rounded-2xl shadow-sm stat-card">
                <p class="text-gray-400 font-bold text-sm uppercase">Total Products</p>
                <h3 class="text-4xl font-black text-slate-800 mt-2"><?= $total_products ?></h3>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm stat-card">
                <p class="text-gray-400 font-bold text-sm uppercase">Active Inquiries</p>
                <h3 class="text-4xl font-black text-slate-800 mt-2"><?= $active_inquiries ?></h3>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm stat-card">
                <p class="text-gray-400 font-bold text-sm uppercase">Total Revenue</p>
                <h3 class="text-4xl font-black text-green-600 mt-2">$<?= number_format($revenue, 2) ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Your Live Listings</h3>
                <button onclick="window.location.href='add_product.php'" class="text-sm bg-slate-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-black transition">
                    + Post New Product
                </button>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-400 text-xs uppercase tracking-tighter">
                        <th class="p-6">Product Detail</th>
                        <th>Category</th>
                        <th>Price/Unit</th>
                        <th>MOQ</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(count($my_products) > 0): ?>
                        <?php foreach($my_products as $product): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <img src="<?= $product['image_url'] ?>" class="w-12 h-12 rounded-lg object-cover shadow-sm bg-white">
                                    <span class="font-bold text-slate-700"><?= $product['title'] ?></span>
                                </div>
                            </td>
                            <td class="text-sm text-slate-500"><?= $product['category'] ?></td>
                            <td class="font-black text-slate-900">$<?= number_format($product['price_per_unit'], 2) ?></td>
                            <td class="text-sm font-bold text-slate-600"><?= $product['moq'] ?> pcs</td>
                            <td>
                                <span class="bg-green-100 text-green-700 text-[10px] px-2 py-1 rounded-md font-black uppercase tracking-widest">Active</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-20 text-center text-slate-400 italic">
                                You haven't listed any products yet. 
                                <a href="add_product.php" class="text-orange-600 font-bold underline">Add your first product now!</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
