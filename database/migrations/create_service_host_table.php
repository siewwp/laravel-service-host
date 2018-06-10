<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceHostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('service-host.host_clients_table_name');

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('secret', 100);
            $table->string('name');
            $table->string('webhook_url');
            $table->boolean('revoked');
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
        $tableName = config('service-host.host_clients_table_name');

        Schema::drop($tableName);
    }
}
