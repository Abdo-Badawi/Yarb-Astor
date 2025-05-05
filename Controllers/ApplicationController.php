<?php

// Include necessary files
include_once '../Controllers/DBController.php';
include_once '../Models/Application.php';
use Models\Application;

class ApplicationController {

    private $db;

    // Constructor to initialize DBController
    public function __construct() {
        $this->db = new DBController(); // Instantiate DBController to manage database connections
        $this->db->openConnection();
    }

    public function getApplicationByOpportunityID($hostID) {
        $query = "SELECT
            opportunity.title, 
            opportunity.opportunity_photo, 
            opportunity.category, 
            opportunity.location AS opportunity_location, 
            opportunity.start_date, 
            opportunity.end_date, 
            opportunity.requirements,
            opportunity.description,
            opportunity.created_at,


        
            applications.application_id,
            applications.traveler_id,
            applications.status,
            applications.applied_date,
            applications.comment,            
        
            users.email,
            users.gender,
            users.first_name,
            users.last_name,
            users.phone_number,
        
            traveler.language_spoken,
            traveler.rate,
            traveler.location AS traveler_location
        
            FROM opportunity
            
            JOIN applications ON opportunity.opportunity_id = applications.opportunity_id
            
            JOIN users ON applications.traveler_id = users.user_id
            
            JOIN traveler ON applications.traveler_id = traveler.traveler_id
            
            WHERE opportunity.host_id = ?";
    
    
        $params = [$hostID];
        $this->db->openConnection();
        $result = $this->db->selectPrepared($query, "s", $params); // "s" for string
        $this->db->closeConnection();
        return $result;
    }
    

}