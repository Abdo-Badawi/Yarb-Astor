<?php
require_once '../Controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();
