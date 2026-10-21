<?php
require_once __DIR__ . '/../includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/');
csrf_check();
admin_logout();
redirect('admin/login.php');
