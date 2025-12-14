<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\NepalCalendarService;

class NepaliDate implements ValidationRule
{
    protected ?string $afterOrEqual = null;
    protected ?string $beforeOrEqual = null;

    public function afterOrEqual(string $date): self
    {
        $this->afterOrEqual = $date;
        return $this;
    }

    public function beforeOrEqual(string $date): self
    {
        $this->beforeOrEqual = $date;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(NepalCalendarService::class);

        // Check if valid BS date format
        if (!$service->isValidBsDateFormat($value)) {
            $fail('The :attribute must be a valid Nepali date (BS) in YYYY-MM-DD format.');
            return;
        }

        // Convert to AD for comparison if needed
        if ($this->afterOrEqual || $this->beforeOrEqual) {
            $adDate = $service->bsToAd($value);

            if (!$adDate) {
                $fail('The :attribute is not a valid Nepali date.');
                return;
            }

            if ($this->afterOrEqual) {
                $compareAdDate = $service->bsToAd($this->afterOrEqual);
                if ($compareAdDate && $adDate->lt($compareAdDate)) {
                    $formattedDate = format_nepali_date($this->afterOrEqual, 'j F Y');
                    $fail("The :attribute must be on or after {$formattedDate}.");
                    return;
                }
            }

            if ($this->beforeOrEqual) {
                $compareAdDate = $service->bsToAd($this->beforeOrEqual);
                if ($compareAdDate && $adDate->gt($compareAdDate)) {
                    $formattedDate = format_nepali_date($this->beforeOrEqual, 'j F Y');
                    $fail("The :attribute must be on or before {$formattedDate}.");
                    return;
                }
            }
        }
    }
}
