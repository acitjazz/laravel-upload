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
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->string('name',190);
            $table->string('slug',200)->nullable()->index();
            $table->string('original_name',190);
            $table->string('file_type',30)->nullable()->index();
            $table->string('public_id',190)->nullable()->index();
            $table->string('extension',10);
            $table->string('storage',190);
            $table->mediumInteger('size')->nullable()->index();
            $table->string('readable_size');
            $table->integer('height');
            $table->integer('width');
            $table->mediumText('url');
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('medias');
    }
};
