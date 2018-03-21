<?php
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

for ($i = 0; $i < sizeof($outputFileNames); $i++) { # Loop 6 times for each output file
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
    $out = '<records>';

    while (($data = fgetcsv($handle, 200, ",")) !== FALSE) {

      if ($data[0] == $locationId[$i]) { // Check the location id
        $rec = '<row count="' . $count . '" id="' . $row . '">';

        for ($c=0; $c < $cols; $c++) {
          $rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';
        }
        $rec .= '</row>';
        $count++;
        $out .= $rec;
      }
      $row++;
    }

    $out .= '</records>';
    # finish ##################################################

    # write out file
    file_put_contents("../csv and xml/air_quality.csv".$outputFileNames[$i], $out);
  }
  fclose($handle);
}
echo "....all done!";
?>
