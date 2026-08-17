<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Branch;
use Modules\Catalog\Models\BranchHour;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Option;
use Modules\Catalog\Models\OptionGroup;
use Modules\Catalog\Models\Product;

class CatalogDatabaseSeeder extends Seeder
{
    /**
     * @return array{branch: Branch, category: Category, product: Product, optionGroup: OptionGroup}
     */
    public function run(): array
    {
        $branch = Branch::create([
            'foodics_id' => 1,
            'name_en' => 'KIMS Downtown',
            'name_ar' => 'كيمز وسط البلد',
            'code' => 'BR-001',
            'address' => '1 Tahrir Square',
            'city' => 'Cairo',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'phone' => '01000000001',
            'accepts_grab_go' => true,
            'accepts_dine_in' => true,
            'is_active' => true,
            'synced_at' => now(),
        ]);

        foreach (range(0, 6) as $day) {
            BranchHour::create([
                'branch_id' => $branch->id,
                'day_of_week' => $day,
                'open_time' => '08:00:00',
                'close_time' => '23:00:00',
                'is_closed' => false,
            ]);
        }

        $category = Category::create([
            'foodics_id' => 1,
            'parent_id' => null,
            'name_en' => 'Coffee',
            'name_ar' => 'قهوة',
            'sort_order' => 1,
            'is_active' => true,
            'synced_at' => now(),
        ]);

        $product = Product::create([
            'foodics_id' => 1,
            'category_id' => $category->id,
            'sku' => 'SKU-00001',
            'name_en' => 'Latte',
            'name_ar' => 'لاتيه',
            'description_en' => 'Espresso with steamed milk.',
            'description_ar' => 'إسبريسو مع حليب مبخر.',
            'base_price' => 65.00,
            'is_available' => true,
            'is_active' => true,
            'synced_at' => now(),
        ]);

        $product->branches()->attach($branch->id, ['is_available' => true]);

        $sizeGroup = OptionGroup::create([
            'foodics_id' => 1,
            'name_en' => 'Size',
            'name_ar' => 'الحجم',
            'min_select' => 1,
            'max_select' => 1,
            'is_required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Option::create([
            'option_group_id' => $sizeGroup->id,
            'foodics_id' => 1,
            'name_en' => 'Medium',
            'name_ar' => 'وسط',
            'price_delta' => 0,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Option::create([
            'option_group_id' => $sizeGroup->id,
            'foodics_id' => 2,
            'name_en' => 'Large',
            'name_ar' => 'كبير',
            'price_delta' => 10,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $product->optionGroups()->attach($sizeGroup->id, ['sort_order' => 1]);

        return ['branch' => $branch, 'category' => $category, 'product' => $product, 'optionGroup' => $sizeGroup];
    }
}
