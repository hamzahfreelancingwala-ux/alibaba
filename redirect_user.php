<?php
include 'db.php';
if(!isset($_SESSION['user_id'])){
    echo "<script>window.location.href='login.php';</script>";
} else {
    if($_SESSION['role'] == 'seller'){
        echo "<script>window.location.href='seller_dashboard.php';</script>";
    } else {
        echo "<script>window.location.href='buyer_dashboard.php';</script>";
    }
}
?>
