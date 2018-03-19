<?php

/*
  - Fraser Yates
  - Get user input from display_scatter_chart.html and create a json table for google charts to process
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

// get date and time from http request and return array of xml w/ correct date and time
$inputDate = $_REQUEST["date"];
$inputTime = $_REQUEST["time"];
$array = $xml->xpath("//reading[@time='$inputTime' and contains(@date,'$inputDate')]");

$dateFormat = "d/m/Y H:i:s";
$domDoc = new DOMDocument;

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
?>
