<?php
// 開啟 session
date_default_timezone_set('Asia/Taipei');
session_start();

// 如果有來自第一頁的資料，儲存到 session


// 發送郵件驗證碼的邏輯
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
    // 取得 session 中的資料
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $phone = $_SESSION['phone'];
    $email = $_SESSION['email'];
    $birthday = $_SESSION['birthday'];
    $created_at = date('Y-m-d H:i:s');  // 當前時間


//檢查是否有資料
if (isset($_SESSION['first_name'])) {
    // 資料庫設定
    $servername = "localhost";  
    $username = "root";  
    $password = "";  
    $dbname = "memberinfo";  

    // 建立資料庫連線
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 檢查連線
    if ($conn->connect_error) {
        die("連線失敗: " . $conn->connect_error);
    }

    // 把第二頁資料拿過來
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $phone = $_SESSION['phone'];
    $email = $_SESSION['email'];
    $birthday = $_SESSION['birthday'];
    $created_at = date('Y-m-d H:i:s');  // 當前時間

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, phone, email, birthday, createtime) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $phone, $email, $birthday, $created_at);

    // 執行查詢並檢查結果
    if ($stmt->execute()) {
        echo "";
    } else {
        echo "<p>錯誤: " . $stmt->error . "</p>";
    }

    $stmt->close();

    // 關閉資料庫連線
    $conn->close();
} else {
    echo "無法獲取資料，請返回重新填寫資料。";
}
?>

<!DOCTYPE html>
<html lang="zh-tw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>資料提交成功</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="background"></div>

    <!-- 顯示資料處理的容器 -->
    <div class="form-container">
        <h1>資料提交結果</h1>


        <p><strong>資料預覽:</strong></p>
        <ul>
            <li><p class="p2">您的姓名: <?php echo htmlspecialchars($first_name." ".$last_name); ?></p></li>
            <li><p class="p2">您的手機: <?php echo htmlspecialchars($phone); ?></p></li>
            <li><p class="p2">您的信箱: <?php echo htmlspecialchars($email); ?></p></li>
            <li><p class="p2">您的生日: <?php echo htmlspecialchars($birthday); ?></p></li>
            <li><p class="p2">資料建立時間: <?php echo htmlspecialchars($created_at); ?></p></li>
        </ul>
        <p><strong>資料提交成功，謝謝您成為本購物平台會員!</strong></p>

        <a href="index.html"><button>返回首頁</button></a>
    </div>
</body>
</html>
