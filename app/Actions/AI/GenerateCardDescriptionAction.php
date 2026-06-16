<?php

namespace App\Actions\AI;

class GenerateCardDescriptionAction
{
    public function execute(string $title): string
    {
        // In a real app, this would call an AI service via Laravel Boost
        return "Implement secure user authentication for {$title} using Laravel Sanctum. " .
               "Create login, registration, and session management functionality. " .
               "Include validation and authorization policies.";
    }
}
