<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_emails', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->foreignId('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade')->onUpdate('cascade');
			
			$table->string('recipient')->nullable();
			$table->string('sender')->nullable();
			$table->string('from')->nullable();
			$table->string('subject')->nullable();
			$table->longText('body_plain')->nullable();
			$table->longText('stripped_text')->nullable();
			$table->string('stripped_signature')->nullable();
			$table->longText('body_html')->nullable();
			$table->longText('stripped_html')->nullable();
			$table->integer('attachment_count')->nullable();
			$table->string('attachment_x')->nullable();
			$table->integer('timestamp')->nullable();
			$table->string('signature')->nullable();
			$table->longText('message_headers')->nullable();
			$table->string('content_id_map')->nullable();
			$table->tinyInteger('read')->default(0);
			$table->string('folder')->nullable();
			
				
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
		Schema::table('mail_emails', function (Blueprint $table) {
			$table->dropForeign('mail_emails_user_id_foreign');
        });
        Schema::dropIfExists('mail_emails');
    }
}
