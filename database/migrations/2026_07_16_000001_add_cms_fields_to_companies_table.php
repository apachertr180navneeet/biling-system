<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('about')->nullable()->after('zipcode');
            $table->string('tagline')->nullable()->after('website');
            $table->string('founding_year', 4)->nullable()->after('tagline');
            $table->string('facebook_url')->nullable()->after('website');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('instagram_url')->nullable()->after('twitter_url');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['about', 'tagline', 'founding_year', 'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url']);
        });
    }
};
