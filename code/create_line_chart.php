<?php

/*
  - Fraser Yates
  - Get user input from display_line_chart.html and create a json table for google charts to process
  - Use xpath to return an array of the requested input time and date - https://developer.mozilla.org/en-US/docs/Web/XPath/Functions/translate
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

// Reformat from yyyy-mm-dd to dd/mm/yyyy
$temp = new DateTime($inputDate);
$firstDate = date_format($temp, "d/m/Y");

// Reformat from yyyy-mm-dd to dd/mm/yyyy + 1 day
$temp2 = new DateTime($inputDate);
$temp2->add(new DateInterval("P1D")); // P1D means a period of 1 day
$secondDate = date_format($temp2, "d/m/Y");

// Send an xpath request to return an array of values between the user selected 24hr period.
// Use translate to remove ':' so xpath treats the date as an integer - https://developer.mozilla.org/en-US/docs/Web/XPath/Functions/translate
$array = $xml->xpath("//reading[(@date='$firstDate' and translate(@time, ':', '') >= translate('$inputTime', ':', '')) or(@date='$secondDate' and translate(@time, ':', '') <= translate('$inputTime', ':', ''))]");

// Sort the array using cmpDate method - https://www.w3schools.com/php/func_array_usort.asp
usort($array, "cmpDate");

$dateFormat = "d/m/Y H:i:s";
foreach ($array as $single) {
  $reading = simplexml_load_string($single->asXML());
  $date = DateTime::createFromFormat($dateFormat, ($reading->attributes()->date . " " . $reading->attributes()->time));
  $no2 = $reading->attributes()->val;

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
  $jsonRow[] = array("c" => $temp); //add row to new column
}
$jsonTable["rows"] = $jsonRow;
$jsonTableEncode = json_encode($jsonTable);

//echo JSON for google charts
echo $jsonTableEncode;

/*
 - Name: cmpDate
 - Parameters: $a and $b, adjacent xml entires
 - Returns: 0 if dates are equal
            -1 if first date is smaller than second
            1 if first date is larger than second
  - Comments: Creates a DOM tree objet to access date and compares them
*/
function cmpDate($a, $b) {
  $aXml = simplexml_load_string($a->asXML());
  $bXml = simplexml_load_string($b->asXML());

  $dateFormat = "d/m/Y H:i:s";
  $date1 = DateTime::createFromFormat($dateFormat, ($aXml->attributes()->date . " " . $aXml->attributes()->time));
  $date2 = DateTime::createFromFormat($dateFormat, ($bXml->attributes()->date . " " . $bXml->attributes()->time));

  if ($date1 == $date2) {
    return 0;
  } else if ($date1 < $date2) {
    return -1;
  } else {
    return 1;
  }
}
?>
