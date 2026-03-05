<?php 
include 'db.php'; 

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$buyer_id = $_SESSION['user_id'];
$view = $_GET['page'] ?? 'home'; // Check if we are on 'home' or 'orders'

// Fetch inquiries/orders for the 'orders' view
$inquiries = [];
if($view == 'orders' || $view == 'home') {
    $stmt = $pdo->prepare("SELECT i.*, p.title as product_name, p.image_url, u.company_name 
                          FROM inquiries i 
                          JOIN products p ON i.product_id = p.id 
                          JOIN users u ON i.seller_id = u.id 
                          WHERE i.buyer_id = ? 
                          ORDER BY i.created_at DESC");
    $stmt->execute([$buyer_id]);
    $inquiries = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buyer Hub | <?= ($view == 'orders') ? 'My Orders' : 'Dashboard' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar-link:hover { background-color: #fff7ed; color: #ea580c; transition: 0.3s; }
        .active-link { background-color: #fff7ed; color: #ea580c; font-weight: bold; border-left: 4px solid #f97316; }
        
        #payment-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px); z-index: 100; align-items: center; justify-content: center;
            flex-direction: column; color: white;
        }
        .success-checkmark { width: 80px; height: 80px; stroke: #4bb543; animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both; }
        @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 40px #4bb543; } }
    </style>
</head>
<body class="bg-gray-50 flex">

    <div id="payment-overlay">
        <svg class="success-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle cx="26" cy="26" r="25" fill="none" stroke="#4bb543" stroke-width="2"/>
            <path fill="none" stroke="#fff" stroke-width="4" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
        </svg>
        <h2 class="text-2xl font-bold mt-4">Payment Successful!</h2>
    </div>

    <div class="w-64 bg-white border-r h-screen p-6 sticky top-0 shadow-sm flex flex-col">
        <h2 class="text-2xl font-black text-orange-600 mb-10 italic">GlobalTrade</h2>
        <nav class="space-y-1 flex-1">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Market</p>
            <a href="buyer_dashboard.php?page=home" class="block p-3 text-gray-600 rounded-xl sidebar-link <?= ($view == 'home') ? 'active-link' : '' ?>">🏠 Dashboard</a>
            <a href="marketplace.php" class="block p-3 text-gray-600 rounded-xl sidebar-link">📦 Browse Products</a>
            
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-8 mb-4">Purchases</p>
            <a href="buyer_dashboard.php?page=orders" class="block p-3 text-gray-600 rounded-xl sidebar-link <?= ($view == 'orders') ? 'active-link' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Orders By Me
            </a>
        </nav>
        <div class="border-t pt-4">
            <a href="logout.php" class="block p-3 text-red-500 hover:bg-red-50 rounded-xl transition font-bold">🚪 Logout</a>
        </div>
    </div>

    <div class="flex-1 p-10">
        <?php if($view == 'home'): ?>
            <header class="mb-10">
                <h1 class="text-3xl font-black text-slate-800">Welcome, <?= $_SESSION['fullname'] ?></h1>
                <p class="text-gray-500">Explore the global market or track your recent activity.</p>
            </header>
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-3xl border shadow-sm">
                    <h3 class="text-lg font-bold">Active Requests</h3>
                    <p class="text-4xl font-black text-orange-600 mt-2"><?= count($inquiries) ?></p>
                    <a href="buyer_dashboard.php?page=orders" class="text-sm text-blue-500 mt-4 block">View details →</a>
                </div>
                <div class="bg-slate-900 p-8 rounded-3xl text-white">
                    <h3 class="text-lg font-bold">Global Sourcing</h3>
                    <p class="text-slate-400 mt-2">Find new suppliers and products today.</p>
                    <button onclick="window.location.href='marketplace.php'" class="mt-4 bg-orange-600 px-6 py-2 rounded-lg font-bold">Go to Market</button>
                </div>
            </div>

        <?php elseif($view == 'orders'): ?>
            <header class="mb-10">
                <h1 class="text-3xl font-black text-slate-800">Orders By Me</h1>
                <p class="text-gray-500">A dedicated list of all your purchase requests and statuses.</p>
            </header>
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr class="text-gray-400 text-[11px] uppercase tracking-widest">
                            <th class="p-6">Product</th>
                            <th>Agreed Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($inquiries as $inq): ?>
                        <tr>
                            <td class="p-6 flex items-center gap-4">
                                <img src="<?= $inq['image_url'] ?>" class="w-12 h-12 rounded-lg object-cover border">
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($inq['product_name']) ?></span>
                            </td>
                            <td class="font-black">$<?= number_format($inq['proposed_price'], 2) ?></td>
                            <td>
                                <?php if($inq['status'] == 'pending'): ?>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-[10px] font-bold uppercase">Pending</span>
                                <?php elseif($inq['status'] == 'accepted'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase">Accepted</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($inq['status'] == 'accepted'): ?>
                                    <button onclick="triggerPaymentAnimation()" class="bg-slate-900 text-white px-4 py-1 rounded-lg text-xs font-bold">PAY NOW</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function triggerPaymentAnimation() {
            const overlay = document.getElementById('payment-overlay');
            overlay.style.display = 'flex';
            setTimeout(() => { overlay.style.display = 'none'; }, 3000);
        }
    </script>
</body>
</html>
