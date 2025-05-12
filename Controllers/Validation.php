<?php

class Validation {

    // Start session (only if not already started)
    public static function session(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Check if all values in the associative array are set (not null)
    public static function areFieldsSet(array $fields): bool {
        foreach ($fields as $key => $value) {
            if (!isset($value)) {
                return false;
            }
        }
        return true;
    }

    // Check if all values in the associative array are not empty (after trimming)
    public static function areFieldsNotEmpty(array $fields): bool {
        foreach ($fields as $key => $value) {
            if (empty(trim($value))) {
                return false;
            }
        }
        return true;
    }
}
?>
