<?php

namespace App\Console\Commands;

use App\Repositories\DaterangeRepository;
use App\Repositories\CsvRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDaterangeCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan generate:daterange-csv 01/01/2019 31/12/2019 --I=2 --ED=0 --ED=6 --EDFI=0 --EDFI=6 --EDT=25/12 --EDT=01/01 --EDOTM=5 --EDOTM=15 
     * php artisan generate:daterange-csv 01/01/2019 31/03/2019 --interval=2 --exclude_day=0 --exclude_day=6 --exclude_day_from_interval=0 --exclude_day_from_interval=6 --exclude_date=25/12 --exclude_date=01/01 --exclude_day_of_the_month=5 --exclude_day_of_the_month=15
     *
     * @var string
     */
    protected $signature = 'generate:daterange-csv 
                            {startdate : The startdate of the daterange. } 
                            {enddate : The enddate of the daterange. } 
                            {--I|interval=1 : Interval of days between dates (default: 1) }
                            {--ED|exclude_day=* : The days that will be excluded from the results. Accepts integer from 0 (sunday) to 6 (saturday). }
                            {--EDFI|exclude_day_from_interval=* : The days that will be excluded from the interval count. Accepts integer from 0 (sunday) to 6 (saturday). }
                            {--EDT|exclude_date=* : The dates (dd/mm) that will be excluded from the results. }
                            {--EDOTM|exclude_day_of_the_month=* : The nth-days of each month that will be excluded from the results. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate a CSV file of a daterange.';

    /**
     * The name of the csv repository.
     *
     */
    protected $csvRepository;

    /**
     * The name of the daterange repository.
     *
     */
    protected $daterangeRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CsvRepository $csvRepository, DaterangeRepository $daterangeRepository)
    {
        parent::__construct();

        $this->csvRepository = $csvRepository;
        $this->daterangeRepository = $daterangeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startdate = Carbon::createFromFormat('d/m/Y', $this->argument('startdate'));
        $enddate = Carbon::createFromFormat('d/m/Y', $this->argument('enddate'));
        $interval = (int)$this->option('interval');
        $exclude_days = $this->option('exclude_day');
        $exclude_days_from_interval = $this->option('exclude_day_from_interval');
        $exclude_dates = $this->option('exclude_date');
        $exclude_days_of_the_month = $this->option('exclude_day_of_the_month');

        $daterange = $this->daterangeRepository->getDates(
                                                    $startdate, 
                                                    $enddate, 
                                                    $interval, 
                                                    $exclude_days, 
                                                    $exclude_days_from_interval, 
                                                    $exclude_dates, 
                                                    $exclude_days_of_the_month);

        $this->info(implode(',', $daterange));

        $csv = $this->csvRepository->generateCsv($daterange, 'd/m/Y');

        $this->info('CSV file written to /public/daterange/dates.csv');

    }
}
