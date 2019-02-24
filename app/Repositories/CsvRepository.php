<?php

namespace App\Repositories;

use League\Csv\Writer;
use SplTempFileObject;

class CsvRepository
{
	/**
	 * Returns an array of each date in the daterange
	 *
	 * @var array $dates,
	 * @var string $format
	 *
	 * @return void
	 **/
	public static function generateCsv(array $dates, string $format)
	{
		$header = ['meeting', 'date'];
		$records = [];
		$i = 0;
		foreach ($dates as $date) {
			$records[] = array($i, date($format, strtotime($date)));
			$i++;
		}

        $csv = Writer::createFromPath(public_path('daterange.csv'), "w");
		$csv->insertOne($header);
		$csv->insertAll($records);

	}
}