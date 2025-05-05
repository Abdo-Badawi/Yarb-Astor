<?php

include_once '../Controllers/DBController.php';
include_once '../Controllers/TravelerProfileController.php';
include_once '../Models/User.php';

class Traveler {
    // Define the properties based on your table structure
    private $traveler_id;
    private $skill;
    private $language_spoken;
    private $preferred_language;
    private $joined_date;
    private $bio;
    private $rate;
    private $location;
    private $created_at;
    private $status;

    // Constructor to initialize the properties
    public function __construct($traveler_id = null, $skill = null, $language_spoken = null, $preferred_language = null,
                                $joined_date = null, $bio = null, $rate = null, $location = null, $created_at = null, $status = null) {
        $this->traveler_id = $traveler_id;
        $this->skill = $skill;
        $this->language_spoken = $language_spoken;
        $this->preferred_language = $preferred_language;
        $this->joined_date = $joined_date;
        $this->bio = $bio;
        $this->rate = $rate;
        $this->location = $location;
        $this->created_at = $created_at;
        $this->status = $status;
    }

    // Getters and Setters for each property
    public function getTravelerId() {
        return $this->traveler_id;
    }

    public function setTravelerId($traveler_id) {
        $this->traveler_id = $traveler_id;
    }

    public function getSkill() {
        return $this->skill;
    }

    public function setSkill($skill) {
        $this->skill = $skill;
    }

    public function getLanguageSpoken() {
        return $this->language_spoken;
    }

    public function setLanguageSpoken($language_spoken) {
        $this->language_spoken = $language_spoken;
    }

    public function getPreferredLanguage() {
        return $this->preferred_language;
    }

    public function setPreferredLanguage($preferred_language) {
        $this->preferred_language = $preferred_language;
    }

    public function getJoinedDate() {
        return $this->joined_date;
    }

    public function setJoinedDate($joined_date) {
        $this->joined_date = $joined_date;
    }

    public function getBio() {
        return $this->bio;
    }

    public function setBio($bio) {
        $this->bio = $bio;
    }

    public function getRate() {
        return $this->rate;
    }

    public function setRate($rate) {
        $this->rate = $rate;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($location) {
        $this->location = $location;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    // Optionally, you can add methods to convert the class to an associative array
    public function toArray() {
        return [
            'traveler_id' => $this->traveler_id,
            'skill' => $this->skill,
            'language_spoken' => $this->language_spoken,
            'preferred_language' => $this->preferred_language,
            'joined_date' => $this->joined_date,
            'bio' => $this->bio,
            'rate' => $this->rate,
            'location' => $this->location,
            'created_at' => $this->created_at,
            'status' => $this->status,
        ];
    }
}
?>
