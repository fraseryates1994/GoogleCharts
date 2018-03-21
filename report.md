# Web Development Report
Fraser Yates 15024481

Contents:
1. Links
2. Dom Oriented Parsers vs. Stream Oriented Parsers
3. Graphs
4. References
## Links
Links can be found here:
- [XML and CSV files](https://github.com/fraseryates1994/GoogleCharts/tree/master/csv%20and%20xml "csv and xml files")
- [Code](https://github.com/fraseryates1994/GoogleCharts/tree/master/code "Html/ Javascript and php files")
- [Line and Scatter Visualisation](http://www.cems.uwe.ac.uk/~f2-yates/WebDev/code/index.html "index .html")

## Dom Oriented Parsers vs. Stream Oriented Parsers
PHP source code to extract air_quality.csv can be found [here](https://github.com/fraseryates1994/GoogleCharts/blob/master/code/create_xml.php "to create_xml") and the PHP source code to normalise further can be found [here](https://github.com/fraseryates1994/GoogleCharts/blob/master/code/create_no2.php "to create_no2").

### Dom Oriented Parsers
A DOM oriented parser are in-memory objects which represent the document tree and infoset of the XML document. Hence, the object represents a tree of the document, this lends itself well if the information you need is from child sibling or ancestor nodes. Once the object is in memory, navigation and parsing is easy. Therefore developers find DOM oriented parsers easier to code and more flexible. Furthermore, if often requires less code to implement - therefore, maintenance of the code is easier. 

DOM oriented parsers do not return anything until the entire text has been passed. Therefore, it is not useful for dynamic documents and documents which may be larger than the available memory in RAM. Iteratively creating objects causes DOM parsers to use large amounts of memory and are often slower than stream oriented parsers. Hence, this model requires good processor and memory requirements. Due to this, it is not good when the document size is large. Meaning the scalability of a project suffers. An iterative DOM parser can be implemented through:
```php
$z = new XMLReader;
$z->open('data.xml');
$doc = new DOMDocument;
while ($z->read() && $z->name !== 'row');

while ($z->name === 'row')
{
    $node = simplexml_import_dom($doc->importNode($z->expand(), true));

    var_dump($node->element_1);
    $z->next('row');
}
```
### Stream Oriented Parsers
A Stream oriented parser means XML infosets are parsed at application run-time. The main advantage of steam parsing documents is the output can be generated immediately. Therefore, allowing the XML infosets to be garbage collected.  Thus, this approach is better for large or dynamic documents which may not fit entirely into the available memory. For example, a twitter data mining application would be more suited for a stream oriented parser because of the large scale and dynamic nature of twitter. Furthermore, developers working on hardware with strict requirements would need to use a stream based parser due to its garbage collection capabilities. For example, phone developers - an important requirement for android or iOS developers is maximising battery life. Therefore, minimising the use of memory and the processor is necessary to fulfill this requirement. 

One disadvantage of using stream oriented parsers is you can only view one infoset at a time. This narrows the scope of your document. In certain situations it may be beneficial to keep previously parsed objects around. Secondly, you need to know what you’re processing before you start. Lastly, stream oriented parsers require userland code (code outside of the kernel) which is prone to error. Moreover, it gives you more lines of code to maintain. However, a stream parser can be built into a tree parser through building the tree along the way. However, this gives you more lines of code to maintain.

## Graphs
Visualisation of the graph can be found in the uwe cems server [here](http://www.cems.uwe.ac.uk/~f2-yates/WebDev/code/index.html "index .html"). The users journey is illustrated below through a state diagram:
![](https://github.com/fraseryates1994/GoogleCharts/blob/master/images/user%20state%20diagram.png)
- Both charts utilise xpath to return an array containing XML which corresponds to user input. ([XPATH](https://www.w3schools.com/xml/xpath_intro.asp "to xpath"))
- Both charts utilise AJAX through creating an xhttp object to send http requests to and from the front and back end. ([AJAX](https://www.w3schools.com/xml/ajax_intro.asp "ajax intro")). JSON data can be retrieved from the php through an xhttp object:
```javascript
xhttp.onreadystatechange = function() {
        if (xhttp.readyState == XMLHttpRequest.DONE && xhttp.status == 200) {
          jsonData = xhttp.responseText;
        }
      }
```
- Both charts utilise an extra column in the JSON string to set point colours on the graph. The colour correspond to the daily air quality index found at the [DEFRA site](https://uk-air.defra.gov.uk/air-pollution/daqi "to defra"). The JSON appears as:
```javascript
{"cols":[{"label":"date\/time","type":"date"},{"label":"NO2","type":"number"},{"role":"style","type":"string"}],
"rows":[{"c":[{"v":"Date(2016, 6, 10, 08, 00, 00)"},{"v":32},{"v":"#66ff99"}]}
```
- The No2 value is compared against the following function to return the hex colour value for Google charts to use:
```php
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
  } else if etc ...
```
- An extension of this project could include a pie chart. This could show the difference in NO2 between locations. NO2 values from a specific time and date could be gathered from all locations. A percentage could then be calulated. For example: 
```php
$brislingtonPercentage = ($brislingtonNo2 / $totalNo2) * 100;
```

### Scatter Chart
![](https://github.com/fraseryates1994/GoogleCharts/blob/master/images/scatter%20chart.png)
- PHP source code can be found [here](https://github.com/fraseryates1994/GoogleCharts/blob/master/code/create_scatter_chart.php "to create_scatter_chart") and the HTML/ Javascript can be found [here](https://github.com/fraseryates1994/GoogleCharts/blob/master/code/display_scatter_chart.html "to display_scatter_chart").

### Line Chart
![](https://github.com/fraseryates1994/GoogleCharts/blob/master/images/line%20chart.png)
- PHP source code can be found [here](https://github.com/fraseryates1994/GoogleCharts/blob/master/code/create_line_chart.php "to create_line_chart") and the HTML/ Javascript can be found [here](https://github.com/fraseryates1994/GoogleCharts/blob/master/code/display_scatter_chart.html "to display_line_chart")

## References
- [Why StAX](https://docs.oracle.com/javase/tutorial/jaxp/stax/why.html)
- [xpath](https://www.w3schools.com/xml/xpath_intro.asp)
- [ajax](https://www.w3schools.com/xml/ajax_intro.asp)
- [Defra](https://uk-air.defra.gov.uk/air-pollution/daqi)
- [Google Charts Server Side Code](https://developers.google.com/chart/interactive/docs/php_example)
