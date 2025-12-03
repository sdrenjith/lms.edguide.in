<?php

namespace App\Filament\Resources\QuestionMediaResource\Pages;

use App\Filament\Resources\QuestionMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionMedia extends EditRecord
{
    protected static string $resource = QuestionMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
