<?php
// No namespace declaration - this class is in the global namespace
require_once __DIR__ . '/../Models/Host.php';

class HostController {
    private $hostModel;
    
    public function __construct() {
        // Remove the namespace prefix since we're not using namespaces
        $this->hostModel = new Host();
    }
    
    /**
     * Get host by ID
     * 
     * @param int $hostId Host ID
     * @return array|null Host data or null if not found
     */
    public function getHostById(int $hostId) {
        if ($hostId <= 0) {
            error_log("Invalid host ID in getHostById");
            return null;
        }
        
        return $this->hostModel->getHostById($hostId);
    }
    
    /**
     * Get all hosts
     * 
     * @return array Array of hosts
     */
    public function getAllHosts(): array {
        return $this->hostModel->getAllHosts();
    }
    
    /**
     * Update host profile
     * 
     * @param array $hostData Host data
     * @return bool True if update was successful, false otherwise
     */
    public function updateHostProfile(array $hostData): bool {
        if (empty($hostData['host_id'])) {
            error_log("Missing host ID in updateHostProfile");
            return false;
        }
        
        return $this->hostModel->updateHostProfile($hostData);
    }
    
    /**
     * Search hosts by criteria
     * 
     * @param array $criteria Search criteria
     * @return array Array of matching hosts
     */
    public function searchHosts(array $criteria): array {
        return $this->hostModel->searchHosts($criteria);
    }
}
?>
