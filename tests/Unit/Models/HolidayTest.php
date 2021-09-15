<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Database\Seeders\HolidaySeeder;
use Lara\Jarvis\Models\Holiday;
use Lara\Jarvis\Tests\TestCase;

class HolidayTest extends TestCase
{
    use RefreshDatabase;

    public function setUp (): void
    {
        parent::setUp();

        $knownDate = Carbon::create(2021, 4, 21, 18);
        Carbon::setTestNow($knownDate);

        $this->artisan('db:seed', ['--class' => HolidaySeeder::class]);
    }

    public function test_same_fillable()
    {
        $fillableTest = [
            "name", "type", "date",
        ];

        $fillable = (new Holiday())->getFillable();

        $this->assertEqualsCanonicalizing($fillableTest, $fillable);
    }

    public function test_list_all()
    {
        $this->assertDatabaseCount('holidays', 16);
    }

    public function test_is_today_holiday()
    {
        $response = (new Holiday())->isTodayHoliday();

        self::assertEquals($response, true);
    }

    public function test_is_today_not_holiday()
    {
        $response = (new Holiday())->isTodayNotHoliday();

        self::assertEquals($response, false);
    }

    public function test_is_today_util_day()
    {
        $response = (new Holiday())->isTodayUtilDay();

        self::assertEquals($response, false);
    }

    public function test_is_today_not_util_day()
    {
        $response = (new Holiday())->isTodayNotUtilDay();

        self::assertEquals($response, true);
    }

    /**
     * Tests if next util day is working.
     *
     * @return void
     */
    public function test_next_util_day_from()
    {
        $response = (new Holiday())->nextUtilDayFrom("2021-04-21");

        self::assertEquals("2021-04-22", $response);
    }

    /**
     * Tests if util days in month counter works.
     *
     * @return void
     */
    public function test_quantity_util_days_in_month()
    {
        $response = (new Holiday())->countUtilDaysInMonth("2021-04-20");

        self::assertEquals(20, $response);
    }

    /**
     * Tests if not util days in month counter works
     *
     * @return void
     */
    public function test_quantity_not_util_days_in_month()
    {
        $response = (new Holiday())->countNotUtilDaysInMonth("2021-04-20");

        self::assertEquals(10, $response);
    }
}
