<?php

/*
  - Fraser Yates
  - Get user input from display_scatter_chart.html and create a json table for google charts to process
  - Use xpath to return an array of the requested input time and date - https://developer.mozilla.org/en-US/docs/Web/XPath/Functions/contains
*/

// get location from http request and open file
$inputFileName = "../csv and xml/".$_REQUEST["location"].".xml";
$xml = simplexml_load_file($inputFileName);

// get date and time from http request
$inputDate = $_REQUEST["date"];
$inputTime = $_REQUEST["time"];

// Send xpath request to return an array at the requested time and date
$array = $xml->xpath("//reading[@time='$inputTime' and contains(@date,'$inputDate')]");

// create json format
$jsonRow = array();
$jsonTable = array();
$jsonTable["cols"] = array(
  array("label" => "date/time", "type" => "date"),
  array("label" => "NO2", "type" => "number"),
  array("role" => "style", "type" => "string")
);

// Loop through XML and format a JSON string for google charts
foreach ($array as $single) {
  $reading = simplexml_load_string($single->asXML());

  // Create dateTime from xml
  $date = $reading->attributes()->date;
  $time = $reading->attributes()->time;
  $dateTime = $date . " " . $time;
  $dateAndTime= DateTime::createFromFormat("d/m/Y H:i:s", ($dateTime));

  # Convert dateAndTime to JSON format
  $temp = array();
  $jsonDate = "Date(";
  $year = date("Y", $dateAndTime->format("U")) . ", ";
  $month = (date("m", $dateAndTime->format("U")) -1) . ", "; // Remove 1 month because Google charts reads 0-11 not 1-12
  $day = date("d", $dateAndTime->format("U")) . ", ";
  $hour = date("H", $dateAndTime->format("U")) . ", ";
  $minute = date("i", $dateAndTime->format("U")) . ", ";
  $second = date("s", $dateAndTime->format("U")) . ")";

  $jsonDate .= $year.$month.$day.$hour.$minute.$second; // Concat Strings

  // Get no2 value
  $no2 = (int) $reading->attributes()->val;

  $colour = getColour($no2);

  $temp[] = array("v" => $jsonDate);
  $temp[] = array("v" => $no2);
  $temp[] = array("v" => $colour);
  $jsonRow[] = array("c" => $temp); //add row to new column
}

$jsonTable["rows"] = $jsonRow;
$jsonTableEncode = json_encode($jsonTable);

//echo out google charts
echo $jsonTableEncode;

/*
- Name: getColour
- Parameters: $no2
- Returns: hex colour string
- Comments: takes in no2 value and return the colour based on https://uk-air.defra.gov.uk/air-pollution/daqi
*/
function getColour($no2) {
  if (isLow($no2) == true) {
    if ($no2 < 68) {
      return "#66ff99";
    } else if ($no2 < 135) {
      return "#00ff00";
    } else {
      return "#00cc00";
    }
  } else if (isModerate($no2) == true) {
    if ($no2 < 268) {
      return "#ffff00";
    } else if ($no2 < 335) {
      return "#ffcc00";
    } else {
      return "#ff9900";
    }
  } else if (isHigh($no2) == true) {
    if ($no2 < 468) {
      return "#ff5050";
    } else if ($no2 < 535) {
      return "#ff0000";
    } else {
      return "#cc0000";
    }
  } else {
    return "#cc00ff";
  }
}

/*
- Name: isLow
- Parameters: $no2
- Returns: boolean
- Comments: takes in no2 value and return true if in low range
*/
function isLow($no2) {
  if ($no2 >= -100 && $no2 <= 200) { // - because csv may contain - values
    return true;
  }
  return false;
}

/*
- Name: isModerate
- Parameters: $no2
- Returns: boolean
- Comments: takes in no2 value and return true if in moderate range
*/
function isModerate($no2) {
  if ($no2 >= 201 && $no2 <= 400) {
    return true;
  }
  return false;
}

/*
- Name: isHigh
- Parameters: $no2
- Returns: boolean
- Comments: takes in no2 value and return true if in high range
*/
function isHigh($no2) {
  if ($no2 >= 401 && $no2 <= 600) {
    return true;
  }
  return false;
}

?>
