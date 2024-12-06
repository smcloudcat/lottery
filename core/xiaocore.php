<?php
//检测是否安装，这个安装功能是几百年前写的了（软件管理系统），所以可以有点和源码有点格格不入......
$lockFileName = 'install.lock';
if (file_exists($directoryPath . 'core/' .$lockFileName)) {
} else {
    echo '<script>function autoPopup() {if (confirm("你的网站没有安装锁，看起来是没安装吗？请前往安装吧")) {window.location.href = "install.php";}}window.onload = autoPopup;</script>';
    exit();
}

//连接数据库
require_once 'config.php';
$conn = new mysqli($host,$user,$pass,$db);

//cf的turnstile接口地址
$verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

//这个是首页信息获取用的，我把内容全部放admins了，主要是我对mysql有点不熟，这块写的有点。。。
$id = 1;
$checkinfo = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$checkinfo->bind_param("i", $id); // "i" 表示绑定的是整数类型
$checkinfo->execute();
$info = $checkinfo->get_result()->fetch_assoc();
$checkinfo->close();


//检查版本更新
$version = '1.1.1';

//开启session
session_start();

//发件function，我研究了十分钟的想出来的方法，调用phpmailer，感觉好极了，if是判断防止过定义的，不然得另起一个文件，有点麻烦了
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'src/Exception.php';
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';
if($emailuse){
    function configureMailer(PHPMailer $mail, array $info) {
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = 0;//1为开发模式
        $mail->isSMTP();
        $mail->Host = $info['stmp'];
        $mail->SMTPAuth = true;
        $mail->Username = $info['emailname'];
        $mail->Password = $info['emailpass'];
        $mail->SMTPSecure = $info['sec'];
        $mail->Port = $info['port'];
        $mail->setFrom($info['emailname'], $info['title']);
        $mail->addReplyTo($info['emailname'], 'info');
    }
function setRecipient(PHPMailer $mail, string $recipientEmail, string $recipientName = '收件人') {
    $mail->addAddress($recipientEmail, $recipientName);
}
function setEmailContent(PHPMailer $mail, string $subject, string $htmlBody, string $altBody = '') {
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;
    $mail->AltBody = $altBody ?: '如果邮件客户端不支持HTML则显示此内容';
}
function sendEmail(PHPMailer $mail) {
    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
function send($recipientEmail, $recipientName, $subject, $htmlBody, $altBody, $info) {
    try {
        $mail = new PHPMailer(true);
        configureMailer($mail, $info);
        setRecipient($mail, $recipientEmail, $recipientName);
        setEmailContent($mail, $subject, $htmlBody, $altBody);
        return sendEmail($mail);
    } catch (Exception $e) {
        return "邮件发送失败：" . $mail->ErrorInfo;
    }
}}

//随机验证码
if($codeuse){
function getcode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0;$i < $length;$i++) {
        $randomString .=$characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
}
