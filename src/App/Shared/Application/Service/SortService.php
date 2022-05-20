<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

class SortService
{
    function recursiveKsort(&$array): bool
    {
        foreach ($array as &$value) {
            if (is_array($value)) $this->recursiveKsort($value);
        }
        return ksort($array);
    }
}