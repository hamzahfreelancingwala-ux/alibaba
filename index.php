<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GlobalTrade | World's Leading B2B Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hero-bg { background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%); }
        .feature-card:hover { border-color: #f97316; transform: translateY(-5px); transition: 0.3s; }
    </style>
</head>
<body class="bg-gray-50">

<nav class="bg-white py-4 shadow-sm border-b sticky top-0 z-50">
    <div class="container mx-auto px-6 flex justify-between items-center">
        <h1 class="text-3xl font-black text-orange-600 tracking-tighter italic">GlobalTrade</h1>
        <div class="space-x-8 font-medium text-gray-700">
            <a href="#features" class="hover:text-orange-600">Features</a>
            <a href="#solutions" class="hover:text-orange-600">Solutions</a>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="hover:text-orange-600">Sign In</a>
                <a href="signup.php" class="bg-orange-600 text-white px-6 py-2 rounded-full hover:bg-orange-700">Join for Free</a>
            <?php else: ?>
                <a href="redirect_user.php" class="bg-blue-600 text-white px-6 py-2 rounded-full">Go to Workspace</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero-bg py-24">
    <div class="container mx-auto px-6 grid md:grid-cols-2 items-center">
        <div>
            <span class="text-orange-600 font-bold uppercase tracking-widest text-sm">B2B Manufacturing</span>
            <h1 class="text-6xl font-extrabold text-slate-900 mt-4 leading-tight">Source from <br>Global Suppliers.</h1>
            <p class="text-xl text-gray-600 mt-6 max-w-md">One-stop sourcing solution for businesses. Verified suppliers, trade assurance, and bulk pricing.</p>
            <div class="mt-10 flex gap-4">
                <button onclick="window.location.href='signup.php'" class="bg-slate-900 text-white px-8 py-4 rounded-lg font-bold">Start Sourcing</button>
                <button class="border border-slate-900 px-8 py-4 rounded-lg font-bold">Sell Globally</button>
            </div>
        </div>
        <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=800" class="rounded-3xl shadow-2xl">
    </div>
</section>

<section id="features" class="py-20 bg-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold mb-16">Platform Features</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="feature-card p-10 border-2 rounded-3xl text-left">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-6">📦</div>
                <h3 class="text-xl font-bold mb-4">Bulk Management</h3>
                <p class="text-gray-500">Easily manage thousands of SKUs and wholesale price tiers.</p>
            </div>
            <div class="feature-card p-10 border-2 rounded-3xl text-left">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">💬</div>
                <h3 class="text-xl font-bold mb-4">Direct RFQ</h3>
                <p class="text-gray-500">Request for quotations and negotiate directly with factory owners.</p>
            </div>
            <div class="feature-card p-10 border-2 rounded-3xl text-left">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">🛡️</div>
                <h3 class="text-xl font-bold mb-4">Secure Payments</h3>
                <p class="text-gray-500">Escrow-style payment systems for safe cross-border transactions.</p>
            </div>
        </div>
    </div>
</section>

</body>
</html>
