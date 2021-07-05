<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->uuid('email_id');
			$table->foreign('email_id')
      			->references('id')->on('mail_emails')
      			->onDelete('cascade')->onUpdate('cascade');
			
			$table->string('file_path')->nullable();
			$table->string('file_url')->nullable();
			$table->string('file_name')->nullable();
			$table->string('file_mimetype')->nullable();
			$table->string('file_size')->nullable();
			
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
		Schema::table('mail_attachments', function (Blueprint $table) {
			$table->dropForeign('mail_attachments_email_id_foreign');
        });
        Schema::dropIfExists('mail_attachments');
    }
}
