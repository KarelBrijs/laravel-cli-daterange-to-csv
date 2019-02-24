# Laravel Artisan Command: Putting a daterange in a csv file

### Installation

1. Clone this repository like you normally would.
2. Run commands:
```
composer install
php -r "copy('.env.example', '.env');"
php artisan key:generate
```
3. Done.

### Usage

##### Signature:
```
php artisan generate:daterange-csv [options] [--] <startdate> <enddate>
```

##### Arguments:
```
  startdate                The startdate of the daterange.
  enddate                  The enddate of the daterange.
```

##### Options:
```
  --interval			Interval of days between dates (default: 1)
  --exclude_day			The days that will be excluded from the results. Values from 0 (sunday) to 6 (saturday). **
  --exclude_day_from_interval	The days that will be excluded from the interval count. Values from 0 (sunday) to 6 (saturday). **
  --exclude_date		The dates (dd/mm) that will be excluded from the results. **
  --exclude_day_of_the_month	The nth-days of each month that will be excluded from the results. **

  ** = multiple values are allowed, just use the option again with a different value
```

##### Export:
File will be exported to ```daterange.csv``` in the ```/public``` directory.

### Use case

If you want to for example generate a csv file containing a list of possible meeting dates where the requirements are as follows: 
- The meeting dates should be on each n-th day, based on the given interval
- A meeting can’t be in the weekend
- Saturday and Sunday should not be counted as days
- A meeting can’t be on the 25/12 or 1/1
- A meeting can’t be on the 5th of 15th of each month

Use the following command: 
```
php artisan generate:daterange-csv 01/01/2019 31/12/2019 --interval=2 --exclude_day=0 --exclude_day=6 --exclude_day_from_interval=0 --exclude_day_from_interval=6 --exclude_date=25/12 --exclude_date=01/01 --exclude_day_of_the_month=5 --exclude_day_of_the_month=15
```

