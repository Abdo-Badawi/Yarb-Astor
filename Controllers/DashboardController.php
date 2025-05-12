<?php
require_once '../Models/Host.php';

class DashboardController {
    public function getHostDash($hostID): array {
        $host = new Host();
        return $host->getHostDashboardData($hostID);
    }
}

?>
