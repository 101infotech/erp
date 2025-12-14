<?php

namespace App\Services;

use Carbon\Carbon;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Illuminate\Support\Facades\Log;

/**
 * Nepal Calendar Service (Bikram Sambat ↔ Gregorian Conversion)
 * 
 * Provides conversion between Bikram Sambat (BS) and Gregorian (AD) calendars
 * Uses anuzpandey/laravel-nepali-date package for accurate conversions
 */
class NepalCalendarService
{
    /**
     * Convert Bikram Sambat date to Gregorian date
     * 
     * @param string $bsDate Format: YYYY-MM-DD (e.g., "2081-08-15")
     * @return Carbon|null
     */
    public function bsToAd(string $bsDate): ?Carbon
    {
        try {
            if (!$this->isValidBsDateFormat($bsDate)) {
                return null;
            }

            // Use the package helper function to convert BS to AD
            $adDateString = toEnglishDate($bsDate);

            return Carbon::parse($adDateString);
        } catch (\Exception $e) {
            Log::error('BS to AD conversion error: ' . $e->getMessage(), [
                'bsDate' => $bsDate
            ]);
            return null;
        }
    }

    /**
     * Convert Gregorian date to Bikram Sambat date
     * 
     * @param Carbon|string $adDate
     * @return string|null Format: YYYY-MM-DD
     */
    public function adToBs($adDate): ?string
    {
        try {
            $date = $adDate instanceof Carbon ? $adDate : Carbon::parse($adDate);

            // Use the package helper function to convert AD to BS
            $bsDateString = toNepaliDate($date->format('Y-m-d'));

            return $bsDateString;
        } catch (\Exception $e) {
            Log::error('AD to BS conversion error: ' . $e->getMessage(), [
                'adDate' => is_string($adDate) ? $adDate : $adDate->format('Y-m-d')
            ]);
            return null;
        }
    }

    /**
     * Format BS date for display
     * 
     * @param string $bsDate Format: YYYY-MM-DD
     * @param string $format Format string (default: 'j F Y')
     * @param string $locale Locale ('en' or 'np')
     * @return string Formatted date (e.g., "15 Mangsir 2081")
     */
    public function formatBsDate(string $bsDate, string $format = 'j F Y', string $locale = 'en'): string
    {
        try {
            return LaravelNepaliDate::from($bsDate)->toNepaliDate(format: $format, locale: $locale);
        } catch (\Exception $e) {
            Log::error('BS date formatting error: ' . $e->getMessage(), [
                'bsDate' => $bsDate,
                'format' => $format
            ]);

            // Fallback to basic formatting
            [$year, $month, $day] = explode('-', $bsDate);
            return $day . ' ' . $this->getMonthName((int)$month) . ' ' . $year;
        }
    }

    /**
     * Get current date in BS format
     * 
     * @return string Format: YYYY-MM-DD
     */
    public function getCurrentBsDate(): string
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            return toNepaliDate($today);
        } catch (\Exception $e) {
            Log::error('Current BS date calculation error: ' . $e->getMessage());

            // Fallback to approximate calculation
            $today = Carbon::now();
            $bsYear = $today->year + 57;
            return sprintf('%04d-01-01', $bsYear);
        }
    }

    /**
     * Validate BS date format
     * 
     * @param string $bsDate
     * @return bool
     */
    public function isValidBsDateFormat(string $bsDate): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $bsDate)) {
            return false;
        }

        [$year, $month, $day] = explode('-', $bsDate);

        // Basic validation
        if ($year < 2000 || $year > 2200) {
            return false;
        }

        if ($month < 1 || $month > 12) {
            return false;
        }

        if ($day < 1 || $day > 32) { // BS months can have up to 32 days
            return false;
        }

        return true;
    }

    /**
     * Get month name in Nepali
     * 
     * @param int $month Month number (1-12)
     * @return string
     */
    public function getMonthName(int $month): string
    {
        $monthNames = [
            1 => 'Baishakh',
            2 => 'Jestha',
            3 => 'Ashadh',
            4 => 'Shrawan',
            5 => 'Bhadra',
            6 => 'Ashwin',
            7 => 'Kartik',
            8 => 'Mangsir',
            9 => 'Poush',
            10 => 'Magh',
            11 => 'Falgun',
            12 => 'Chaitra',
        ];

        return $monthNames[$month] ?? 'Unknown';
    }

    /**
     * Parse user-entered BS date and corresponding AD date
     * Since accurate conversion is not implemented, users will enter both dates
     * 
     * @param string $bsDate BS date in YYYY-MM-DD format
     * @param string $adDate AD date in YYYY-MM-DD format
     * @return array ['bs' => string, 'ad' => Carbon]
     */
    public function parseManualDates(string $bsDate, string $adDate): array
    {
        return [
            'bs' => $bsDate,
            'ad' => Carbon::parse($adDate),
        ];
    }

    /**
     * Calculate difference in days between two BS dates
     * Converts to AD for calculation
     * 
     * @param string $bsDateStart
     * @param string $bsDateEnd
     * @return int Number of days
     */
    public function diffInDays(string $bsDateStart, string $bsDateEnd): int
    {
        $adStart = $this->bsToAd($bsDateStart);
        $adEnd = $this->bsToAd($bsDateEnd);

        if (!$adStart || !$adEnd) {
            return 0;
        }

        return $adStart->diffInDays($adEnd);
    }

    /**
     * Calculate working days between two BS dates (excluding Saturdays)
     * 
     * @param string $bsDateStart
     * @param string $bsDateEnd
     * @return int Number of working days
     */
    public function calculateWorkingDays(string $bsDateStart, string $bsDateEnd): int
    {
        $adStart = $this->bsToAd($bsDateStart);
        $adEnd = $this->bsToAd($bsDateEnd);

        if (!$adStart || !$adEnd) {
            return 0;
        }

        $workDays = 0;
        $current = $adStart->copy();

        while ($current->lte($adEnd)) {
            if (!$current->isSaturday()) {
                $workDays++;
            }
            $current->addDay();
        }

        return $workDays;
    }

    /**
     * Compare two BS dates
     * 
     * @param string $bsDate1
     * @param string $bsDate2
     * @return int -1 if date1 < date2, 0 if equal, 1 if date1 > date2
     */
    public function compareDates(string $bsDate1, string $bsDate2): int
    {
        return strcmp($bsDate1, $bsDate2) <=> 0;
    }

    /**
     * Check if BS date1 is after BS date2
     * 
     * @param string $bsDate1
     * @param string $bsDate2
     * @return bool
     */
    public function isAfter(string $bsDate1, string $bsDate2): bool
    {
        return $this->compareDates($bsDate1, $bsDate2) > 0;
    }

    /**
     * Check if BS date1 is before BS date2
     * 
     * @param string $bsDate1
     * @param string $bsDate2
     * @return bool
     */
    public function isBefore(string $bsDate1, string $bsDate2): bool
    {
        return $this->compareDates($bsDate1, $bsDate2) < 0;
    }

    /**
     * Get the number of days in a specific BS month
     * 
     * @param int $year BS Year (e.g., 2081)
     * @param int $month BS Month (1-12)
     * @return int Number of days in the month (29, 30, or 31)
     */
    public function getDaysInMonth(int $year, int $month): int
    {
        try {
            // Create first day of month
            $firstDay = sprintf('%04d-%02d-01', $year, $month);

            // Create first day of next month
            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }
            $nextMonthFirstDay = sprintf('%04d-%02d-01', $nextYear, $nextMonth);

            // Convert to AD
            $firstDayAd = $this->bsToAd($firstDay);
            $nextMonthFirstDayAd = $this->bsToAd($nextMonthFirstDay);

            if (!$firstDayAd || !$nextMonthFirstDayAd) {
                // Fallback to default values based on Nepali calendar patterns
                // Months 1-8 usually have 30-31 days, months 9-12 usually have 29-30 days
                return $month <= 8 ? 31 : 29;
            }

            // Calculate difference
            return $firstDayAd->diffInDays($nextMonthFirstDayAd);
        } catch (\Exception $e) {
            Log::error('Get days in BS month error: ' . $e->getMessage(), [
                'year' => $year,
                'month' => $month
            ]);

            // Fallback to approximate values
            return $month <= 8 ? 31 : 29;
        }
    }
}
