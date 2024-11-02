<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeyOnServersTable extends Migration
{
    public function up()
    {
        Schema::table('servers', function (Blueprint $table) {
            // Xóa foreign key không cần thiết
            $table->dropForeign('servers_ibfk_1');
        });
    }

    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            // Khôi phục lại foreign key nếu cần
            $table->foreign('episode_id')
                  ->references('id')
                  ->on('servers')
                  ->onDelete('cascade');
        });
    }
}
