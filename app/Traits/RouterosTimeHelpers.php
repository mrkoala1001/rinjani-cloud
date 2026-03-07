<?php

namespace App\Traits;

trait RouterosTimeHelpers
{
    /**
     * Parse RouterOS time string (e.g. 1d 12h 30m) into seconds.
     */
    protected function parseRouterOSTime($time)
    {
        if (!$time || $time == '-') return 0;
        
        $time = strtolower(trim($time));
        
        // Handle colon format (hh:mm:ss or mm:ss)
        if (strpos($time, ':') !== false) {
            $parts = explode(':', $time);
            $count = count($parts);
            if ($count == 3) {
                return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
            } elseif ($count == 2) {
                return ($parts[0] * 60) + $parts[1];
            }
        }

        $totalSeconds = 0;
        
        // Match units: weeks (w), days (d), hours (h), minutes (m), seconds (s)
        preg_match_all('/(\d+)([wdhms])/', $time, $matches);
        
        if (empty($matches[0])) {
            // If no units, assume it's seconds
            return is_numeric($time) ? intval($time) : 0;
        }

        foreach ($matches[1] as $idx => $val) {
            $unit = $matches[2][$idx];
            switch ($unit) {
                case 'w': $totalSeconds += $val * 604800; break;
                case 'd': $totalSeconds += $val * 86400; break;
                case 'h': $totalSeconds += $val * 3600; break;
                case 'm': $totalSeconds += $val * 60; break;
                case 's': $totalSeconds += $val; break;
            }
        }
        
        return $totalSeconds;
    }
}
