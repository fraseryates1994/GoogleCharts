<?php

/*
  - Fraser Yates
  - Normalise 6 xml files to 6 no2 xmls files
*/

$inputFiles = array("brislington.xml", "fishponds.xml", "parson_st.xml", "rupert_st.xml", "wells_rd.xml", "newfoundland_way.xml");
$outputFiles = array("brislington_no2.xml", "fishponds_no2.xml", "parson_st_no2.xml", "rupert_st_no2.xml", "wells_rd_no2.xml", "newfoundland_way_no2.xml");
$reader = new XMLReader;
$writer = new XMLWriter;
$doc = new DOMDocument; // Needed if using simplexml_import_dom
$count = 0; // Create count to get first row for location
$desc = "";
$lat = "";
$long = "";
$date = "";
$time = "";
$no2 = "";

//loop through input producing required output.
for($i = 0; $i < sizeof($inputFiles); $i++){
  echo "Reading $inputFiles[$i]"."<br/>";
  $count = 0; // Create count to get first row for location

  // Check if output file exists
  if(file_exists("../csv and xml/".$outputFiles[$i])) {
    echo "An output file already exists!";
    exit(0);
  }
  $reader->open("../csv and xml/".$inputFiles[$i]);
  $writer->openURI("../csv and xml/".$outputFiles[$i]);

  //Write initial XML
  $writer->startDocument('1.0','UTF-8');
  $writer->setIndent(4);
  $writer->startElement('data');
  $writer->writeAttribute('type', 'nitrogen dioxide');

  // move to the first row node
  while ($reader->read() && $reader->name !== 'row');

  // Iterate through rows
  while ($reader->name === 'row')
  {
    // either one should work
    // $node = new SimpleXMLElement($reader->readOuterXML());
    $node = simplexml_import_dom($doc->importNode($reader->expand(), true));

    // Get location information from first row
    if ($count == 0) {
      $desc = (String) $node->desc['val']; // get value from desc object
      $lat = (String) $node->lat['val']; // get value from lat object
      $long = (String) $node->long['val']; // get value from long object

      // Write to <location />
      $writer->startElement('Location');
      $writer->writeAttribute('id', $desc);
      $writer->writeAttribute('lat', $lat);
      $writer->writeAttribute('long', $long);
    }

    $date = (String) $node->date['val']; // get value from date object
    $time = (String) $node->time['val']; // get value from time object
    $no2 = (String) $node->no2['val']; // get value from no2 object

    // Write to <element />
    $writer->startElement('reading');
    $writer->writeAttribute('date', $date);
    $writer->writeAttribute('time', $time);
    $writer->writeAttribute('val', $no2);
    $writer->endElement();

    // go to next <row />
    $reader->next('row');
    $count++;
  }
  // End writer
  $writer->endDocument();
  echo "Done<br/>";
}
$writer->flush();
echo "Normalisation complete!";
?>
