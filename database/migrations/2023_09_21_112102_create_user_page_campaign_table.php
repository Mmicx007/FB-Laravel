<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPageCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_page_campaign', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('page_id');
            $table->string('campaign_id');
            $table->string('name');
            $table->decimal('budget', 10, 2);
            $table->string('target_audience');
            $table->dateTime('start_date');
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
        Schema::dropIfExists('user_page_campaign');
    }
}
