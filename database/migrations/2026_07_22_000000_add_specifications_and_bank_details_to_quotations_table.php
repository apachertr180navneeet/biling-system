<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('model_maker_name')->nullable()->default('E- PASSENGER/ARZOO/PASSANGER');
            $table->string('gross_weight')->nullable()->default('60 KG');
            $table->string('charging_time')->nullable()->default('3-4 HR');
            $table->string('performance')->nullable()->default('HIGH SPEED 25 KM/HR');
            $table->string('charger_output')->nullable()->default('DC 51V 105 AH (1 LITHIUM BATTERY)');
            $table->string('motor_output')->nullable()->default('1200 W');
            $table->string('seating_capacity')->nullable()->default('5');
            $table->string('type_of_break')->nullable()->default('DRUM BREAK');
            $table->string('roof_top_abs')->nullable()->default('YES');
            $table->string('front_fiber_wind_shield')->nullable()->default('YES');
            $table->string('meter_type')->nullable()->default('DIGITAL');
            $table->text('accessories')->nullable()->default('STEPNY, JACK, TOOL KIT,STERIO, SIDE MIRROR');
            $table->text('terms_and_conditions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'model_maker_name',
                'gross_weight',
                'charging_time',
                'performance',
                'charger_output',
                'motor_output',
                'seating_capacity',
                'type_of_break',
                'roof_top_abs',
                'front_fiber_wind_shield',
                'meter_type',
                'accessories',
                'terms_and_conditions',
            ]);
        });
    }
};
