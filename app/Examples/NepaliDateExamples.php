<?php

/**
 * Nepali Date Converter - Usage Examples
 * 
 * This file demonstrates practical examples of using the Nepali date converter
 * in the ERP system. Copy these patterns to use in your code.
 */

namespace App\Examples;

use App\Services\NepalCalendarService;
use Carbon\Carbon;

class NepaliDateExamples
{
    private NepalCalendarService $calendar;

    public function __construct()
    {
        $this->calendar = app(NepalCalendarService::class);
    }

    /**
     * Example 1: Convert current date to Nepali
     */
    public function getCurrentNepaliDate()
    {
        // Method 1: Using helper
        $todayBs = nepali_date();
        echo "Today (BS): {$todayBs}\n";

        // Method 2: Using service
        $todayBs = $this->calendar->adToBs(Carbon::now());
        echo "Today (BS): {$todayBs}\n";

        // Method 3: Using package helper
        $todayBs = toNepaliDate(date('Y-m-d'));
        echo "Today (BS): {$todayBs}\n";
    }

    /**
     * Example 2: Payroll period dates
     */
    public function payrollPeriodExample()
    {
        // User enters Nepali dates for payroll period
        $periodStartBs = '2081-08-01'; // 1st Mangsir 2081
        $periodEndBs = '2081-08-30';   // 30th Mangsir 2081

        // Convert to AD for database and calculations
        $periodStartAd = $this->calendar->bsToAd($periodStartBs);
        $periodEndAd = $this->calendar->bsToAd($periodEndBs);

        echo "Nepali Period: {$periodStartBs} to {$periodEndBs}\n";
        echo "English Period: {$periodStartAd->format('Y-m-d')} to {$periodEndAd->format('Y-m-d')}\n";

        // Format for display
        $formattedPeriod = sprintf(
            '%s to %s',
            format_nepali_date($periodStartBs, 'j F Y', 'en'),
            format_nepali_date($periodEndBs, 'j F Y', 'en')
        );
        echo "Display: {$formattedPeriod}\n";
    }

    /**
     * Example 3: Employee join date conversion
     */
    public function employeeJoinDateExample()
    {
        // Employee joined on 2081-01-15 (BS)
        $joinDateBs = '2081-01-15';

        // Convert to AD
        $joinDateAd = english_date($joinDateBs);

        echo "Join Date (BS): {$joinDateBs}\n";
        echo "Join Date (AD): {$joinDateAd->format('Y-m-d')}\n";
        echo "Formatted: " . format_nepali_date($joinDateBs, 'l, j F Y') . "\n";

        // Calculate days since joining
        $daysSinceJoining = $joinDateAd->diffInDays(Carbon::now());
        echo "Days since joining: {$daysSinceJoining}\n";
    }

    /**
     * Example 4: Leave request dates
     */
    public function leaveRequestExample()
    {
        // Leave from 2081-09-10 to 2081-09-14
        $leaveStartBs = '2081-09-10';
        $leaveEndBs = '2081-09-14';

        // Convert to AD for calculations
        $leaveStartAd = english_date($leaveStartBs);
        $leaveEndAd = english_date($leaveEndBs);

        // Calculate leave days (including weekends)
        $leaveDays = $leaveStartAd->diffInDays($leaveEndAd) + 1;

        echo "Leave Period (BS): {$leaveStartBs} to {$leaveEndBs}\n";
        echo "Leave Period (AD): {$leaveStartAd->format('M d, Y')} to {$leaveEndAd->format('M d, Y')}\n";
        echo "Total Days: {$leaveDays}\n";
    }

    /**
     * Example 5: Birthday in Nepali calendar
     */
    public function birthdayExample()
    {
        // Employee birthday: 2055-05-15 (BS)
        $birthdayBs = '2055-05-15';
        $birthdayAd = english_date($birthdayBs);

        echo "Birthday (BS): {$birthdayBs}\n";
        echo "Birthday (AD): {$birthdayAd->format('F d, Y')}\n";
        echo "Age: " . $birthdayAd->age . " years\n";

        // Next birthday in current year
        $currentYearBs = explode('-', nepali_date())[0];
        $nextBirthdayBs = $currentYearBs . '-05-15';
        $nextBirthdayAd = english_date($nextBirthdayBs);

        echo "Next Birthday: " . format_nepali_date($nextBirthdayBs, 'l, j F Y') . "\n";
        echo "Days until: " . Carbon::now()->diffInDays($nextBirthdayAd) . "\n";
    }

    /**
     * Example 6: Report generation with dates
     */
    public function reportGenerationExample()
    {
        $reportStartBs = '2081-07-01';
        $reportEndBs = '2081-09-30';

        // Generate report title
        $reportTitle = sprintf(
            'Quarterly Report: %s - %s',
            format_nepali_date($reportStartBs, 'j F Y'),
            format_nepali_date($reportEndBs, 'j F Y')
        );

        echo $reportTitle . "\n";

        // Also show in English
        $reportTitleAd = sprintf(
            'Period: %s - %s',
            english_date($reportStartBs, 'M d, Y'),
            english_date($reportEndBs, 'M d, Y')
        );

        echo $reportTitleAd . "\n";
    }

    /**
     * Example 7: Date validation
     */
    public function dateValidationExample()
    {
        $dates = [
            '2081-08-15',  // Valid
            '2081-8-15',   // Invalid (wrong format)
            '81-08-15',    // Invalid (year too short)
            '2081-13-01',  // Invalid (month > 12)
            'invalid',     // Invalid
        ];

        foreach ($dates as $date) {
            $isValid = $this->calendar->isValidBsDateFormat($date);
            $status = $isValid ? '✓ Valid' : '✗ Invalid';
            echo "{$date}: {$status}\n";

            if ($isValid) {
                $ad = english_date($date);
                echo "  → AD: {$ad->format('Y-m-d')}\n";
            }
        }
    }

    /**
     * Example 8: Month-wise operations
     */
    public function monthOperationsExample()
    {
        // Get current Nepali month
        $currentBs = nepali_date();
        [$year, $month, $day] = explode('-', $currentBs);

        echo "Current Nepali Month: {$this->calendar->getMonthName((int)$month)}\n";

        // List all months
        echo "\nAll Nepali Months:\n";
        for ($i = 1; $i <= 12; $i++) {
            echo "{$i}. {$this->calendar->getMonthName($i)}\n";
        }
    }

    /**
     * Example 9: Localization (English vs Nepali)
     */
    public function localizationExample()
    {
        $date = '2081-08-15';

        // English
        $englishFull = format_nepali_date($date, 'l, j F Y', 'en');
        $englishShort = format_nepali_date($date, 'D, j M Y', 'en');

        // Nepali
        $nepaliFull = format_nepali_date($date, 'l, j F Y', 'np');
        $nepaliShort = format_nepali_date($date, 'D, j M Y', 'np');

        echo "English (Full): {$englishFull}\n";
        echo "English (Short): {$englishShort}\n";
        echo "Nepali (Full): {$nepaliFull}\n";
        echo "Nepali (Short): {$nepaliShort}\n";
    }

    /**
     * Example 10: Database storage pattern
     */
    public function databaseStorageExample()
    {
        // When saving to database
        $userInputBs = '2081-08-15'; // User enters BS date

        // Validate
        if (!$this->calendar->isValidBsDateFormat($userInputBs)) {
            echo "Invalid date format\n";
            return;
        }

        // Convert to AD
        $adDate = english_date($userInputBs);

        if ($adDate === null) {
            echo "Date conversion failed\n";
            return;
        }

        // Store both formats
        $data = [
            'event_date_bs' => $userInputBs,          // "2081-08-15"
            'event_date_ad' => $adDate->format('Y-m-d'), // "2024-11-28"
            'event_date_carbon' => $adDate,            // Carbon instance
        ];

        echo "Data to save:\n";
        print_r($data);

        // When retrieving from database
        $retrievedBs = $data['event_date_bs'];
        $displayDate = format_nepali_date($retrievedBs, 'l, j F Y', 'en');
        echo "\nDisplay: {$displayDate}\n";
    }
}

// Run examples
if (php_sapi_name() === 'cli' && isset($argv[0]) && basename($argv[0]) === basename(__FILE__)) {
    $examples = new NepaliDateExamples();

    echo "=== Nepali Date Converter Examples ===\n\n";

    echo "1. Current Date:\n";
    $examples->getCurrentNepaliDate();

    echo "\n2. Payroll Period:\n";
    $examples->payrollPeriodExample();

    echo "\n3. Employee Join Date:\n";
    $examples->employeeJoinDateExample();

    echo "\n4. Leave Request:\n";
    $examples->leaveRequestExample();

    echo "\n5. Birthday:\n";
    $examples->birthdayExample();

    echo "\n6. Report Generation:\n";
    $examples->reportGenerationExample();

    echo "\n7. Date Validation:\n";
    $examples->dateValidationExample();

    echo "\n8. Month Operations:\n";
    $examples->monthOperationsExample();

    echo "\n9. Localization:\n";
    $examples->localizationExample();

    echo "\n10. Database Storage:\n";
    $examples->databaseStorageExample();
}
