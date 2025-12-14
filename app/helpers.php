<?php

use App\Services\NepalCalendarService;
use Carbon\Carbon;

if (!function_exists('nepali_date')) {
    /**
     * Convert AD date to BS or format BS date
     * 
     * @param string|Carbon|null $date Date to convert (if null, uses current date)
     * @param string|null $format Format for output (null = YYYY-MM-DD)
     * @param string $locale Locale for formatting ('en' or 'np')
     * @return string
     */
    function nepali_date($date = null, ?string $format = null, string $locale = 'en'): string
    {
        $service = app(NepalCalendarService::class);

        if ($date === null) {
            return $service->getCurrentBsDate();
        }

        // If date looks like BS format, format it
        if (is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $parts = explode('-', $date);
            if ((int)$parts[0] > 2050) { // Likely BS date
                return $format ? $service->formatBsDate($date, $format, $locale) : $date;
            }
        }

        // Convert AD to BS
        $bsDate = $service->adToBs($date);

        if ($format) {
            return $service->formatBsDate($bsDate, $format, $locale);
        }

        return $bsDate;
    }
}

if (!function_exists('english_date')) {
    /**
     * Convert BS date to AD
     * 
     * @param string $bsDate BS date in YYYY-MM-DD format
     * @param string|null $format Carbon format for output (null returns Carbon instance)
     * @return Carbon|string|null
     */
    function english_date(string $bsDate, ?string $format = null)
    {
        $service = app(NepalCalendarService::class);
        $carbon = $service->bsToAd($bsDate);

        if ($carbon === null) {
            return null;
        }

        return $format ? $carbon->format($format) : $carbon;
    }
}

if (!function_exists('format_nepali_date')) {
    /**
     * Format a BS date in human-readable format
     * 
     * @param string $bsDate BS date in YYYY-MM-DD format
     * @param string $format Format string
     * @param string $locale Locale ('en' or 'np')
     * @return string
     */
    function format_nepali_date(string $bsDate, string $format = 'j F Y', string $locale = 'en'): string
    {
        $service = app(NepalCalendarService::class);
        return $service->formatBsDate($bsDate, $format, $locale);
    }
}
