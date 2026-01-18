<?php
// ==========================================
// 1. 配置中心 (已根据你提供的代码填入素材)
// ==========================================
$default_config = [
    'name' => '云盘分享',
    'color' => '#3b82f6', 
    'logo' => 'https://img.icons8.com/color/48/cloud-storage.png', 
    'guide' => '', 
    'app_name' => '网盘APP'
];

$pan_configs = [
    // --- 夸克网盘配置 (来自你刚刚发的代码) ---
    'quark' => [
        'keywords' => ['quark.cn', 'myquark'], // 识别关键词
        'name' => '夸克网盘',
        'color' => '#0d53ff', // 夸克蓝 (自动替换你代码里的 #3b82f6)
        'logo' => 'https://testlink11.oss-cn-beijing.aliyuncs.com/quark-logo.png', // 你的夸克Logo
        'guide' => 'https://testlink11.oss-cn-beijing.aliyuncs.com/quark-guide.png', // 你的夸克引导图
        'app_name' => '夸克APP'
    ],
    // --- 百度网盘配置 (来自你之前的代码) ---
    'baidu' => [
        'keywords' => ['baidu.com'], // 识别关键词
        'name' => '百度网盘',
        'color' => '#06a7ff', // 百度蓝
        'logo' => 'https://testlink11.oss-cn-beijing.aliyuncs.com/baidu-logo.png', // 你的百度Logo
        'guide' => 'https://testlink11.oss-cn-beijing.aliyuncs.com/baidu-guide.png', // 你的百度引导图
        'app_name' => '百度网盘APP'
    ],
    // --- 迅雷/UC 预留位 (你以后有了图片填这里) ---
    'uc' => [
        'keywords' => ['uc.cn'],
        'name' => 'UC网盘',
        'color' => '#ff8200',
        'logo' => 'https://gw.alicdn.com/tfs/TB12h.2f.z1gK0jSZLeXXb9kVXa-200-200.png',
        'guide' => '', 
        'app_name' => 'UC网盘APP'
    ]
];

// ==========================================
// 2. 核心逻辑 (勿动)
// ==========================================
$url = isset($_GET['url']) ? $_GET['url'] : '';
$file_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '分享文件';

// 简单的安全处理
$url = htmlspecialchars($url);

// 移动端检测
$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
$is_mobile = (strpos($agent, 'iphone') || strpos($agent, 'android') || strpos($agent, 'ipad'));

// 手机端直接跳转
if ($is_mobile && !empty($url)) {
    header("Location: " . $url);
    exit;
}

// 智能识别当前网盘
$current_theme = $default_config;
if (!empty($url)) {
    foreach ($pan_configs as $key => $conf) {
        foreach ($conf['keywords'] as $keyword) {
            if (strpos($url, $keyword) !== false) {
                $current_theme = $conf;
                break 2;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $file_name; ?> - 安全转存</title>
    <script src="https://cdn.bootcdn.net/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        /* 定义主题变量，实现一键换色 */
        :root {
            --theme-color: <?php echo $current_theme['color']; ?>;
        }

        /* 你的原始CSS样式保持不变，略作优化适配变量 */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Microsoft YaHei", sans-serif;
            background-color: #f5f7fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .topbar {
            position: absolute;
            top: 20px;
            left: 30px;
            display: flex;
            align-items: center;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .site-logo { width: 32px; height: 32px; margin-right: 10px; object-fit: contain; }

        .stage {
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .card {
            background: #fff;
            width: 360px;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            text-align: center;
        }

        h2 { font-size: 16px; color: #333; font-weight: normal; margin-bottom: 5px; 
             white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .sub { font-size: 14px; color: #666; margin-bottom: 20px; }
        
        /* 使用变量代替硬编码颜色 */
        .app-name { color: var(--theme-color); font-weight: bold; }

        .qr {
            width: 190px;
            height: 190px;
            margin: 0 auto 20px;
            padding: 8px;
            border: 1px solid #eee;
            border-radius: 12px;
        }
        .qr img { display: block; width: 100%; height: auto; }

        .hint { font-size: 14px; color: #333; line-height: 1.6; font-weight: bold; }
        .hint small { font-weight: normal; font-size: 12px; color: #888; display: block; margin-top: 4px; }
        .hint span { color: var(--theme-color); } /* 这里的 span 也会自动变色 */

        .guide-right { width: 380px; height: 500px; display: flex; align-items: center; justify-content: center;}
        .guide-img { max-width: 100%; max-height: 100%; object-fit: contain; }

        @media (max-width: 768px) {
            .guide-right { display: none; }
            .topbar { left: 50%; transform: translateX(-50%); }
        }
    </style>
</head>
<body>

    <div class="topbar">
        <img src="<?php echo $current_theme['logo']; ?>" alt="<?php echo $current_theme['name']; ?>" class="site-logo">
        <span><?php echo $current_theme['name']; ?></span>
    </div>

    <div class="stage">
        <div class="card" role="region" aria-label="资源卡片">
            <h2>资源名称：<?php echo $file_name; ?></h2>
            <div class="extract-code" style="margin-bottom:10px;color:#374151;font-size:14px;">
                提取码：<strong style="font-weight:700;color:#0b1220;">无</strong>
            </div>
            
            <div class="sub">
                打开 <span class="app-name"><?php echo $current_theme['app_name']; ?></span> - 点击搜索框相机 - 扫码
            </div>

            <div class="qr" id="qrcode"></div>

            <div class="hint">
                由于资源仅允许手机端访问<br>
                <small>请使用 <span style="color:var(--theme-color)"><?php echo $current_theme['app_name']; ?></span> 或浏览器扫码打开</small>
            </div>
        </div>

        <div class="guide-right" aria-hidden="true">
            <?php if(!empty($current_theme['guide'])): ?>
                <img src="<?php echo $current_theme['guide']; ?>" alt="引导图" class="guide-img">
            <?php endif; ?>
        </div>
    </div>

    <script type="text/javascript">
        var targetUrl = "<?php echo $url; ?>";
        if(targetUrl){
            new QRCode(document.getElementById("qrcode"), {
                text: targetUrl,
                width: 172,
                height: 172,
                colorDark : "<?php echo $current_theme['color']; ?>", // 二维码颜色也可以自动变！
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        } else {
            document.getElementById("qrcode").innerHTML = "<p style='padding-top:70px;color:#999;font-size:12px;'>链接为空</p>";
        }
    </script>

</body>
</html>
