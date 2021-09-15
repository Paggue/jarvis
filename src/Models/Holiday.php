<?php

namespace Lara\Jarvis\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        "name", "type", "date",
    ];

    const PAYMENT_LIMIT_TIME      = '17:00:00';
    const WITHDRAW_LIMIT_TIME     = '17:00:00';

    public static function isHoliday ($date)
    {
        if(!is_string($date)) $date = $date->format('Y-m-d');

        return Holiday::where('date', $date)->first();
    }

    public static function isNotHoliday ($date)
    {
        return !Holiday::isHoliday($date);
    }

    public static function isUtilDay ($date)
    {
        $dayName = Carbon::parse($date)->format('l');

        $dateTime = Carbon::parse($date)->format('H:i:s');

        return ($dayName != "Sunday" && $dayName !== "Saturday" && Holiday::isNotHoliday($date) && $dateTime <= self::PAYMENT_LIMIT_TIME);
    }

    public static function isNotUtilDay ($date)
    {
        return !Holiday::isUtilDay($date);
    }

    public static function isTodayHoliday ()
    {
        return (boolean)Holiday::isHoliday(Carbon::now()->toDateString());
    }

    public static function isTodayNotHoliday ()
    {
        return !Holiday::isTodayHoliday();
    }

    public static function isTodayUtilDay ()
    {
        return Holiday::isUtilDay(Carbon::now()->toDateString());
    }

    public static function isTodayNotUtilDay ()
    {
        return !Holiday::isTodayUtilDay();
    }

    public static function nextUtilDayFrom ($startDate)
    {
        $date = $startDate;

        while (Holiday::isNotUtilDay($date)) {
            $date = Carbon::parse($date)->addDay();
        }

        return $date->toDateString();
    }

    public static function nextUtilDay ()
    {
        $date = Carbon::now();

        while (Holiday::isNotUtilDay($date)) {
            $date = Carbon::parse($date)->addDay()->startOfDay();
        }

        return $date;
    }

    public function countUtilDaysInMonth ($date)
    {
        $startDate = Carbon::parse($date)->startOfMonth();

        $utilDaysCounter = 0;

        $monthDays = $startDate->daysInMonth;

        for ($i = 0; $i < $monthDays; $i++) {

            if (Holiday::isUtilDay(Carbon::parse($date)->startOfMonth()->addDays($i))) {
                $utilDaysCounter++;
            }
        }

        return $utilDaysCounter;
    }

    public function countNotUtilDaysInMonth ($date)
    {
        $startDate = Carbon::parse($date)->startOfMonth();

        $utilDaysCounter = 0;

        $monthDays = $startDate->daysInMonth;

        for ($i = 0; $i < $monthDays; $i++) {

            if (Holiday::isNotUtilDay(Carbon::parse($date)->startOfMonth()->addDays($i))) {
                $utilDaysCounter++;
            }
        }

        return $utilDaysCounter;
    }

    public static function isPaymentDateValid ($date = null)
    {
        if (!$date) $date = Carbon::now();

        $currentTime = Carbon::now()->format('H:i:s');
        $natal       = Carbon::create(2021, 12, 24)->format('Y-m-d');
        $current     = Carbon::now()->format('Y-m-d');


        if ($current == $date->format('Y-m-d') && ($currentTime >= self::WITHDRAW_LIMIT_TIME || $currentTime >= self::PAYMENT_LIMIT_TIME))
            return false;
        else if (Holiday::isNotUtilDay($date))
            return false;
        else if ($natal == $current && $currentTime > "12:00")
            return false;
        else
            return true;
    }

    public static function nextUtilDayForPayment ()
    {
        $date = Carbon::now();

        while (!Holiday::isPaymentDateValid($date)) {
            $date = Carbon::parse($date)->addDay()->startOfDay();
        }

        return $date;
    }

    public static function nextUtilDayForTransfer ()
    {
        $date = Carbon::now();

        return $date;
    }
}
