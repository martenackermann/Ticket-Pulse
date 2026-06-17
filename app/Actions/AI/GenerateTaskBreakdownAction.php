<?php

namespace App\Actions\AI;

class GenerateTaskBreakdownAction
{
    /**
     * @return list<string>
     */
    public function execute(string $title): array
    {
        return [
            "Research requirements for {$title}",
            'Configure environment and dependencies',
            'Implement core logic and UI',
            'Write feature tests',
            'Review and refine implementation',
        ];
    }
}
