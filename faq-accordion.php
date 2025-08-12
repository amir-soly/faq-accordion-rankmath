<?php
/*
Plugin Name: FAQ Accordion from RankMath Schema
Description: نمایش FAQ رنک مث به صورت آکوردیون با تنظیمات سفارشی استایل در پنل مدیریت.
Version:     1.0
Author:      amir soli
License:     GPLv2 or later
Text Domain: faq-accordion-rankmath
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // محافظت در برابر دسترسی مستقیم
}

// بارگذاری کلاس اصلی افزونه
require_once plugin_dir_path(__FILE__) . 'includes/class-faq-accordion.php';

// راه‌اندازی افزونه
function faq_accordion_run() {
    $plugin = new FAQ_Accordion();
    $plugin->run();
}
faq_accordion_run();
