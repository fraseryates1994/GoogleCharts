<?php

/*
- Fraser Yates
- Get user input from display_line_chart.html and create a json table for google charts to process
- Use xpath to return an array of the requested input time and date - https://developer.mozilla.org/en-US/docs/Web/XPath/Functions/translate
*/

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

// create json format
$jsonRow = array();
$jsonTable = array();
$jsonTable["cols"] = array(
  array("label" => "date/time", "type" => "date"),
  array("label" => "NO2", "type" => "number"),
  array("role" => "style", "type" => "string")
);

foreach ($array as $single) {
  // Open Tree
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
  $month = (date("m", $dateAndTime->format("U")) -1)  . ", "; // Remove 1 month because Google charts reads 0-11 not 1-12
  $day = date("d", $dateAndTime->format("U")) . ", ";
  $hour = date("H", $dateAndTime->format("U")) . ", ";
  $minute = date("i", $dateAndTime->format("U")) . ", ";
  $second = date("s", $dateAndTime->format("U")) . ")";

  $jsonDate .= $year.$month.$day.$hour.$minute.$second; // concat String

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

  // Get datetime from aXML
  $aDate = $aXml->attributes()->date;
  $aTime = $aXml->attributes()->time;
  $aDateTime = $aDate . " " . $aTime;

  // get datetime from bxml
  $bDate = $bXml->attributes()->date;
  $bTime = $bXml->attributes()->time;
  $bDateTime = $bDate . " " . $bTime;

  $date1 = DateTime::createFromFormat("d/m/Y H:i:s", $aDateTime);
  $date2 = DateTime::createFromFormat("d/m/Y H:i:s", $bDateTime);

  if ($date1 == $date2) {
    return 0;
  } else if ($date1 < $date2) {
    return -1;
  } else {
    return 1;
  }
}

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
  if ($no2 >= -100 && $no2 <= 200) { // -100 because csv may contain - values
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
