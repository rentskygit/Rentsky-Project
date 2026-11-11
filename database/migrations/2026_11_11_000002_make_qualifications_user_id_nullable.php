<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE qualifications DROP CONSTRAINT IF EXISTS qualifications_user_id_unique');
        DB::statement('ALTER TABLE qualifications DROP CONSTRAINT IF EXISTS qualifications_user_id_foreign');
        DB::statement('ALTER TABLE qualifications ALTER COLUMN user_id DROP NOT NULL');
        DB::statement('ALTER TABLE qualifications ADD CONSTRAINT qualifications_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS qualifications_user_id_unique ON qualifications (user_id) WHERE user_id IS NOT NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS qualifications_user_id_unique');
        DB::statement('ALTER TABLE qualifications DROP CONSTRAINT IF EXISTS qualifications_user_id_foreign');
        DB::statement('DELETE FROM qualifications WHERE user_id IS NULL');
        DB::statement('ALTER TABLE qualifications ALTER COLUMN user_id SET NOT NULL');
        DB::statement('ALTER TABLE qualifications ADD CONSTRAINT qualifications_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE qualifications ADD CONSTRAINT qualifications_user_id_unique UNIQUE (user_id)');
    }
};