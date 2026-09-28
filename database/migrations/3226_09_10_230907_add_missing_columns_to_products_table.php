<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)
                      ->nullable()
                      ->after('price');
            }

            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku', 50)
                      ->nullable()
                      ->after('stock');
            }

            if (!Schema::hasColumn('products', 'status')) {
                $table->string('status', 20)
                      ->default('active')
                      ->after('sku');
            }

            if (!Schema::hasColumn('products', 'badge')) {
                $table->string('badge', 20)
                      ->default('none')
                      ->after('status');
            }

            if (!Schema::hasColumn('products', 'featured')) {
                $table->boolean('featured')
                      ->default(false)
                      ->after('badge');
            }

            if (!Schema::hasColumn('products', 'views')) {
                $table->integer('views')
                      ->default(0)
                      ->after('featured');
            }
        });

        $this->backfillSkus();
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['original_price', 'sku', 'status', 'badge', 'featured', 'views'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function backfillSkus(): void
    {
        $products = DB::table('products')->whereNull('sku')->orWhere('sku', '')->get();

        foreach ($products as $product) {
            $sku = 'SKU-' . str_pad($product->id, 6, '0', STR_PAD_LEFT);

            DB::table('products')
                ->where('id', $product->id)
                ->update(['sku' => $sku]);
        }
    }
};