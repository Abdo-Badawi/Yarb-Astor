<?php
// Fix the include statements to use the correct paths
include_once __DIR__ . '/../Models/Traveler.php';
include_once __DIR__ . '/../Models/Host.php';
include_once __DIR__ . '/../Models/Admin.php';

class ProfileController {
    
    /**
     * View traveler profile data
     * 
     * @return array|bool Traveler profile data or false on failure
     */
    public function viewTravelerProfile() {
        $traveler = new Traveler();
        $travelerId = $_SESSION['userID'];
        $travelerProfile = $traveler->getTravelerData($travelerId);
        return $travelerProfile;
    }
    
    /**
     * Update traveler profile data
     * 
     * @param array $userData Updated profile data
     * @return bool Success status
     */
    public function updateTravelerProfile($userData) {
        $traveler = new Traveler();
        $travelerId = $_SESSION['userID'];
        $result = $traveler->updateTravelerProfile($travelerId, $userData);
        return $result;
    }
    
    /**
     * View host profile data
     * 
     * @return array|bool Host profile data or false on failure
     */
    public function viewHostProfile() {
        $host = new Host();
        $hostId = $_SESSION['userID'];
        $hostProfile = $host->getHostData($hostId);
        return $hostProfile;
    }
    
    /**
     * Update host profile data
     * 
     * @param array $userData Updated profile data
     * @return bool Success status
     */
    public function updateHostProfile($userData) {
        $host = new Host();
        $hostId = $_SESSION['userID'];
        $result = $host->updateHostProfile($userData);
        return $result;
    }
    
    /**
     * View admin profile data
     * 
     * @return array|bool Admin profile data or false on failure
     */
    public function viewAdminProfile() {
        $admin = new Admin();
        $adminId = $_SESSION['userID'];
        $adminProfile = $admin->getUserData($adminId);
        return $adminProfile;
    }

    /**
     * Update admin profile data
     * 
     * @param array $userData Updated profile data
     * @return bool Success status
     */
    public function updateAdminProfile($userData) {
        $admin = new Admin();
        $adminId = $_SESSION['userID'];
        $result = $admin->updateUserProfile($adminId, $userData);
        return $result;
    }
}
