<?php
header('Content-Type: application/json');
$emailuse=1;
$codeuse=1;
$directoryPath = '../../';
include("../../core/xiaocore.php");

$secretKey = '0x4AAAAAAA0fEw_NmKpEDWjPERyUYJQ2Ekc';

$code=getcode();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['act'] === 'send') {
    $email = $_POST['email'] ?? '';
    $cfResponse = $_POST['cf'] ?? '';

    if (empty($email)) {
        echo json_encode(['code' => 0, 'result' => '邮箱不能为空']);
        exit;
    }
    
    $allowedDomains = ['qq.com'];
    $emailDomain = substr(strrchr($email, "@"), 1);
    if (!in_array($emailDomain, $allowedDomains)) {
        echo json_encode(['code' => 0,'result' => '仅支持qq邮箱哦']);
        exit;
    }

    if ($info['cfcode'] == 1){
    $secretKey = $info['secretKey'];
    if (empty($cfResponse)) {
        echo json_encode(['code' => 0, 'result' => '验证码验证未完成，请完成验证后重试']);
        exit;
    }
    

    $data = [
        'secret' => $secretKey,
        'response' => $cfResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $cfResult = json_decode($response, true);
    if (empty($cfResult['success'])) {
        echo json_encode(['code' => 0, 'result' => '验证码验证失败，请重试']);
        exit;
    }
    }

    // 邮件发送逻辑
    if (send($email, $info['title'], "你的验证码", "你的验证码：".$code."<br>该验证码仅用于活动抽奖，如果不是本人操作请无视", "你的验证码：".$code."该验证码仅用于活动抽奖，如果不是本人操作请无视", $info)) {
       $_SESSION['emailcode'] = $code;

    echo json_encode(['code' => 1, 'result' => '验证码发送成功']);
    } else {
        echo json_encode(['code' => 0, 'result' => '邮件服务未响应']);
    }
}