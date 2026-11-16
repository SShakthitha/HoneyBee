<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GalleryImageSeeder extends Seeder
{
    public function run(): void
    {
        // Always delete and reinsert so re-running this seeder refreshes images.
        DB::table('gallery_images')->delete();
        $now = now();

        DB::table('gallery_images')->insert([

            // ── Gift & Design ─────────────────────────────────────────────
            [
                'title'      => 'Personalised Gift Box',
                'category'   => 'Gift & Design',
                // Wrapped gift box with bow on clean white background
                'image'      => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Ribbon Wrapped Gift',
                'category'   => 'Gift & Design',
                // Gold ribbon wrapped around a gift — festive and clean
                'image'      => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Custom Gift Hamper',
                'category'   => 'Gift & Design',
                // Wicker hamper basket filled with treats and gifts
                'image'      => 'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Keepsake Gift Box',
                'category'   => 'Gift & Design',
                // Wooden keepsake box open with velvet lining — gift shop item
                'image'      => 'https://images.unsplash.com/photo-1619683618983-b22a4c3f0e37?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Frame Designs ─────────────────────────────────────────────
            [
                'title'      => 'Classic Wooden Frame',
                'category'   => 'Frame Designs',
                // Natural pine photo frame on a shelf
                'image'      => 'https://images.unsplash.com/photo-1534536281715-e28d76689b4d?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Engraved Memory Frame',
                'category'   => 'Frame Designs',
                // White picture frame with photo — personalised memory gift
                'image'      => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Gallery Wall Frames',
                'category'   => 'Frame Designs',
                // Collage of photo frames arranged on white wall
                'image'      => 'https://images.unsplash.com/photo-1522444195799-478538b28823?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Laser Work ────────────────────────────────────────────────
            // All three show laser-engraved GIFT SHOP items — wooden keepsakes,
            // engraved signs, personalised name boards — NOT industrial equipment.
            [
                'title'      => 'Laser Engraved Name Board',
                'category'   => 'Laser Work',
                // Engraved wooden name plaque / sign — personalised gift
                'image'      => 'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Engraved Wooden Keepsake',
                'category'   => 'Laser Work',
                // Personalised engraved wooden gift with text and design
                'image'      => 'https://images.unsplash.com/photo-1627738668643-67a3f2f7b32e?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Custom Laser Engraved Gift',
                'category'   => 'Laser Work',
                // Engraved wooden coaster / keepsake item — craft gift shop
                'image'      => 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Events ────────────────────────────────────────────────────
            [
                'title'      => 'Wedding Event Decor',
                'category'   => 'Events',
                // Elegant wedding table décor with flowers and candles
                'image'      => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Birthday Celebration',
                'category'   => 'Events',
                // Birthday party table setup with balloons and decorations
                'image'      => 'https://images.unsplash.com/photo-1464349153735-7db50ed83c84?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Floral Event Styling',
                'category'   => 'Events',
                // Event venue with floral centrepiece styling
                'image'      => 'https://images.unsplash.com/photo-1478146059778-26028b07395a?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Party Celebration Setup',
                'category'   => 'Events',
                // Party table with confetti, balloons and celebration décor
                'image'      => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

        ]);

        $this->command->info('GalleryImageSeeder: inserted 14 gallery images.');
    }
}
