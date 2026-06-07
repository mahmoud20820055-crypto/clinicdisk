<?php
// ============================================
// التكوينات العامة للتطبيق
// ============================================

// إعدادات التطبيق الأساسية
if (!defined('APP_NAME')) define('APP_NAME', 'ClinicDesk');
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/clinicdesk');
if (!defined('APP_URL')) define('APP_URL', 'http://localhost/clinicdesk');
if (!defined('DEFAULT_CONTROLLER')) define('DEFAULT_CONTROLLER', 'login');
if (!defined('ITEMS_PER_PAGE')) define('ITEMS_PER_PAGE', 10);

// المنطقة الزمنية
date_default_timezone_set('Asia/Amman');

// وضع التطوير (لإظهار الأخطاء)
if (!defined('DEVELOPMENT_MODE')) define('DEVELOPMENT_MODE', true);
?>