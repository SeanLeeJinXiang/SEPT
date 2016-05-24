<?php
include('lib/forecast.io.php');

$api_key = 'ce608d65c49616e17f2994b31a5f5c18';

$latitude = '-66.73';
$longitude = '112.83';
$units = 'auto';  // Can be set to 'us', 'si', 'ca', 'uk' or 'auto' (see forecast.io API); default is auto
$lang = 'en'; // Can be set to 'en', 'de', 'pl', 'es', 'fr', 'it', 'tet' or 'x-pig-latin' (see forecast.io API); default is 'en'

$forecast = new ForecastIO($api_key, $units, $lang);

// all default will be
// $forecast = new ForecastIO($api_key);


/*
 * GET CURRENT CONDITIONS
 */
$condition = $forecast->getCurrentConditions($latitude, $longitude);

echo 'Current temperature: '.$condition->getTemperature(). "\n";
echo 'Summary: '.$condition->getSummary()."\n";
echo 'Time: '.$condition->getTime("[Y]-[M]-[d]-[e]")."\n";
echo nl2br("\n");

/*
 * GET HOURLY CONDITIONS FOR TODAY
 */
$conditions_today = $forecast->getForecastToday($latitude, $longitude);

echo "\n\nTodays temperature:\n";

foreach($conditions_today as $cond) {

    echo $cond->getTime('H:i:s') . ': ' . $cond->getTemperature(). "\n";

}
echo nl2br("\n");

/*
 * GET DAILY CONDITIONS FOR NEXT 7 DAYS
 */
$conditions_week = $forecast->getForecastWeek($latitude, $longitude);

echo "\n\nConditions this week:\n";

foreach($conditions_week as $conditions) {

    echo $conditions->getTime('Y-m-d') . ': ' . $conditions->getMaxTemperature() . "\n";

}
echo nl2br("\n");
/*
 * GET HISTORICAL CONDITIONS
 */
$condition = $forecast->getHistoricalConditions($latitude, $longitude, '2010-10-10T14:00:00-0700');

echo "\n\nTemperatur 2010-10-10: ". $condition->getMaxTemperature(). "\n";


