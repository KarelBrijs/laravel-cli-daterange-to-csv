<?php

namespace App\Console\Commands;

use App\Repositories\DaterangeRepository;
use App\Repositories\CsvRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Validator;

class GenerateDaterangeCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan generate:daterange-csv 01/01/2019 31/12/2019 -I=2 --exclude_day=0 --exclude_day=6 --exclude_day_from_interval=0 --exclude_day_from_interval=6 --exclude_date=25/12 --exclude_date=01/01 --exclude_day_of_the_month=5 --exclude_day_of_the_month=15
     * php artisan generate:daterange-csv 01/01/2019 31/03/2019 --interval=2 --exclude_day=0 --exclude_day=6 --exclude_day_from_interval=0 --exclude_day_from_interval=6 --exclude_date=25/12 --exclude_date=01/01 --exclude_day_of_the_month=5 --exclude_day_of_the_month=15
     *
     * @var string
     */
    protected $signature = 'generate:daterange-csv 
                            {startdate : The startdate of the daterange. } 
                            {enddate : The enddate of the daterange. } 
                            {--interval=1 : Interval of days between dates (default: 1) }
                            {--exclude_day=* : The days that will be excluded from the results. Accepts integer from 0 (sunday) to 6 (saturday). }
                            {--exclude_day_from_interval=* : The days that will be excluded from the interval count. Accepts integer from 0 (sunday) to 6 (saturday). }
                            {--exclude_date=* : The dates (dd/mm) that will be excluded from the results. }
                            {--exclude_day_of_the_month=* : The nth-days of each month that will be excluded from the results. }';

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
        $validator = Validator::make([
            'startdate' => $this->argument('startdate'),
            'enddate' => $this->argument('enddate'),
            'interval' => (int)$this->option('interval'),
            'exclude_days' => $this->option('exclude_day'),
            'exclude_days_from_interval' => $this->option('exclude_day_from_interval'),
            'exclude_dates' => $this->option('exclude_date'),
            'exclude_days_of_the_month' => $this->option('exclude_day_of_the_month'),
        ], [
            'startdate' => ['required', 'date_format:d/m/Y'],
            'enddate' => ['required', 'date_format:d/m/Y'],
            'interval' => ['nullable', 'numeric', 'digits_between:1,4'],
            'exclude_days.*' => ['numeric', 'in:0,1,2,3,4,5,6'], // 
            'exclude_days_from_interval.*' => ['numeric', 'in:0,1,2,3,4,5,6'],
            'exclude_dates.*' => ['date_format:d/m'],
            'exclude_days_of_the_month.*' => ['numeric', 'between:1,31'],
        ]);

        if ($validator->fails()) {
            $this->info('Daterange CSV not created. See following errors:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return;
        }

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

        $csv = $this->csvRepository->generateCsv($daterange, 'd/m/Y');

        $this->info('CSV file written to /public/daterange/dates.csv');

    }
}
