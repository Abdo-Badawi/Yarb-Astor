<?php
    include_once '../Models/Traveler.php';
    include_once '../Models/Host.php';
    include_once '../Models/Admin.php';



    function viewHostProfile() {
        $host = new Host();
        $hostId = $_SESSION['userID'];
        $hostProfile = $host->getUserData($hostId);
        return $hostProfile;    
    }

    function updateHostProfile($userId, $userData) {
        $host = new Host();
        // Make sure we're using the correct method
        $result = $host->updateUserProfile($userId, $userData);
        return $result;        
    }

    function viewTravelerProfile() {
        $traveler = new Traveler();
        $travelerId = $_SESSION['userID'];
        $travelerProfile = $traveler->getUserData($travelerId);
        return $travelerProfile;
    }

    function updateTravelerProfile($userId, $userData) {
        $traveler = new Traveler();
        // Make sure we're using the correct method
        $result = $traveler->updateUserProfile($userId, $userData);
        return $result;        
    }


    function viewAdminProfile(){
        $admin = new Admin();
        $adminId = $_SESSION['userID'];
        $adminProfile = $admin->getUserData($adminId);
        return $adminProfile;
    }

    function updateAdminProfile($userData) {
        $admin = new Admin();
        $adminId = $_SESSION['userID'];
        $result = $admin->updateUserProfile($adminId, $userData);
        return $result;

    }
    
?>


