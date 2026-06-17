<?php

namespace Database\Seeders;

use App\Models\FnbCategory;
use App\Models\FnbMenu;
use Illuminate\Database\Seeder;

class FnbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Makanan' => [
                ['name' => 'Nasi Goreng Artisan', 'price' => 45000.00, 'description' => 'Nasi goreng khas hotel dengan telur mata sapi, sate ayam, dan kerupuk.'],
                ['name' => 'Mie Goreng Spesial', 'price' => 40000.00, 'description' => 'Mie goreng dengan potongan ayam, bakso, telur dadar suwir, dan acar.'],
                ['name' => 'Club Sandwich', 'price' => 55000.00, 'description' => 'Sandwich tiga lapis dengan isi smoke beef, keju, ayam suwir, dan kentang goreng.'],
                ['name' => 'Sup Buntut Premium', 'price' => 85000.00, 'description' => 'Sup buntut sapi gurih dengan wortel, kentang, emping, dan sambal ijo.'],
                ['name' => 'Sirloin Steak', 'price' => 120000.00, 'description' => 'Daging sirloin panggang disajikan dengan kentang goreng dan sayuran segar.'],
            ],
            'Minuman' => [
                ['name' => 'Kopi Susu Aren', 'price' => 25000.00, 'description' => 'Kopi espresso dengan susu segar dan gula aren premium.'],
                ['name' => 'Ice Lychee Tea', 'price' => 25000.00, 'description' => 'Teh es manis dengan tambahan buah leci segar.'],
                ['name' => 'Orange Juice Fresh', 'price' => 30000.00, 'description' => 'Jus jeruk segar diperas murni tanpa pemanis buatan.'],
                ['name' => 'Mineral Water', 'price' => 15000.00, 'description' => 'Air mineral kemasan botol kaca 600ml.'],
            ],
            'Cemilan' => [
                ['name' => 'French Fries', 'price' => 25000.00, 'description' => 'Kentang goreng renyah disajikan dengan saus sambal dan mayones.'],
                ['name' => 'Garlic Bread', 'price' => 30000.00, 'description' => 'Roti panggang dengan mentega bawang putih dan taburan keju parmesan.'],
                ['name' => 'Pisang Keju Cokelat', 'price' => 25000.00, 'description' => 'Pisang goreng keju parut siram susu cokelat kental manis.'],
            ],
        ];

        foreach ($categories as $catName => $menus) {
            $category = FnbCategory::create(['name' => $catName]);

            foreach ($menus as $menu) {
                FnbMenu::create([
                    'category_id' => $category->id,
                    'name' => $menu['name'],
                    'price' => $menu['price'],
                    'description' => $menu['description'],
                    'active' => true,
                ]);
            }
        }
    }
}
