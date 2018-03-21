<?php

/*
  - Fraser Yates
  - Get user input from display_scatter_chart.html and create a json table for google charts to process
  - Use xpath to return an array of the requested input time and date - https://developer.mozilla.org/en-US/docs/Web/XPath/Functions/contains
*/

// create json format
$jsonRow = array();
$jsonTable = array();
$jsonTable["cols"] = array(
  array("label" => "date/time", "type" => "date"),
  array("label" => "NO2", "type" => "number")
);

// get location from http request and open file
$inputFileName = "../csv and xml/".$_REQUEST["location"].".xml";
$xml = simplexml_load_file($inputFileName);

// get date and time from http request
$inputDate = $_REQUEST["date"];
$inputTime = $_REQUEST["time"];

// Send xpath request to return an array at the requested time and date
$array = $xml->xpath("//reading[@time='$inputTime' and contains(@date,'$inputDate')]");

$dateFormat = "d/m/Y H:i:s";

// Loop through XML and format a JSON string for google charts
foreach ($array as $single) {
  $reading = simplexml_load_string($single->asXML()); // Get XML
  $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date . " " . $reading->attributes()->time)); // Get date from XML
  $no2 = $reading->attributes()->val; // Get NO2 from XML

  # create date json
  $temp = array();
  $jsonDate = "Date(";
  $year = date("Y", $date->format("U")) . ", ";
  $month = (date("m", $date->format("U")) - 1) . ", ";
  $day = date("d", $date->format("U")) . ", ";
  $hour = date("H", $date->format("U")) . ", ";
  $minute = date("i", $date->format("U")) . ", ";
  $second = date("s", $date->format("U")) . ")";

  // Concat
  $jsonDate .= $year.$month.$day.$hour.$minute.$second;

  $temp[] = array("v" => $jsonDate);
  $temp[] = array("v" => (int) $no2);
  $jsonRow[] = array("c" => $temp);
}

$jsonTable["rows"] = $jsonRow;
$jsonTableEncode = json_encode($jsonTable);

//echo out google charts
echo $jsonTableEncode;
?>
