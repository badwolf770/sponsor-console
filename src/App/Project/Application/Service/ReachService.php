<?php
declare(strict_types=1);

namespace App\Project\Application\Service;

class ReachService
{
    public function generatePercentName(int $reach): string
    {
        return sprintf('Reach %s+,%%', $reach);
    }

    public function generateThousandName(int $reach): string
    {
        return sprintf('Reach %s+,000', $reach);
    }
}