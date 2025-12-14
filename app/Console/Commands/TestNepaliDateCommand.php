<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NepalCalendarService;
use Carbon\Carbon;

class TestNepaliDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nepali:test-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Nepali date converter functionality';

    protected NepalCalendarService $calendar;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->calendar = app(NepalCalendarService::class);

        $this->info('=== Nepali Date Converter Test ===');
        $this->newLine();

        // Test 1: Current Date
        $this->info('1. Current Date Conversion:');
        $todayAd = Carbon::now()->format('Y-m-d');
        $todayBs = nepali_date();
        $this->line("  Today (AD): {$todayAd}");
        $this->line("  Today (BS): {$todayBs}");
        $this->line("  Formatted: " . format_nepali_date($todayBs, 'l, j F Y', 'en'));
        $this->newLine();

        // Test 2: Known Date Conversion
        $this->info('2. Known Date Test (2024-04-13 = 2081-01-01):');
        $testAd = '2024-04-13';
        $expectedBs = '2081-01-01';
        $convertedBs = toNepaliDate($testAd);
        $this->line("  AD: {$testAd}");
        $this->line("  BS: {$convertedBs}");
        $this->line("  Expected: {$expectedBs}");
        $this->line("  Match: " . ($convertedBs === $expectedBs ? '✓ YES' : '✗ NO'));
        $this->newLine();

        // Test 3: Reverse Conversion
        $this->info('3. Reverse Conversion (BS to AD):');
        $testBs = '2081-08-15';
        $convertedAd = english_date($testBs);
        $this->line("  BS: {$testBs}");
        $this->line("  AD: " . $convertedAd->format('Y-m-d'));
        $this->line("  Formatted (BS): " . format_nepali_date($testBs, 'j F Y'));
        $this->newLine();

        // Test 4: All Months
        $this->info('4. Nepali Month Names:');
        for ($i = 1; $i <= 12; $i++) {
            $monthName = $this->calendar->getMonthName($i);
            $this->line("  {$i}. {$monthName}");
        }
        $this->newLine();

        // Test 5: Date Validation
        $this->info('5. Date Format Validation:');
        $testDates = [
            '2081-08-15' => true,
            '2081-8-15' => false,
            '81-08-15' => false,
            'invalid' => false,
        ];

        foreach ($testDates as $date => $expected) {
            $isValid = $this->calendar->isValidBsDateFormat($date);
            $status = $isValid ? '✓' : '✗';
            $match = ($isValid === $expected) ? '✓' : '✗';
            $this->line("  {$status} {$date} (Expected: " . ($expected ? 'Valid' : 'Invalid') . ") {$match}");
        }
        $this->newLine();

        // Test 6: Formatting Options
        $this->info('6. Formatting Options:');
        $sampleDate = '2081-08-15';
        $formats = [
            'Y-m-d' => 'Database format',
            'j F Y' => 'Full date',
            'D, j M Y' => 'Short format',
            'l, jS F Y' => 'Long format with ordinal',
        ];

        foreach ($formats as $format => $description) {
            $formatted = format_nepali_date($sampleDate, $format, 'en');
            $this->line("  {$description}: {$formatted}");
        }
        $this->newLine();

        // Test 7: Localization
        $this->info('7. Localization (English vs Nepali):');
        $this->line("  English: " . format_nepali_date($sampleDate, 'l, j F Y', 'en'));
        $this->line("  Nepali: " . format_nepali_date($sampleDate, 'l, j F Y', 'np'));
        $this->newLine();

        // Test 8: Round-trip Conversion
        $this->info('8. Round-trip Conversion Test:');
        $originalAd = '2024-11-28';
        $bs = toNepaliDate($originalAd);
        $backToAd = toEnglishDate($bs);
        $match = ($originalAd === $backToAd);
        $this->line("  Original AD: {$originalAd}");
        $this->line("  To BS: {$bs}");
        $this->line("  Back to AD: {$backToAd}");
        $this->line("  Round-trip: " . ($match ? '✓ SUCCESS' : '✗ FAILED'));
        $this->newLine();

        $this->info('✅ All tests completed!');
        $this->newLine();
        $this->info('Package: anuzpandey/laravel-nepali-date v2.3.2');
        $this->info('Documentation: docs/NEPALI_DATE_CONVERTER.md');

        return 0;
    }
}
