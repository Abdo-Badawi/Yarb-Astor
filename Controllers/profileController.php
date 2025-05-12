<?php
    include_once '../Models/Traveler.php';
    include_once '../Models/Host.php';
    include_once '../Models/Admin.php';

    function viewHostProfile(){

    }

    function viewTravelerProfile(){
        $traveler = new Traveler();
        $travelerId = $_SESSION['userID'];
        $travelerProfile = $traveler->getUserData($travelerId);
        return $travelerProfile;
    }

    function updateTravelerProfile() {
        $traveler = new Traveler();
        $travelerId = $_SESSION['userID'];
        $travelerProfile = $traveler->getUserData($travelerId);
        return $travelerProfile;        
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


