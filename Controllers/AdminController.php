<?php
require_once '../Models/Admin.php';

class AdminController {
    private $adminModel;
    
    public function __construct() {
        $this->adminModel = new Models\Admin();
    }
    
    /**
     * Authenticate admin login
     *
     * @param string $username Admin username
     * @param string $password Admin password
     * @return bool True if login successful, false otherwise
     */
    public function login(string $username, string $password): bool {
        $this->adminModel->Username = $username;
        $this->adminModel->Password = $password;
        
        return $this->adminModel->login();
    }
    
    /**
     * Get admin by ID
     *
     * @param int $adminId Admin ID
     * @return Models\Admin|null Admin object or null if not found
     */
    public function getAdminById(int $adminId): ?Models\Admin {
        return $this->adminModel->getAdminById($adminId);
    }
    
    /**
     * Get all admins
     *
     * @return array Array of Admin objects
     */
    public function getAllAdmins(): array {
        return $this->adminModel->getAllAdmins();
    }
    
    /**
     * Update admin profile
     *
     * @param int $adminId Admin ID
     * @param array $data Admin data to update
     * @return bool True if update successful, false otherwise
     */
    public function updateAdminProfile(int $adminId, array $data): bool {
        $admin = $this->adminModel->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        // Update admin properties
        if (isset($data['username'])) {
            $admin->Username = $data['username'];
        }
        
        if (isset($data['password'])) {
            // Hash password before storing
            $admin->Password = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (isset($data['role'])) {
            $admin->Role = $data['role'];
        }
        
        return $admin->updateProfile();
    }
    
    /**
     * Check if admin has permission to manage fees
     *
     * @param int $adminId Admin ID
     * @return bool True if admin has permission, false otherwise
     */
    public function canManageFees(int $adminId): bool {
        $admin = $this->adminModel->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        return $admin->manageFees();
    }
    
    /**
     * Check if admin has permission to approve opportunities
     *
     * @param int $adminId Admin ID
     * @return bool True if admin has permission, false otherwise
     */
    public function canApproveOpportunity(int $adminId): bool {
        $admin = $this->adminModel->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        return $admin->approveOpportunity();
    }
    
    /**
     * Check if admin has permission to suspend users
     *
     * @param int $adminId Admin ID
     * @return bool True if admin has permission, false otherwise
     */
    public function canSuspendUser(int $adminId): bool {
        $admin = $this->adminModel->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        return $admin->suspendUser();
    }
    
    /**
     * Check if admin has permission to manage reports
     *
     * @param int $adminId Admin ID
     * @return bool True if admin has permission, false otherwise
     */
    public function canManageReports(int $adminId): bool {
        $admin = $this->adminModel->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        return $admin->manageReports();
    }
    
    /**
     * Check if admin has permission to manage FAQ
     *
     * @param int $adminId Admin ID
     * @return bool True if admin has permission, false otherwise
     */
    public function canManageFAQ(int $adminId): bool {
        $admin = $this->adminModel->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        return $admin->faqSupport();
    }
    
    /**
     * Check if admin has permission to reset passwords
     *
     * @param int $adminId Admin ID
     * @return bool True if admin has permission, false otherwise
     */
    public function canResetPassword(int $adminId): bool {
        $admin = $this->adminModel->getAdminById($adminId);
        
        if (!$admin) {
            return false;
        }
        
        return $admin->resetPassword();
    }
}