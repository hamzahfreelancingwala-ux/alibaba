<?php 
include 'db.php'; 

// Security Check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

// Logic to handle File Upload
$message = "";
if(isset($_POST['submit_product'])){
    $seller_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $moq = $_POST['moq'];
    $cat = $_POST['category'];
    
    // File upload handling
    $target_dir = "uploads/";
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image is valid
    if(move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (seller_id, title, description, price_per_unit, moq, category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if($stmt->execute([$seller_id, $title, $desc, $price, $moq, $cat, $target_file])){
            echo "<script>alert('Product Published Successfully with Image!'); window.location.href='seller_dashboard.php';</script>";
        }
    } else {
        $message = "Error uploading file. Please check folder permissions.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List New Product | GlobalTrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { background: #0f172a; min-height: 100vh; }
        .form-container { background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .input-box { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; margin-top: 5px; outline-color: #f97316; }
        
        /* Custom File Upload Styling */
        .file-upload-wrapper {
            border: 2px dashed #cbd5e1;
            padding: 30px;
            text-align: center;
            border-radius: 15px;
            cursor: pointer;
            transition: 0.3s;
            position: relative;
        }
        .file-upload-wrapper:hover { border-color: #f97316; background: #fff7ed; }
        #image-preview { 
            max-width: 100%; 
            max-height: 250px; 
            margin-top: 15px; 
            border-radius: 10px; 
            display: none; 
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body class="flex">

    <div class="sidebar w-64 p-6 text-white sticky top-0">
        <h2 class="text-2xl font-black text-orange-500 mb-10 italic">GlobalTrade</h2>
        <nav class="space-y-4">
            <a href="seller_dashboard.php" class="block p-3 hover:bg-slate-800 rounded-lg">📊 Dashboard</a>
            <a href="add_product.php" class="block p-3 bg-orange-600 rounded-lg font-bold">➕ Add Product</a>
            <a href="logout.php" class="block p-3 text-red-400 mt-20 hover:underline">🚪 Logout</a>
        </nav>
    </div>

    <div class="flex-1 p-12">
        <div class="max-w-4xl mx-auto form-container p-10">
            <h1 class="text-3xl font-bold text-slate-800">Upload Wholesale Product</h1>
            <p class="text-slate-500 mb-8">Fill in the details to list your product on the global market.</p>

            <?php if($message): ?>
                <div class="bg-red-100 text-red-600 p-4 rounded-lg mb-6"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-600">Product Name</label>
                            <input type="text" name="title" placeholder="e.g. 100% Cotton T-Shirts" class="input-box" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Price (USD)</label>
                                <input type="number" step="0.01" name="price" placeholder="5.00" class="input-box" required>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-600">MOQ</label>
                                <input type="number" name="moq" placeholder="50" class="input-box" required>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Category</label>
                            <select name="category" class="input-box">
                                <option>Electronics</option>
                                <option>Fashion & Apparel</option>
                                <option>Home & Furniture</option>
                                <option>Industrial Tools</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Product Description</label>
                            <textarea name="description" rows="5" placeholder="Mention material, sizes, shipping terms..." class="input-box" required></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600 mb-2 block">Product Showcase Image</label>
                        <div class="file-upload-wrapper" onclick="document.getElementById('file-input').click()">
                            <input type="file" id="file-input" name="product_image" accept="image/*" class="hidden" onchange="previewLocalImage(event)" required>
                            <div id="upload-placeholder">
                                <span class="text-4xl text-slate-300">📁</span>
                                <p class="text-slate-500 mt-2">Click to browse your files</p>
                                <p class="text-xs text-slate-400">JPG, PNG, WEBP allowed</p>
                            </div>
                            <img id="image-preview" src="#" alt="Preview">
                        </div>
                        
                        <button name="submit_product" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl mt-10 hover:bg-black transition-all shadow-lg">
                            Publish Product Now
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function previewLocalImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');

            reader.onload = function(){
                if(reader.readyState === 2){
                    preview.src = reader.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
