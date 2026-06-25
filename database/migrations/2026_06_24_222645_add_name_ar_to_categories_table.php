<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });

        $names = [
            'Fashion' => 'الموضة',
            'Electronics' => 'الإلكترونيات',
            'Beauty' => 'الجمال',
            'Home' => 'المنزل',
            'Sports' => 'الرياضة',
            'Grocery' => 'البقالة',
            'Men' => 'الرجال',
            'Women' => 'النساء',
            'Kids' => 'الأطفال',
            'Accessories' => 'الإكسسوارات',
            'Phones' => 'الهواتف',
            'Laptops' => 'اللابتوب',
            'Headphones' => 'السماعات',
            'Wearables' => 'الأجهزة الذكية',
            'Skincare' => 'العناية بالبشرة',
            'Makeup' => 'مستحضرات التجميل',
            'Fragrance' => 'العطور',
            'Hair Care' => 'العناية بالشعر',
            'Furniture' => 'الأثاث',
            'Kitchen' => 'المطبخ',
            'Decor' => 'الديكور',
            'Lighting' => 'الإضاءة',
            'Fitness' => 'اللياقة البدنية',
            'Outdoor' => 'الرياضة الخارجية',
            'Footwear' => 'الأحذية',
            'Snacks' => 'الوجبات الخفيفة',
            'Beverages' => 'المشروبات',
            'Pantry' => 'المؤن',
        ];

        foreach ($names as $en => $ar) {
            DB::table('categories')->where('name', $en)->update(['name_ar' => $ar]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });
    }
};
