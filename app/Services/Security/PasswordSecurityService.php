<?php

namespace App\Services\Security;

/**
 * Service dedicated to password security validation.
 *
 * This class is intentionally incomplete.
 * Students will implement ANSSI password rules here.
 */
class PasswordSecurityService
{
    /**
     * Validate password strength according to ANSSI.
     *
     * TODO (students):
     *  - Min length >= 12
     *  - Uppercase letter
     *  - Lowercase letter
     *  - Digit
     *  - Special character
     *  - Reject common passwords
     *
     * Current behavior → weak validation (voluntary...).
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 12) {
            $errors[] = 'Le mot de passe doit contenir au moins 12 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une lettre majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une lettre minuscule.';
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
        }

        return $errors;
    }
}
