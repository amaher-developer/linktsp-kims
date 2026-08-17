<?php

namespace App\Enums;

enum RewardRedemptionStatus: string
{
    case Pending = 'pending';
    case Redeemed = 'redeemed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}
