<?php

/*
  - Fraser Yates
  - Get user input from display_line_chart.html and create a json table for google charts to process
*/

// create json format
$rows = array();
$table = array();
$table["cols"] = array(
  array("label" => "date/time", "type" => "date"),
  array("label" => "NO2", "type" => "number")
);

// get location from http request and open file
$inputFileName = "../csv and xml/".$_REQUEST["location"].".xml";
$xml = simplexml_load_file($inputFileName);

// get date and time from http request
$inputDate = $_REQUEST["date"];
$inputTime = $_REQUEST["time"];

// Reformat from yyyy-mm-dd to dd/mm/yyyy
$temp = new DateTime($inputDate);
$firstDate = date_format($temp, "d/m/Y");

// Reformat from yyyy-mm-dd to dd/mm/yyyy + 1 day
$temp2 = new DateTime($inputDate);
$temp2->add(new DateInterval("P1D")); // P1D means a period of 1 day
$secondDate = date_format($temp2, "d/m/Y");

// Send an xpath request ro return an array of values between the user selected 24hr period
$array = $xml->xpath("//reading[(@date='$firstDate' and translate(@time, ':', '') >= translate('$inputTime', ':', '')) or(@date='$secondDate' and translate(@time, ':', '') <= translate('$inputTime', ':', ''))]");

// Sort the array using cmpDate method
usort($array, "cmpDate");

$dateFormat = "d/m/Y H:i:s";
foreach ($array as $single) {
  $reading = simplexml_load_string($single->asXML());
  $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date . " " . $reading->attributes()->time));
  $val = $reading->attributes()->val;

  # create json string (for date)
  $temp = array();
  $googleChartsJSONDate = "Date(";
  $googleChartsJSONDate .= date("Y", $date->format("U")) . ", ";
  $googleChartsJSONDate .= (date("m", $date->format("U")) - 1) . ", ";
  $googleChartsJSONDate .= date("d", $date->format("U")) . ", ";
  $googleChartsJSONDate .= date("H", $date->format("U")) . ", ";
  $googleChartsJSONDate .= date("i", $date->format("U")) . ", ";
  $googleChartsJSONDate .= date("s", $date->format("U")) . ")";

  $temp[] = array("v" => $googleChartsJSONDate); //add val
  $temp[] = array("v" => (int) $val); //add val
  $rows[] = array("c" => $temp); //add row to new column
}
$table["rows"] = $rows;
$tableJSON = json_encode($table);

//echo out for ayjax
echo $tableJSON;

function cmpDate($a, $b) {
  $reading1 = simplexml_load_string($a->asXML());
  $reading2 = simplexml_load_string($b->asXML());

  $dateFormat = "d/m/Y H:i:s";
  $date1 = DateTime::createFromFormat($dateFormat, ($reading1->attributes()->date . " " . $reading1->attributes()->time));
  $date2 = DateTime::createFromFormat($dateFormat, ($reading2->attributes()->date . " " . $reading2->attributes()->time));

  if ($date1 == $date2) {
    return 0;
  } else if ($date1 < $date2) {
    return -1;
  } else {
    return 1;
  }
}
?>
