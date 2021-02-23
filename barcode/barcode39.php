<?php
require('class/BCGFontFile.php');
require('class/BCGDrawing.php');
require('class/BCGcode39.barcode.php');

$code_sn = isset($_GET['code_sn'])?trim($_GET['code_sn']):'';
$check_sum = !empty($_GET['check_sum'])?true:false;

$font = new BCGFontFile('./font/Arial.ttf', 18);
$colorFront = new BCGColor(0, 0, 0);
$colorBack = new BCGColor(255, 255, 255);
$hideTxt = !empty($_GET['hideTxt'])?true:false;//是否 隐藏文本
// Barcode Part
$code = new BCGcode39();
$code->setScale(2);
$code->setThickness(30);
$code->setForegroundColor($colorFront);
$code->setBackgroundColor($colorBack);
$code->setFont($font);
$code->setChecksum($check_sum);
if($hideTxt){
    $code->setLabel(null);
}
$code->parse($code_sn);

// Drawing Part
$drawing = new BCGDrawing('', $colorBack);
$drawing->setBarcode($code);
$drawing->draw();

header('Content-Type: image/png');

$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>