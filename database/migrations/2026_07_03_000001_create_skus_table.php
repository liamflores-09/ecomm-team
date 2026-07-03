<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('sku');
            $table->string('variant')->nullable();

            // PR section
            $table->text('pr_file_location')->nullable();
            $table->string('pr_assignee')->nullable();
            $table->string('pr_status')->nullable();
            $table->boolean('ready_for_cvp')->default(false);
            $table->text('remarks')->nullable();
            $table->date('pr_date_started')->nullable();
            $table->date('pr_date_completed')->nullable();

            // Content section
            $table->string('content_assignee')->nullable();
            $table->date('content_date_started')->nullable();
            $table->date('content_date_posted')->nullable();
            $table->boolean('cvp_uploaded')->default(false);

            // Marketplace links
            $table->text('shopee_link')->nullable();
            $table->text('lazada_link')->nullable();
            $table->text('tiktok_link')->nullable();
            $table->text('jg_pro_shopee_link')->nullable();
            $table->text('jg_pro_lazada_link')->nullable();
            $table->text('shopify_link')->nullable();
            $table->text('cinepro_link')->nullable();
            $table->text('lzd_brand_mall_link')->nullable();
            $table->text('shp_brand_mall_link')->nullable();
            $table->text('tt_brand_mall_link')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};
