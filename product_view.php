<?php 
include 'db.php'; 

// 1. Get Product ID from URL
if(!isset($_GET['id'])) {
    echo "<script>window.location.href='marketplace.php';</script>";
    exit();
}

$product_id = $_GET['id'];

// 2. Fetch Product & Seller Details
$stmt = $pdo->prepare("SELECT p.*, u.fullname as seller_name, u.company_name, u.id as seller_id 
                       FROM products p 
                       JOIN users u ON p.seller_id = u.id 
                       WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if(!$product) {
    echo "<h1 style='text-align:center; margin-top:50px;'>Product Not Found</h1>";
    exit();
}

// 3. Handle Inquiry Submission
if(isset($_POST['send_inquiry'])) {
    $buyer_id = $_SESSION['user_id'];
    $seller_id = $product['seller_id'];
    $message = $_POST['message'];
    $proposed_price = $_POST['proposed_price'];

    $ins = $pdo->prepare("INSERT INTO inquiries (product_id, buyer_id, seller_id, message, proposed_price) VALUES (?, ?, ?, ?, ?)");
    if($ins->execute([$product_id, $buyer_id, $seller_id, $message, $proposed_price])) {
        echo "<script>alert('Inquiry Sent Successfully!'); window.location.href='buyer_dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $product['title'] ?> | GlobalTrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .product-image-box { background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 20px; }
        .inquiry-card { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 20px; }
    </style>
</head>
<body class="bg-gray-50">

<nav class="bg-white p-4 border-b">
    <div class="container mx-auto flex justify-between items-center">
        <a href="marketplace.php" class="text-orange-600 font-bold text-xl">← Back to Marketplace</a>
        <span class="text-gray-400">Product ID: #<?= $product['id'] ?></span>
    </div>
</nav>

<div class="container mx-auto py-12 px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        
        <div>
            <div class="product-image-box shadow-xl mb-8">
                <img src="<?= $product['image_url'] ?>" alt="<?= $product['title'] ?>" class="w-full rounded-xl object-contain h-[450px]">
            </div>
            
            <h2 class="text-2xl font-bold text-slate-800 mb-4">Product Description</h2>
            <div class="prose text-gray-600">
                <?= nl2br($product['description']) ?>
            </div>
        </div>

        <div class="space-y-6">
            <h1 class="text-4xl font-black text-slate-900"><?= $product['title'] ?></h1>
            <p class="text-gray-500">Category: <span class="text-orange-600 font-bold"><?= $product['category'] ?></span></p>
            
            <div class="flex items-center gap-6 py-6 border-y border-gray-200">
                <div>
                    <p class="text-sm text-gray-400 uppercase font-bold">Wholesale Price</p>
                    <p class="text-4xl font-black text-slate-900">$<?= number_format($product['price_per_unit'], 2) ?><span class="text-lg text-gray-400">/pc</span></p>
                </div>
                <div class="border-l pl-6">
                    <p class="text-sm text-gray-400 uppercase font-bold">Min Order (MOQ)</p>
                    <p class="text-2xl font-bold text-slate-700"><?= $product['moq'] ?> Pieces</p>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                <p class="text-blue-800 font-bold">Supplier Information</p>
                <p class="text-blue-600 text-sm">Sold by: <?= $product['company_name'] ?? $product['seller_name'] ?></p>
            </div>

            <div class="inquiry-card p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6 text-slate-800 italic underline decoration-orange-500">Send Inquiry to Supplier</h3>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-600 mb-1">Your Proposed Price ($)</label>
                        <input type="number" step="0.01" name="proposed_price" value="<?= $product['price_per_unit'] ?>" class="w-full p-3 rounded-lg border focus:ring-2 focus:ring-orange-500 outline-none" required>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-600 mb-1">Detailed Requirements</label>
                        <textarea name="message" rows="4" placeholder="Ask about shipping, customization, or lead times..." class="w-full p-3 rounded-lg border focus:ring-2 focus:ring-orange-500 outline-none" required></textarea>
                    </div>

                    <?php if($_SESSION['role'] == 'buyer'): ?>
                        <button name="send_inquiry" class="w-full bg-orange-600 text-white font-black py-4 rounded-xl shadow-lg hover:bg-orange-700 transition transform hover:scale-[1.02]">
                            SEND QUOTATION REQUEST
                        </button>
                    <?php else: ?>
                        <div class="bg-gray-200 text-gray-500 p-4 rounded-lg text-center font-bold">
                            Sellers cannot send inquiries.
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>
