<?php
require_once 'php/config.php';

// Destroy session and redirect to home page
session_destroy();
redirect('index.php');
?>