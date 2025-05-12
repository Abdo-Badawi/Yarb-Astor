<?php
    include_once '../Models/Traveler.php';
    // include_once '../Models/Host.php';
    // include_once '../Models/Admin.php';

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

    }

?>
