<?php
namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;

trait DatabaseMigrations
{
    protected function signature(Blueprint $table): void
    {
         $table->unsignedBigInteger("created_by");
         $table->foreign('created_by')->references('id')->on('users');
         $table->unsignedBigInteger("updated_by");
         $table->foreign('updated_by')->references('id')->on('users');
         $table->unsignedBigInteger("deleted_by")->nullable();
         $table->foreign('deleted_by')->references('id')->on('users');
        $table->timestamps();
        $table->softDeletes();
    }
}
