<?php

namespace App\Support;

class GradeHelper
{
    public static function fromPercentage(?float $percentage): string
    {
        if ($percentage === null) {
            return 'N/A';
        }

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            $percentage >= 33 => 'E',
            default => 'F',
        };
    }
}
