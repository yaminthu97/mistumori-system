<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimations', function (Blueprint $table) {
            $table->decimal('PG_man_months', 4, 2)->change();
            $table->decimal('BSE_man_months', 4, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimations', function (Blueprint $table) {
            $table->decimal('PG_man_months', 2, 1)->change();
            $table->decimal('BSE_man_months', 2, 1)->change();
        });
    }
};
