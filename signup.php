<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join GlobalTrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-orange-600 mb-6">Create Account</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Full Name</label>
                <input type="text" name="fullname" class="w-full border p-3 rounded mt-1 outline-orange-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email Address</label>
                <input type="email" name="email" class="w-full border p-3 rounded mt-1 outline-orange-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full border p-3 rounded mt-1 outline-orange-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700">Register As</label>
                <select name="role" class="w-full border p-3 rounded mt-1">
                    <option value="buyer">Buyer (Sourcing)</option>
                    <option value="seller">Seller (Supplier)</option>
                </select>
            </div>
            <button name="register" class="w-full bg-orange-600 text-white p-4 rounded-lg font-bold hover:bg-orange-700">Sign Up</button>
        </form>
    </div>

    <?php
    if(isset($_POST['register'])){
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        if($stmt->execute([$_POST['fullname'], $_POST['email'], $hash, $_POST['role']])){
            echo "<script>window.location.href='login.php';</script>";
        }
    }
    ?>
</body>
</html>
