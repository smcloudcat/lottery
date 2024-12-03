<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>小猫咪抽奖系统 - 后台管理</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <meta name="keywords" content="小猫咪抽奖系统,年会抽奖系统,节日抽奖系统,双十一活动,618活动,双十二活动">
    <meta name="description" content="小猫咪抽奖系统，一款开源免费的php抽奖系统，可用于年会抽奖，节日抽奖等等，支持自定义奖品概率和数量，页面简介美观，操作容易">
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../css/style.min.css" rel="stylesheet">
    <script src="https://cdn.lwcat.cn/jquery/jquery.js"></script>
</head>

<body>
<div class="container-fluid p-t-15">
    <!-- 页面标题 -->
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>后台管理</h4>
                </div>
                <div class="card-body">
                    <p>欢迎使用小猫咪抽奖系统后台管理</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>检测更新</h4>
                </div>
                <div class="card-body">
                    <div id="version-info"></div>
                </div>
            </div>
        </div>
    </div>

    </div>
<script>
        $(document).ready(function() {
            const currentVersion = '<?php echo $version; ?>';
            const updateUrl = 'https://lwcat.cn/lottery/update/index.php';

            $.ajax({
                url: updateUrl,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.version === currentVersion) {
                        $('#version-info').html(`当前版本：${currentVersion}<br>最新版本：${response.version}<br>当前已是最新版本`);
                    } else {
                        $('#version-info').html(`当前版本：${currentVersion}<br>最新版本：${response.version}<br>有新版本哦，请参考更新内容来考虑是否更新<br>本次更新内容：<br>${response.new}<br><a class='btn btn-primary' href='${response.url}'>下载最新版本</a>`);
                    }
                },
                error: function() {
                    $('#version-info').html("请求服务器失败，请联系作者");
                }
            });
        });
    </script>
<script type="text/javascript" src="../../js/jquery.min.js"></script>
<script type="text/javascript" src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/main.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>