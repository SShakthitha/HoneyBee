<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Existing MySQL installations already have the original enum. Fresh
        // installations receive this value from the create-table migration.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE gallery_images MODIFY category ENUM('Gift & Design', 'Frame Designs', 'Laser Work', 'Events') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE gallery_images MODIFY category ENUM('Gift & Design', 'Laser Work', 'Events') NOT NULL");
        }
    }
};
