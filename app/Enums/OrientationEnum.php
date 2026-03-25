<?php

declare(strict_types=1);

namespace App\Enums;

enum OrientationEnum: string
{
    case STRAIGHT = 'straight';
    case GAY = 'gay';
    case BISEXUAL = 'bisexual';
    case OTHER = 'other';
}
