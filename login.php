<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In | GlobalTrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen">
    <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-md border border-gray-100">
        <h2 class="text-2xl font-bold text-center mb-8">Sign in to your account</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" class="w-full border p-4 mb-4 rounded-xl outline-orange-500" required>
            <input type="password" name="password" placeholder="Password" class="w-full border p-4 mb-6 rounded-xl outline-orange-500" required>
            <button name="login" class="w-full bg-slate-900 text-white p-4 rounded-xl font-bold hover:bg-black transition">Login</button>
        </form>
        <p class="text-center mt-6 text-gray-500">New here? <a href="signup.php" class="text-orange-600 font-bold">Register</a></p>
    </div>

    <?php
    if(isset($_POST['login'])){
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch();

        if($user && password_verify($_POST['password'], $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];
            // JS Redirection
            echo "<script>window.location.href='redirect_user.php';</script>";
        } else {
            echo "<script>alert('Invalid Credentials');</script>";
        }
    }
    ?>
</body>
</html>
