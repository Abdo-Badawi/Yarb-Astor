<?php
/**
 * AdminDashboardController.php
 *
 * Controller for handling admin dashboard data and operations
 */

require_once '../Models/Admin.php';

class AdminDashboardController {
    private $adminModel;

    public function __construct() {
        // Use the correct Admin class from Models directory
        $this->adminModel = new Admin();
    }

    /**
     * Get all dashboard data for admin
     *
     * @return array Dashboard data including stats, activities, and pending items
     */
    public function getDashboardData() {
        return $this->adminModel->getDashboardData();
    }

    // Rest of the methods remain the same
    // ...
}
?>