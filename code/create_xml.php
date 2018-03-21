<?php
/*
   - Fraser Yates
   - Convert csv to XML
*/

echo "working .. wait<br/>";
ob_flush();
flush();

$csvInputPath = "../csv and xml/air_quality.csv";
$outputFileNames = array("brislington.xml", "fishponds.xml", "parson_st.xml", "rupert_st.xml", "wells_rd.xml", "newfoundland_way.xml");
$locationId = array(3,6,8,9,10,11);

//Check if input file exists
if(!file_exists("../csv and xml/".$csvInputPath)) {
  echo "input file does not exist!<br/>";
  exit(0); //NOTE: exit and die are the same
}

//Check if output files exist
if(file_exists("../csv and xml/".$outputFileNames[0])) {
  echo "output files already exists!<br/>";
  exit(0);
}


if (($handle = fopen($csvInputPath, "r")) !== FALSE) {

  # define the tags - last col value in csv file is derived so ignore
  $header = array('id', 'desc', 'date', 'time', 'nox', 'no', 'no2', 'lat', 'long');

  # throw away the first line - field names
  fgetcsv($handle, 200, ",");

  # count the number of items in the $header array so we can loop using it
  $cols = count($header);

  #set record count to 1
  $count = 1;
  # set row count to 2 - this is the row in the original csv file
  $row = 2;

  # start ##################################################
  $outBris = '<records>';
  $outFish = '<records>';
  $outPars = '<records>';
  $outRup = '<records>';
  $outWells = '<records>';
  $outNewF = '<records>';

  while (($data = fgetcsv($handle, 200, ",")) !== FALSE) {

    if ($data[0] == $locationId[0]) { // Check the location id
      $rec = '<row count="' . $count . '" id="' . $row . '">';

      for ($c=0; $c < $cols; $c++) {
        $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
      }
      $rec .= '</row>';
      $count++;
      $outBris .= $rec;
    } else if ($data[0] == $locationId[1]) {
      $rec = '<row count="' . $count . '" id="' . $row . '">';

      for ($c=0; $c < $cols; $c++) {
        $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
      }
      $rec .= '</row>';
      $count++;
      $outFish .= $rec;
    } else if ($data[0] == $locationId[2]) {
      $rec = '<row count="' . $count . '" id="' . $row . '">';

      for ($c=0; $c < $cols; $c++) {
        $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
      }
      $rec .= '</row>';
      $count++;
      $outPars .= $rec;
    } else if ($data[0] == $locationId[3]) {
      $rec = '<row count="' . $count . '" id="' . $row . '">';

      for ($c=0; $c < $cols; $c++) {
        $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
      }
      $rec .= '</row>';
      $count++;
      $outRup .= $rec;
    } else if ($data[0] == $locationId[4]) {
      $rec = '<row count="' . $count . '" id="' . $row . '">';

      for ($c=0; $c < $cols; $c++) {
        $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
      }
      $rec .= '</row>';
      $count++;
      $outWells .= $rec;
    } else if ($data[0] == $locationId[5]) {
      $rec = '<row count="' . $count . '" id="' . $row . '">';

      for ($c=0; $c < $cols; $c++) {
        $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
      }
      $rec .= '</row>';
      $count++;
      $outNewF .= $rec;
    }
    $row++;
  }

  $outBris .= '</records>';
  $outFish .= '</records>';
  $outPars .= '</records>';
  $outRup .= '</records>';
  $outWells .= '</records>';
  $outNewF .= '</records>';
  # finish ##################################################

  # write out file
  file_put_contents("../csv and xml/".$outputFileNames[0], $outBris);
  file_put_contents("../csv and xml/".$outputFileNames[1], $outFish);
  file_put_contents("../csv and xml/".$outputFileNames[2], $outPars);
  file_put_contents("../csv and xml/".$outputFileNames[3], $outRup);
  file_put_contents("../csv and xml/".$outputFileNames[4], $outWells);
  file_put_contents("../csv and xml/".$outputFileNames[5], $outNewF);

}
fclose($handle);

echo "....all done!";
?>
