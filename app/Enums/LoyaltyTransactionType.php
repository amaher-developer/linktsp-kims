<?php

namespace App\Enums;

enum LoyaltyTransactionType: string
{
    case Earn = 'earn';
    case Redeem = 'redeem';
    case Refund = 'refund';
    case Reversal = 'reversal';
    case Bonus = 'bonus';
    case Adjustment = 'adjustment';
    case Expire = 'expire';
}
