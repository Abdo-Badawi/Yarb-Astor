<?php
include_once '../Models/Application.php';

class ApplicationController {
    function getApplicationByOpportunityID($hostID){
        $application = new Application();
        $applications = $application->getApplicationByOpportunityID($hostID);
        return $applications;
    }

    function getAppByOppoID($applicationID){
        $application = new Application();
        $applicationData = $application->getApplicationByID($applicationID);
        return $applicationData;
    }

    function updateAppStat($applicationID, $status){
        $application = new Application();
        return $application->updateApplicationStatus($applicationID, $status);
    }

    function getAppByID($applicationID){
        $application = new Application();
        return $application->getApplicationByID($applicationID);
    }
}
?>
