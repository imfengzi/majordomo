<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->integer('permission_id')->comment('权限id')->nullable()->default(null);
            $table->string('path')->comment('路径');
            $table->string('thumbnail')->comment('缩略图')->default('');
            $table->string('icon')->comment('图标')->default('');
            $table->string('guard_name', 30);
            $table->integer('sequence')->default(1)->comment('顺序');
            $table->timestamps();
            $table->softDeletes();
            NestedSet::columns($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
};

