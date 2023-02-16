<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('path')->comment('路径');
            $table->string('desc')->comment('描述');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('permission_route', function (Blueprint $table) {
            $table->id();
            $table->string('route_id');
            $table->string('permission_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
        Schema::dropIfExists('permission_route');
    }
};
