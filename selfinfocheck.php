<?php
session_start();

// 引入 PHPMailer 和 Exception 
require_once __DIR__ . '/vendor/autoload.php';
require 'vendor/autoload.php';
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 使用 Composer 自動載入 PHPMailer

// 如果收到 POST 請求，處理電子信箱
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];

    // 儲存表單資料到 session
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['phone'] = $phone;
    $_SESSION['email'] = $email;
    $_SESSION['birthday'] = $birthday;

    // 使用 PHPMailer 發送郵件
    try {
        // 創建 PHPMailer 物件
        $mail = new PHPMailer(true);

        // 設定郵件伺服器
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USERNAME']; // 使用 $_ENV 來取得環境變數
        $mail->Password = $_ENV['MAIL_PASSWORD'];  // 讀取 .env 中的 MAIL_PASSWORD (請根據.env.example檔案自行更改寄件人密碼)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($_ENV['MAIL_USERNAME'], '購物網站');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = '電子郵件驗證碼';

        // 生成驗證碼並儲存至 session
        $verificationCode = mt_rand(1000, 9999);  // 生成 4 位數字驗證碼
        $_SESSION['verification_code'] = $verificationCode;  // 儲存驗證碼到 session

        // 設定郵件內容
        $mail->Body = '您的驗證碼是：' . $verificationCode;

        // 發送郵件
        $mail->send();
    } catch (Exception $e) {
        echo "郵件發送失敗：{$mail->ErrorInfo}";
    }
}

// 檢查表單是否提交並檢查 session 是否存在驗證碼
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification-code'])) { 
    if (isset($_SESSION['verification_code'])) {
        $userCode = $_POST['verification-code'];  // 取得使用者輸入的驗證碼

        // 檢查驗證碼是否正確
        if ($userCode == $_SESSION['verification_code']) {
            header('Location: showinfo.php');
            exit;
        } else {
           echo '<script>alert("驗證碼錯誤，請檢查輸入之驗證碼是否正確。")</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-tw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電子郵件驗證</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <div class="background"></div>
    <div class="form-container">
        <h1>電子郵件驗證</h1>
        
        <!-- 檢查是否提交信箱 -->
        <div class="email-display">
            <img src="email-icon.jpg" alt="信箱圖示" class="email-icon"> <!-- 顯示信箱圖示 -->
            <span id="email-display">
                <?php 
                // 顯示從 session 獲取的電子信箱
                if (isset($_SESSION['email'])) {
                    echo htmlspecialchars($_SESSION['email']);
                }
                ?>
            </span>
        </div>

        <p>我們將把驗證碼發送到您的信箱，請查收並輸入以下驗證碼。</p>
        
        <!-- 驗證碼表單 -->
        <form method="post">
            <label for="verification-code">輸入驗證碼：</label>
            <input type="text" id="verification-code" name="verification-code" required placeholder="請輸入驗證碼">
            <button type="submit" class="button">確認</button>
        </form>
    </div>
</body>
</html>
