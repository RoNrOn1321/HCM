<?php
// Simple test to check API endpoints without sessions
$_GET['type'] = 'department';

// Include the reports API directly
require_once 'api/reports.php';
?>