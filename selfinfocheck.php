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
        
        <!--檢查是否提交信箱-->
        <div class="email-display">
            <img src="email-icon.jpg" alt="信箱圖示" class="email-icon"> <!-- 顯示信箱圖示 -->
            <span id="email-display">
                <?php 
                    // 顯示從第一頁提交過來的電子信箱
                    if (isset($_POST['email'])) {
                        echo htmlspecialchars($_POST['email']);  // 安全地顯示用戶輸入的信箱
                    } else {
                        echo "您沒有提供信箱";
                    }
                ?>
            </span>
        </div>

        <p>我們將把驗證碼發送到您的信箱，請查收並輸入以下驗證碼。</p>
        
        <!-- 驗證碼表單 -->
        <form action="showinfo.php" method="post">
            <label for="verification-code">輸入驗證碼：</label>
            <input type="text" id="verification-code" name="verification-code" required placeholder="請輸入驗證碼">
            <button type="submit" class="button">確認</button>
        </form>
    </div>
</body>
</html>
<?php

session_start();

// 引入 PHPMailer 和 Exception 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 使用 Composer 自動載入 PHPMailer
require 'vendor/autoload.php';

// 如果收到 POST 請求，處理電子信箱
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $first_name=$_POST['first-name'];
    $last_name=$_POST['last-name'];
    $phone=$_POST['phone'];
    $email = $_POST['email'];
    $birthday=$_POST['birthday'];
        //把前頁變數儲存供第三頁使用
        $_SESSION['first_name'] = $_POST['first-name'];
        $_SESSION['last_name'] = $_POST['last-name'];
        $_SESSION['phone'] = $_POST['phone'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['birthday'] = $_POST['birthday'];
    

    //使用 PHPMailer 發送郵件
    try {
        // 創建 PHPMailer 物件
        $mail = new PHPMailer(true);

        // 設定郵件伺服器
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 's1092008@gm.pu.edu.tw'; 
        $mail->Password = 'nstg auwi ptns ymye '; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('s1092008@gm.pu.edu.tw', '購物網站');
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
            echo '驗證成功！';
        } else {
            echo '驗證碼錯誤，請再試一次。';
        }
    }
}
?>
