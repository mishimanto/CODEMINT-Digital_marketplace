<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('provider_withdraws', function (Blueprint $table) {
            $table->string('report_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('provider_withdraws', function (Blueprint $table) {
            $table->dropColumn('report_path');
        });
    }

};
