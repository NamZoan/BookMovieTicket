<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

$vnp_TmnCode = env('VNP_TMNCODE', ''); // Mã website tại VNPAY
$vnp_HashSecret = env('VNP_HASHSECRET', ''); // Chuỗi bí mật
$vnp_Url = env('VNP_URL', '');
$vnp_Returnurl = env('VNP_RETURNURL', '');
$vnp_ApiUrl = env('VNP_API_URL','');

$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
