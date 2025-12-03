<?php

namespace App\Filament\Widgets;

use App\Models\Fee;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FeeStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Calculate total fees paid from actual fee payments (Fee model)
        $totalFeesPaid = Fee::sum('amount_paid');
        
        // Calculate total course fees from individual student records (User model)
        $totalCourseFees = User::where('role', 'student')->sum('course_fee');
        
        // Calculate balance dynamically
        $balanceAmount = $totalCourseFees - $totalFeesPaid;
        
        // Get additional statistics
        $totalStudents = User::where('role', 'student')->count();
        $studentsWithPayments = User::where('role', 'student')
            ->whereHas('fees')
            ->count();

        // Only show stats if there are students or payments
        if ($totalStudents === 0 && $totalFeesPaid === 0) {
            return [];
        }

        return [
            Stat::make('Total Fees Paid', '₹' . number_format($totalFeesPaid, 2))
                ->description("From {$studentsWithPayments} out of {$totalStudents} students")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Course Fees', '₹' . number_format($totalCourseFees, 2))
                ->description("Expected from {$totalStudents} students")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
            Stat::make('Balance Amount', '₹' . number_format($balanceAmount, 2))
                ->description('Remaining to be collected')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($balanceAmount > 0 ? 'danger' : 'success'),
        ];
    }
} 