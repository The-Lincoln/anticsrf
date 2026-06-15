<?php
require_once 'classes/AntiCSRF.php';
$csrf = new AntiCSRF();
header('Content-Type: application/json');
echo json_encode(['token' => $csrf->generate()]);