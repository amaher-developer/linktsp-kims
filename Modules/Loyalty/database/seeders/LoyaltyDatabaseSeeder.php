<?php

namespace Modules\Loyalty\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Loyalty\Models\LoyaltyAccount;
use Modules\Loyalty\Models\LoyaltyRule;
use Modules\Loyalty\Models\LoyaltyTransaction;
use Modules\Loyalty\Models\Reward;

class LoyaltyDatabaseSeeder extends Seeder
{
    /**
     * @param  array{product: \Modules\Catalog\Models\Product, customer: \Modules\Ordering\Models\Customer, order: \Modules\Ordering\Models\Order}  $context
     */
    public function run(array $context): void
    {
        LoyaltyRule::create([
            'name' => 'Standard Earn & Redeem',
            'priority' => 0,
            'earn_points_rate' => 1,
            'earn_amount_unit' => 10,
            'redeem_points_unit' => 100,
            'redeem_value' => 10,
            'minimum_redeem_points' => 100,
            'is_active' => true,
        ]);

        Reward::create([
            'product_id' => $context['product']->id,
            'name_en' => 'Free Latte',
            'name_ar' => 'لاتيه مجاني',
            'points_cost' => 150,
            'reward_type' => 'product',
            'is_active' => true,
        ]);

        $loyaltyAccount = LoyaltyAccount::create([
            'customer_id' => $context['customer']->id,
            'status' => 'active',
        ]);

        LoyaltyTransaction::create([
            'loyalty_account_id' => $loyaltyAccount->id,
            'customer_id' => $context['customer']->id,
            'type' => 'earn',
            'points' => 6,
            'order_id' => $context['order']->id,
            'description' => 'Earned from order '.$context['order']->order_number,
        ]);
    }
}
