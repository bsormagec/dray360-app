<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTrainingDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_training_documents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type', 128);
            $table->string('s3uri', 1024)->index();
            $table->string('sha256sum', 64)->index();
            $table->datetime('discovered_at')->nullable();
            $table->json('document_metadata')->nullable();
            $table->json('external_references')->nullable();
            $table->json('extracted_features_list')->nullable();
            $table->json('labels')->nullable();
            $table->json('document_metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_training_documents');
    }
}
