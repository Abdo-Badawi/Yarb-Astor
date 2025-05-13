<?php
require_once '../Models/Host.php';

class DashboardController {
    public function getHostDash($hostID): array {
        // Remove the namespace prefix since we're including the file directly
        $host = new Host();
        $dashboardData = $host->getHostDashboardData($hostID);
        
        // Ensure all required keys exist
        $requiredKeys = ['stats', 'recentApplications', 'recentMessages', 'activeOpportunities'];
        foreach ($requiredKeys as $key) {
            if (!isset($dashboardData[$key])) {
                $dashboardData[$key] = [];
            }
        }
        
        // Ensure all stats keys exist
        $requiredStats = ['profileViews', 'activeApplications', 'unreadMessages', 'activeOpportunities'];
        foreach ($requiredStats as $stat) {
            if (!isset($dashboardData['stats'][$stat])) {
                $dashboardData['stats'][$stat] = 0;
            }
        }
        
        return $dashboardData;
    }
}

?>
