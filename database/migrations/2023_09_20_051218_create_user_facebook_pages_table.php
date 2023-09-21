<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFacebookPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key to link to the user table
            $table->string('page_id')->unique(); // Facebook Page ID
            $table->string('name'); // Page name
            $table->string('cover_url'); // URL of the page cover image
            $table->string('email')->nullable(); // Email associated with the page
            $table->string('username')->nullable(); // Username of the page
            $table->text('access_token'); // Access token for the page
            $table->timestamps();
        });

        // Define the foreign key relationship to the users table
        Schema::table('user_facebook_pages', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_facebook_pages');
    }
}
