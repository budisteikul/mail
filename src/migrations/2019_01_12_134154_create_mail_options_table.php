<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
			$table->foreignId('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade')->onUpdate('cascade');
			
			$table->string('name');
			$table->string('value')->nullable();
			
            $table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('mail_options', function (Blueprint $table) {
			$table->dropForeign('mail_options_user_id_foreign');
        });
        Schema::dropIfExists('mail_options');
    }
}
