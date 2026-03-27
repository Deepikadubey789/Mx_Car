<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // table => [fk_column, fk_index_name, composite_index_name]
        $tables = [
            'cr_car_amenities_translations' => ['cr_car_amenities_id', 'idx_cr_amenities_trans_fk', 'idx_cr_amenities_trans_fk_lang'],
            'cr_car_amenity_categories_translations' => ['cr_car_amenity_categories_id', 'idx_cr_amenity_cat_trans_fk', 'idx_cr_amenity_cat_trans_fk_lang'],
            'cr_car_categories_translations' => ['cr_car_categories_id', 'idx_cr_categories_trans_fk', 'idx_cr_categories_trans_fk_lang'],
            'cr_car_colors_translations' => ['cr_car_colors_id', 'idx_cr_colors_trans_fk', 'idx_cr_colors_trans_fk_lang'],
            'cr_car_fuels_translations' => ['cr_car_fuels_id', 'idx_cr_fuels_trans_fk', 'idx_cr_fuels_trans_fk_lang'],
            'cr_car_maintenance_histories_translations' => ['cr_car_maintenance_histories_id', 'idx_cr_maint_hist_trans_fk', 'idx_cr_maint_hist_trans_fk_lang'],
            'cr_car_makes_translations' => ['cr_car_makes_id', 'idx_cr_makes_trans_fk', 'idx_cr_makes_trans_fk_lang'],
            'cr_car_transmissions_translations' => ['cr_car_transmissions_id', 'idx_cr_transmissions_trans_fk', 'idx_cr_transmissions_trans_fk_lang'],
            'cr_car_types_translations' => ['cr_car_types_id', 'idx_cr_types_trans_fk', 'idx_cr_types_trans_fk_lang'],
            'cr_cars_translations' => ['cr_cars_id', 'idx_cr_cars_trans_fk', 'idx_cr_cars_trans_fk_lang'],
            'cr_services_translations' => ['cr_services_id', 'idx_cr_services_trans_fk', 'idx_cr_services_trans_fk_lang'],
            'cr_tags_translations' => ['cr_tags_id', 'idx_cr_tags_trans_fk', 'idx_cr_tags_trans_fk_lang'],
            'cr_taxes_translations' => ['cr_taxes_id', 'idx_cr_taxes_trans_fk', 'idx_cr_taxes_trans_fk_lang'],
        ];

        foreach ($tables as $table => $config) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            [$foreignKey, $fkIndex, $compositeIndex] = $config;

            Schema::table($table, function (Blueprint $blueprint) use ($foreignKey, $fkIndex, $compositeIndex) {
                $blueprint->index($foreignKey, $fkIndex);
                $blueprint->index([$foreignKey, 'lang_code'], $compositeIndex);
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'cr_car_amenities_translations' => ['idx_cr_amenities_trans_fk', 'idx_cr_amenities_trans_fk_lang'],
            'cr_car_amenity_categories_translations' => ['idx_cr_amenity_cat_trans_fk', 'idx_cr_amenity_cat_trans_fk_lang'],
            'cr_car_categories_translations' => ['idx_cr_categories_trans_fk', 'idx_cr_categories_trans_fk_lang'],
            'cr_car_colors_translations' => ['idx_cr_colors_trans_fk', 'idx_cr_colors_trans_fk_lang'],
            'cr_car_fuels_translations' => ['idx_cr_fuels_trans_fk', 'idx_cr_fuels_trans_fk_lang'],
            'cr_car_maintenance_histories_translations' => ['idx_cr_maint_hist_trans_fk', 'idx_cr_maint_hist_trans_fk_lang'],
            'cr_car_makes_translations' => ['idx_cr_makes_trans_fk', 'idx_cr_makes_trans_fk_lang'],
            'cr_car_transmissions_translations' => ['idx_cr_transmissions_trans_fk', 'idx_cr_transmissions_trans_fk_lang'],
            'cr_car_types_translations' => ['idx_cr_types_trans_fk', 'idx_cr_types_trans_fk_lang'],
            'cr_cars_translations' => ['idx_cr_cars_trans_fk', 'idx_cr_cars_trans_fk_lang'],
            'cr_services_translations' => ['idx_cr_services_trans_fk', 'idx_cr_services_trans_fk_lang'],
            'cr_tags_translations' => ['idx_cr_tags_trans_fk', 'idx_cr_tags_trans_fk_lang'],
            'cr_taxes_translations' => ['idx_cr_taxes_trans_fk', 'idx_cr_taxes_trans_fk_lang'],
        ];

        foreach ($tables as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($indexes) {
                $blueprint->dropIndex($indexes[0]);
                $blueprint->dropIndex($indexes[1]);
            });
        }
    }
};
