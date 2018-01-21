<?php

use App\Search;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url');
            $table->unsignedInteger('user_id');
            $table->enum('status', [
                Search::STATUS_CREATED,
                Search::STATUS_RUNNING,
                Search::STATUS_FINISHED,
                Search::STATUS_PAUSED,
                Search::STATUS_FAILED
            ])->default(Search::STATUS_CREATED);
            $table->boolean('is_limited')->default(true);
            $table->string('pid')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('searches');
    }
}
