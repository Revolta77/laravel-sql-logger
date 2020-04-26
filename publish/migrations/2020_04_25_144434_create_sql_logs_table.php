<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSqlLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sql_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->text('sql');
            $table->decimal('execution_time');
            $table->string('method');
            $table->string('function');
            $table->string('line');
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
        Schema::dropIfExists('sql_logs');
    }
}
