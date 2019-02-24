<?php

namespace App\Repositories;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DaterangeRepository
{
	/**
	 * Returns an array of each date in the daterange
	 *
	 * @var Carbon\Carbon $startdate,
	 * @var Carbon\Carbon $enddate,
	 * @var int $interval,
	 * @var array $exclude_days,
	 * @var array $exclude_days_from_interval,
	 * @var array $exclude_dates,
	 * @var array $exclude_days_of_the_month,
	 *
	 * @return  array $dates
	 **/
	public static function getDates(Carbon $startdate, 
							Carbon $enddate, 
							int $interval, 
							array $exclude_days, 
							array $exclude_days_from_interval,
							array $exclude_dates,
							array $exclude_days_of_the_month)
	{
		$period = CarbonPeriod::create($startdate, $enddate);
		$dates = [];
		$i = 0;

		foreach ($period as $date) {
			if(!in_array($date->dayOfWeek, $exclude_days_from_interval)){
				
				if($i % $interval == 0){
					$dates[] = $date;
				}
				
				$i++;
			}
		}

		$dates = self::removeDatesFromArray($dates, $exclude_dates);
		$dates = self::removeDaysFromArray($dates, $exclude_days);
		$dates = self::removeDaysOfTheMonthFromArray($dates, $exclude_days_of_the_month);

		return $dates;
	}

	/**
	 * Returns an array where given dates are removed from the results.
	 *
	 * @var array $dates,
	 * @var array $exclude_dates,
	 *
	 * @return  array $dates
	 **/
	private static function removeDatesFromArray(array $dates, array $exclude_dates)
	{
		$result = [];
		foreach ($dates as $date) {
			$format = date('d/m', strtotime($date));
			if(!in_array($format, $exclude_dates)){
				$result[] = $date;
			}
		}

		return $result;
	} 

	/**
	 * Returns an array where given days are removed from the results.
	 *
	 * @var array $dates,
	 * @var array $exclude_days_of_the_month,
	 *
	 * @return  array $dates
	 **/
	private static function removeDaysFromArray(array $dates, array $exclude_days)
	{ 
		$result = [];
		foreach ($dates as $date) {
			if(!in_array($date->dayOfWeek, $exclude_days)){
				$result[] = $date;
			}
		}

		return $result;	
	} 

	/**
	 * Returns an array where given nth-days of the month are removed from the results.
	 *
	 * @var array $dates,
	 * @var array $exclude_days_of_the_month,
	 *
	 * @return  array $dates
	 **/
	private static function removeDaysOfTheMonthFromArray(array $dates, array $exclude_days_of_the_month)
	{ 
		$result = [];
		foreach ($dates as $date) {
			if(!in_array($date->day, $exclude_days_of_the_month)){
				$result[] = $date;
			}
		}

		return $result;	
	} 
}