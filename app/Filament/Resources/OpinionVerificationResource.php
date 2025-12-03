<?php

namespace App\Filament\Resources;

use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Forms\Get;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;

class OpinionVerificationResource extends Resource
{
    protected static ?string $model = StudentAnswer::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Opinion Verification';

    protected static ?string $modelLabel = 'Opinion Answer';

    protected static ?string $pluralModelLabel = 'Opinion Answers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Information')
                    ->schema([
                        Forms\Components\TextInput::make('user.name')
                            ->label('Student Name')
                            ->disabled()
                            ->formatStateUsing(fn ($record) => $record->user->name ?? 'N/A'),
                        Forms\Components\TextInput::make('submitted_at')
                            ->label('Submitted At')
                            ->disabled()
                            ->formatStateUsing(fn ($record) => $record->submitted_at ? $record->submitted_at->format('Y-m-d H:i:s') : 'N/A'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Question & Answer')
                    ->schema([
                        Forms\Components\Textarea::make('question.question_text')
                            ->label('Question')
                            ->disabled()
                            ->rows(3)
                            ->formatStateUsing(fn ($record) => $record->question->instruction ?? 'N/A'),
                        Forms\Components\Placeholder::make('audio_video_file')
                            ->label('Student\'s Audio/Video Response')
                            ->content(function ($record) {
                                if ($record->file_upload) {
                                    $fileUrl = asset('storage/' . $record->file_upload);
                                    $fileExtension = pathinfo($record->file_upload, PATHINFO_EXTENSION);
                                    
                                    // Check if it's an audio or video file
                                    if (in_array(strtolower($fileExtension), ['mp3', 'wav', 'ogg', 'm4a'])) {
                                        return new \Illuminate\Support\HtmlString("
                                            <div style='background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;'>
                                                <div style='display: flex; align-items: center; margin-bottom: 0.5rem;'>
                                                    <svg style='width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; color: #059669;' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15.536 6.464a9 9 0 010 12.728m-4.242-4.242a3 3 0 010 4.242m6.364-6.364a5 5 0 010 7.072m-2.828-2.828a7 7 0 010 9.899'/>
                                                    </svg>
                                                    <strong style='color: #374151;'>Audio Response</strong>
                                                </div>
                                                <audio controls style='width: 100%; margin-bottom: 0.5rem;'>
                                                    <source src='{$fileUrl}' type='audio/{$fileExtension}'>
                                                    Your browser does not support the audio element.
                                                </audio>
                                                <div style='text-align: center;'>
                                                    <a href='{$fileUrl}' target='_blank' style='color: #3b82f6; text-decoration: none; font-size: 0.875rem;'>Download Audio File</a>
                                                </div>
                                            </div>
                                        ");
                                    } elseif (in_array(strtolower($fileExtension), ['mp4', 'webm', 'mov'])) {
                                        return new \Illuminate\Support\HtmlString("
                                            <div style='background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;'>
                                                <div style='display: flex; align-items: center; margin-bottom: 0.5rem;'>
                                                    <svg style='width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; color: #059669;' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'/>
                                                    </svg>
                                                    <strong style='color: #374151;'>Video Response</strong>
                                                </div>
                                                <video controls style='width: 100%; max-height: 300px; margin-bottom: 0.5rem;'>
                                                    <source src='{$fileUrl}' type='video/{$fileExtension}'>
                                                    Your browser does not support the video element.
                                                </video>
                                                <div style='text-align: center;'>
                                                    <a href='{$fileUrl}' target='_blank' style='color: #3b82f6; text-decoration: none; font-size: 0.875rem;'>Download Video File</a>
                                                </div>
                                            </div>
                                        ");
                                    }
                                }
                                return 'No audio/video file submitted';
                            })
                            ->visible(fn ($record) => $record->question->subject && strtolower($record->question->subject->name) === 'speaking'),
                        Forms\Components\Textarea::make('student_answer_display')
                            ->label(function ($record) {
                                $isSpeaking = $record->question->subject && strtolower($record->question->subject->name) === 'speaking';
                                return $isSpeaking ? 'Student\'s Written Response (Additional Context)' : 'Student\'s Answer';
                            })
                            ->disabled()
                            ->rows(5)
                            ->formatStateUsing(function ($record) {
                                $answerData = $record->answer_data;
                                if (is_string($answerData)) {
                                    // Try to decode JSON first
                                    $decoded = json_decode($answerData, true);
                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        return is_array($decoded) ? implode(' ', $decoded) : $decoded;
                                    }
                                    // If not JSON, return as string
                                    return $answerData;
                                }
                                $result = is_array($answerData) ? implode(' ', $answerData) : (string) $answerData;
                                return $result ?: 'No written response provided';
                            }),
                        Forms\Components\Textarea::make('sample_answer')
                            ->label('Sample Answer (Reference)')
                            ->disabled()
                            ->rows(3)
                            ->placeholder('No sample answer provided')
                            ->formatStateUsing(function ($record) {
                                $questionData = $record->question->question_data ?? [];
                                if (is_string($questionData)) {
                                    $questionData = json_decode($questionData, true) ?? [];
                                }
                                return $questionData['opinion_answer'] ?? 'No sample answer provided';
                            }),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Verification')
                    ->schema([
                        Forms\Components\Textarea::make('verification_comment')
                            ->label('Comment (Optional)')
                            ->rows(3)
                            ->placeholder('Add any feedback or comment for the student...'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->width('150px'),
                TextColumn::make('question.instruction')
                    ->label('Question')
                    ->searchable()
                    ->limit(60)
                    ->formatStateUsing(fn ($record) => $record->question->instruction ?? 'Question ID: ' . $record->question->id)
                    ->width('300px')
                    ->wrap(),
                BadgeColumn::make('verification_status')
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pending' => 'Pending',
                            'verified_correct' => 'Correct',
                            'verified_incorrect' => 'Incorrect',
                            default => 'Unknown',
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'pending' => 'warning',
                            'verified_correct' => 'success',
                            'verified_incorrect' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->width('120px'),
                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->width('180px'),
                TextColumn::make('verified_at')
                    ->label('Verified')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not verified')
                    ->width('180px'),
                TextColumn::make('verifiedBy.name')
                    ->label('Verified By')
                    ->placeholder('Not verified')
                    ->width('150px'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified_correct' => 'Correct',
                        'verified_incorrect' => 'Incorrect',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Removed the EditAction ("Verify") from the listing actions
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\OpinionVerificationResource\Pages\ListOpinionVerifications::route('/'),
            'create' => \App\Filament\Resources\OpinionVerificationResource\Pages\CreateOpinionVerification::route('/create'),
            'view' => \App\Filament\Resources\OpinionVerificationResource\Pages\ViewOpinionVerification::route('/{record}'),
            'edit' => \App\Filament\Resources\OpinionVerificationResource\Pages\EditOpinionVerification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('question.questionType', function (Builder $query) {
                $query->where('name', 'opinion');
            })
            ->with(['user', 'question', 'verifiedBy']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('question.questionType', function (Builder $query) {
            $query->where('name', 'opinion');
        })
        ->where('verification_status', 'pending')
        ->count();
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Temporarily hidden - Hide for accounts and dataentry users only
        return false; // !(auth()->check() && (auth()->user()->isAccounts() || auth()->user()->isDataEntry()));
    }
} 