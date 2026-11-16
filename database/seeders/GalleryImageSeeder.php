<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GalleryImageSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('gallery_images')->count() > 0) {
            $this->command->info('GalleryImageSeeder: table already has data — skipping.');
            return;
        }

        $now = now();

        DB::table('gallery_images')->insert([

            // ── Gift & Design ─────────────────────────────────────────────
            [
                'title'      => 'Personalised Gift Box',
                'category'   => 'Gift & Design',
                'image'      => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Wrapped Gift with Ribbon',
                'category'   => 'Gift & Design',
                'image'      => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Custom Gift Hamper',
                'category'   => 'Gift & Design',
                'image'      => 'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Handcrafted Gift Package',
                'category'   => 'Gift & Design',
                'image'      => 'https://images.unsplash.com/photo-1481553231771-02cb5a9e6b1b?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Frame Designs ─────────────────────────────────────────────
            [
                'title'      => 'Wooden Photo Frame',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Custom Engraved Frame',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Decorative Wall Frame',
                'category'   => 'Frame Designs',
                'image'      => 'https://images.unsplash.com/photo-1584464491033-06628f3a6b7b?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Laser Work ────────────────────────────────────────────────
            [
                'title'      => 'Laser Engraved Wood',
                'category'   => 'Laser Work',
                'image'      => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Precision Laser Cutting',
                'category'   => 'Laser Work',
                'image'      => 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Custom Laser Design Panel',
                'category'   => 'Laser Work',
                'image'      => 'https://images.unsplash.com/photo-1517420704952-d9f39e95b43e?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Events ────────────────────────────────────────────────────
            [
                'title'      => 'Wedding Event Decor',
                'category'   => 'Events',
                'image'      => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Birthday Celebration',
                'category'   => 'Events',
                'image'      => 'https://images.unsplash.com/photo-1464349153735-7db50ed83c84?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Floral Event Styling',
                'category'   => 'Events',
                'image'      => 'https://images.unsplash.com/photo-1478146059778-26028b07395a?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title'      => 'Party Celebration Setup',
                'category'   => 'Events',
                'image'      => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&q=80',
                'created_at' => $now, 'updated_at' => $now,
            ],

        ]);

        $this->command->info('GalleryImageSeeder: inserted 14 gallery images.');
    }
}
