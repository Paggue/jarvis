<?php

namespace Lara\Jarvis\Utils;

use Carbon\Carbon;

class HolidayUtils
{
    protected $model;

    public function __construct ($model)
    {
        $this->model = $model;
    }

    const PAYMENT_LIMIT_TIME  = '17:00:00';
    const WITHDRAW_LIMIT_TIME = '17:00:00';

    public function isHoliday ($date)
    {
        if (!is_string($date)) $date = $date->format('Y-m-d');

        return $this->model->where('date', $date)->first();
    }

    public function isNotHoliday ($date)
    {
        return !$this->isHoliday($date);
    }

    public function isUtilDay ($date)
    {
        $dayName = Carbon::parse($date)->format('l');

        $dateTime = Carbon::parse($date)->format('H:i:s');

        return ($dayName != "Sunday" && $dayName !== "Saturday" && $this->isNotHoliday($date) && $dateTime <= self::PAYMENT_LIMIT_TIME);
    }

    public function isNotUtilDay ($date)
    {
        return !$this->isUtilDay($date);
    }

    public function isTodayHoliday ()
    {
        return (boolean)$this->isHoliday(Carbon::now()->toDateString());
    }

    public function isTodayNotHoliday ()
    {
        return !$this->isTodayHoliday();
    }

    public function isTodayUtilDay ()
    {
        return $this->isUtilDay(Carbon::now()->toDateString());
    }

    public function isTodayNotUtilDay ()
    {
        return !$this->isTodayUtilDay();
    }

    public function nextUtilDayFrom ($startDate)
    {
        $date = $startDate;

        while ($this->isNotUtilDay($date)) {
            $date = Carbon::parse($date)->addDay();
        }

        return $date->toDateString();
    }

    public function nextUtilDay ()
    {
        $date = Carbon::now();

        while ($this->isNotUtilDay($date)) {
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

            if ($this->isUtilDay(Carbon::parse($date)->startOfMonth()->addDays($i))) {
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

            if ($this->isNotUtilDay(Carbon::parse($date)->startOfMonth()->addDays($i))) {
                $utilDaysCounter++;
            }
        }

        return $utilDaysCounter;
    }

    public function isPaymentDateValid ($date = null)
    {
        if (!$date) $date = Carbon::now();

        $currentTime = Carbon::now()->format('H:i:s');
        $natal       = Carbon::create(2021, 12, 24)->format('Y-m-d');
        $current     = Carbon::now()->format('Y-m-d');


        if ($current == $date->format('Y-m-d') && ($currentTime >= self::WITHDRAW_LIMIT_TIME || $currentTime >= self::PAYMENT_LIMIT_TIME))
            return false;
        else if ($this->isNotUtilDay($date))
            return false;
        else if ($natal == $current && $currentTime > "12:00")
            return false;
        else
            return true;
    }

    public function nextUtilDayForPayment ()
    {
        $date = Carbon::now();

        while (!$this->isPaymentDateValid($date)) {
            $date = Carbon::parse($date)->addDay()->startOfDay();
        }

        return $date;
    }

    public function nextUtilDayForTransfer ()
    {
        return Carbon::now();
    }
}
