<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\QuestionType;

return new class extends Migration
{
    public function up()
    {
        QuestionType::create(['name' => 'audio_fill_blank']);
    }

    public function down()
    {
        QuestionType::where('name', 'audio_fill_blank')->delete();
    }
}; 