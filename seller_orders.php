<?php 
include 'db.php'; 

// Security: Only Sellers allowed
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch all inquiries/orders for this seller's products
$query = "SELECT i.*, p.title as product_name, p.image_url, u.fullname as buyer_name, u.email as buyer_email 
          FROM inquiries i 
          JOIN products p ON i.product_id = p.id 
          JOIN users u ON i.buyer_id = u.id 
          WHERE i.seller_id = ? 
          ORDER BY i.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll();

// Handle Status Updates (Accept/Reject)
if(isset($_GET['action']) && isset($_GET['id'])) {
    $new_status = ($_GET['action'] == 'accept') ? 'accepted' : 'rejected';
    $update = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ? AND seller_id = ?");
    $update->execute([$new_status, $_GET['id'], $seller_id]);
    echo "<script>window.location.href='seller_orders.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders | Seller Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .order-card { transition: 0.3s; border-left: 4px solid transparent; }
        .order-card:hover { border-left-color: #f97316; background-color: #fffaf5; }
        .status-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 12px; border-radius: 99px; }
    </style>
</head>
<body class="bg-slate-50 flex">

    <div class="w-64 bg-slate-900 min-h-screen p-6 text-white sticky top-0">
        <h2 class="text-2xl font-black text-orange-500 italic mb-10">SellerHub</h2>
        <nav class="space-y-4">
            <a href="seller_dashboard.php" class="block p-3 hover:bg-slate-800 rounded-lg">📊 Dashboard</a>
            <a href="add_product.php" class="block p-3 hover:bg-slate-800 rounded-lg">➕ Add Product</a>
            <a href="seller_orders.php" class="block p-3 bg-orange-600 rounded-lg font-bold">📦 Manage Orders</a>
            <a href="logout.php" class="block p-3 text-red-400 mt-20">🚪 Logout</a>
        </nav>
    </div>

    <div class="flex-1 p-10">
        <header class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">Order Inquiries</h1>
            <p class="text-slate-500">Review and respond to bulk purchase requests from buyers.</p>
        </header>

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border">
            <div class="p-6 border-b bg-slate-50 flex justify-between">
                <span class="font-bold text-slate-700">Recent Requests (<?= count($orders) ?>)</span>
            </div>

            <div class="divide-y">
                <?php if(count($orders) > 0): ?>
                    <?php foreach($orders as $order): ?>
                    <div class="p-6 order-card flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <img src="<?= $order['image_url'] ?>" class="w-16 h-16 rounded-lg object-cover bg-gray-100">
                            <div>
                                <h4 class="font-bold text-slate-800 text-lg"><?= $order['product_name'] ?></h4>
                                <p class="text-sm text-slate-500">Buyer: <span class="font-semibold text-slate-700"><?= $order['buyer_name'] ?></span> (<?= $order['buyer_email'] ?>)</p>
                                <div class="mt-2 text-xs bg-slate-100 p-2 rounded italic text-slate-600">
                                    "<?= htmlspecialchars($order['message']) ?>"
                                </div>
                            </div>
                        </div>

                        <div class="text-right flex flex-col items-end gap-3">
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-bold">Proposed Price</p>
                                <p class="text-xl font-black text-orange-600">$<?= number_format($order['proposed_price'], 2) ?></p>
                            </div>

                            <div>
                                <?php if($order['status'] == 'pending'): ?>
                                    <span class="status-badge bg-yellow-100 text-yellow-700">Pending Review</span>
                                    <div class="flex gap-2 mt-2">
                                        <button onclick="window.location.href='seller_orders.php?action=accept&id=<?= $order['id'] ?>'" class="bg-green-600 text-white px-4 py-1 rounded text-xs font-bold hover:bg-green-700">Accept</button>
                                        <button onclick="window.location.href='seller_orders.php?action=reject&id=<?= $order['id'] ?>'" class="bg-red-100 text-red-600 px-4 py-1 rounded text-xs font-bold hover:bg-red-200">Reject</button>
                                    </div>
                                <?php elseif($order['status'] == 'accepted'): ?>
                                    <span class="status-badge bg-green-100 text-green-700">✓ Accepted</span>
                                <?php else: ?>
                                    <span class="status-badge bg-red-100 text-red-600">Rejected</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-20 text-center">
                        <p class="text-slate-400 italic">No orders or inquiries found yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
