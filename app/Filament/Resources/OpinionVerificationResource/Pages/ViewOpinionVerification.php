<?php

namespace App\Filament\Resources\OpinionVerificationResource\Pages;

use App\Filament\Resources\OpinionVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOpinionVerification extends ViewRecord
{
    protected static string $resource = OpinionVerificationResource::class;



    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_correct')
                ->label('Mark as Correct')
                ->icon('heroicon-m-check')
                ->color('success')
                ->extraAttributes(['style' => 'background-color: #10b981 !important; border-color: #10b981 !important;'])
                ->visible(fn () => $this->record->verification_status !== 'verified_correct')
                ->requiresConfirmation()
                ->modalHeading('Mark as Correct')
                ->modalDescription(fn () => $this->record->verification_status === 'pending' 
                    ? 'Are you sure you want to mark this answer as correct? This will award 1 point to the student.'
                    : 'Are you sure you want to change this answer to correct? This will award 1 point to the student.')
                ->action(function () {
                    $user = $this->record->user;
                    $previousStatus = $this->record->verification_status;
                    
                    $this->record->update([
                        'verification_status' => 'verified_correct',
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                    ]);
                    
                    // Handle score adjustment based on previous status
                    if ($previousStatus === 'pending' || $previousStatus === 'verified_incorrect') {
                        // Award point (either first time or changing from incorrect to correct)
                        $user->total_score += 1;
                        $user->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Answer verified as correct')
                            ->body("1 point added to {$user->name}'s score")
                            ->success()
                            ->send();
                    }
                        
                    return redirect()->to('/admin/opinion-verifications');
                }),
            Actions\Action::make('mark_incorrect')
                ->label('Mark as Incorrect')
                ->icon('heroicon-m-x-mark')
                ->color('danger')
                ->visible(fn () => $this->record->verification_status !== 'verified_incorrect')
                ->requiresConfirmation()
                ->modalHeading('Mark as Incorrect')
                ->modalDescription(fn () => $this->record->verification_status === 'pending' 
                    ? 'Are you sure you want to mark this answer as incorrect? No points will be awarded.'
                    : 'Are you sure you want to change this answer to incorrect? This will remove 1 point from the student.')
                ->action(function () {
                    $user = $this->record->user;
                    $previousStatus = $this->record->verification_status;
                    
                    $this->record->update([
                        'verification_status' => 'verified_incorrect',
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                    ]);
                    
                    // Handle score adjustment based on previous status
                    if ($previousStatus === 'verified_correct') {
                        // Remove point when changing from correct to incorrect
                        $user->total_score -= 1;
                        $user->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Answer verified as incorrect')
                            ->body("1 point removed from {$user->name}'s score")
                            ->warning()
                            ->send();
                    } else {
                        // No score change for pending -> incorrect
                        \Filament\Notifications\Notification::make()
                            ->title('Answer verified as incorrect')
                            ->body('No points awarded')
                            ->warning()
                            ->send();
                    }
                        
                    return redirect()->to('/admin/opinion-verifications');
                }),
            Actions\Action::make('reset_pending')
                ->label('Reset to Pending')
                ->icon('heroicon-m-arrow-path')
                ->color('gray')
                ->visible(fn () => $this->record->verification_status !== 'pending')
                ->requiresConfirmation()
                ->modalHeading('Reset to Pending')
                ->modalDescription(fn () => $this->record->verification_status === 'verified_correct' 
                    ? 'Are you sure you want to reset this answer to pending? This will remove 1 point from the student.'
                    : 'Are you sure you want to reset this answer to pending? This will not affect the student\'s score.')
                ->action(function () {
                    $user = $this->record->user;
                    $previousStatus = $this->record->verification_status;
                    
                    $this->record->update([
                        'verification_status' => 'pending',
                        'verified_by' => null,
                        'verified_at' => null,
                    ]);
                    
                    // Handle score adjustment based on previous status
                    if ($previousStatus === 'verified_correct') {
                        // Remove point when resetting from correct to pending
                        $user->total_score -= 1;
                        $user->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Answer reset to pending')
                            ->body("1 point removed from {$user->name}'s score")
                            ->info()
                            ->send();
                    } else {
                        // No score change for incorrect -> pending
                        \Filament\Notifications\Notification::make()
                            ->title('Answer reset to pending')
                            ->body('No score changes made')
                            ->info()
                            ->send();
                    }
                        
                    return redirect()->to('/admin/opinion-verifications');
                }),
            Actions\EditAction::make(),
        ];
    }
} 