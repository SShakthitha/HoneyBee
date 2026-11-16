<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiftDesignSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Gift & Frame products ──────────────────────────────────────────
        // Delete and reinsert every time so re-running always gives fresh data.
        DB::table('gift_designs')->delete();

        DB::table('gift_designs')->insert([

            // ── Gifts ──────────────────────────────────────────────────────
            [
                'item_name'            => 'Personalised Name Gift Box',
                'category'             => 'Gifts',
                'material'             => 'Cardboard & Ribbon',
                'size'                 => 'Medium',
                'price'                => 1200.00,
                'offer_price'          => null,
                'customization_option' => 'Name & Message',
                'description'          => 'A beautifully wrapped personalised gift box with custom name and ribbon.',
                'image'                => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?w=800&q=80',
                'stock_quantity'       => 20,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'item_name'            => 'Custom Gift Hamper',
                'category'             => 'Gifts',
                'material'             => 'Wicker Basket',
                'size'                 => 'Large',
                'price'                => 2500.00,
                'offer_price'          => 2000.00,
                'customization_option' => 'Contents & Card',
                'description'          => 'A curated gift hamper filled with premium treats, personalised just for your loved one.',
                'image'                => 'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=800&q=80',
                'stock_quantity'       => 15,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'item_name'            => 'Ribbon Wrapped Gift Set',
                'category'             => 'Gifts',
                'material'             => 'Gift Wrap & Ribbon',
                'size'                 => 'Small',
                'price'                => 950.00,
                'offer_price'          => null,
                'customization_option' => 'Message Tag',
                'description'          => 'An elegant ribbon-wrapped gift set, perfect for birthdays, anniversaries, and celebrations.',
                'image'                => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=800&q=80',
                'stock_quantity'       => 30,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'item_name'            => 'Handcrafted Keepsake Box',
                'category'             => 'Gifts',
                'material'             => 'Wood & Velvet',
                'size'                 => 'Small',
                'price'                => 1800.00,
                'offer_price'          => null,
                'customization_option' => 'Engraved Name',
                'description'          => 'A handcrafted wooden keepsake box lined with velvet — ideal as a memory gift.',
                // Wooden jewellery/keepsake box — gift shop item
                'image'                => 'https://images.unsplash.com/photo-1619683618983-b22a4c3f0e37?w=800&q=80',
                'stock_quantity'       => 10,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],

            // ── Frames ─────────────────────────────────────────────────────
            [
                'item_name'            => 'Classic Wooden Photo Frame',
                'category'             => 'Frame Designs',
                'material'             => 'Natural Wood',
                'size'                 => '5x7 inch',
                'price'                => 850.00,
                'offer_price'          => null,
                'customization_option' => 'Engraved Message',
                'description'          => 'A classic natural wood photo frame that complements any home décor.',
                // Natural pine photo frame on shelf — not makeup, not roads
                'image'                => 'https://images.unsplash.com/photo-1534536281715-e28d76689b4d?w=800&q=80',
                'stock_quantity'       => 25,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'item_name'            => 'Custom Engraved Memory Frame',
                'category'             => 'Frame Designs',
                'material'             => 'MDF Wood',
                'size'                 => '8x10 inch',
                'price'                => 1400.00,
                'offer_price'          => 1150.00,
                'customization_option' => 'Name, Date & Quote',
                'description'          => 'A personalised memory frame engraved with names, dates, and a meaningful quote.',
                // White picture frame with photo — personalised memory gift
                'image'                => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&q=80',
                'stock_quantity'       => 20,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'item_name'            => 'Decorative Collage Wall Frame',
                'category'             => 'Frame Designs',
                'material'             => 'Hardwood',
                'size'                 => '12x16 inch',
                'price'                => 2200.00,
                'offer_price'          => null,
                'customization_option' => 'Multi-photo Layout',
                'description'          => 'A decorative collage frame that holds multiple photos, perfect for walls.',
                // Multiple frames on a white wall — gallery collage
                'image'                => 'https://images.unsplash.com/photo-1522444195799-478538b28823?w=800&q=80',
                'stock_quantity'       => 12,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'item_name'            => 'Shadow Box Keepsake Frame',
                'category'             => 'Frame Designs',
                'material'             => 'Deep Wood',
                'size'                 => '10x12 inch',
                'price'                => 1750.00,
                'offer_price'          => null,
                'customization_option' => 'Printed Photo & Objects',
                'description'          => 'A deep shadow box frame to display photos alongside small keepsakes and mementos.',
                // Deep shadow box with keepsakes — memory gift frame
                'image'                => 'https://images.unsplash.com/photo-1575538439014-a6cb52a1a259?w=800&q=80',
                'stock_quantity'       => 8,
                'reserved_quantity'    => 0,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],

        ]);

        $this->command->info('GiftDesignSeeder: inserted 4 gifts and 4 frames.');

        // ── Frame Design Gallery images ────────────────────────────────────
        // Delete Frame Designs entries and reinsert fresh.
        DB::table('gallery_images')->where('category', 'Frame Designs')->delete();

        DB::table('gallery_images')->insert([
            [
                'title'      => 'Natural Wood Frame',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1534536281715-e28d76689b4d?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Engraved Memory Frame',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Gallery Wall Collage',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1522444195799-478538b28823?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Shadow Box Display',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1575538439014-a6cb52a1a259?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Black Modern Frame',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Rustic Wooden Frame',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);

        $this->command->info('GiftDesignSeeder: inserted 6 Frame Design gallery images.');
    }
}
