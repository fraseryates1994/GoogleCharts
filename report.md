# Web Development Report
Fraser Yates 15024481

Contents:
1. Links to code
2. Dom Oriented Parsers vs. Stream Oriented Parsers
3. Graphs
## Links
Links can be found here:
- [Code](https://github.com/fraseryates1994/GoogleCharts/tree/master/code "Html/ Javascript and php files")
- [XML and CSV files](https://github.com/fraseryates1994/GoogleCharts/tree/master/csv%20and%20xml "csv and xml files")
## Dom Oriented Parsers vs. Stream Oriented Parsers
### Dom Oriented Parsers
A DOM oriented parser are in-memory objects which represent the document tree and infoset of the XML document. Hence, the object represents a tree of the document, this lends itself well if the information you need is from child sibling or ancestor nodes. Once the object is in memory, navigation and parsing is easy. Therefore developers find DOM oriented parsers easier to code and more flexible. Furthermore, if often requires less code to implement - therefore, maintenance of the code is easier. 

DOM oriented parsers do not return anything until the entire text has been passed. Therefore, it is not useful for dynamic documents and documents which may be larger than the available memory in RAM. Iteratively creating objects causes DOM parsers to use large amounts of memory and are often slower than stream oriented parsers. Hence, this model requires good processor and memory requirements. Due to this, it is not good when the document size is large. Meaning the scalability of a project suffers.
### Stream Oriented Parsers
A Stream oriented parser means XML infosets are parsed at application run-time. The main advantage of steam parsing documents is the output can be generated immediately. Therefore, allowing the XML infosets to be garbage collected.  Thus, this approach is better for large or dynamic documents which may not fit entirely into the available memory. For example, a twitter data mining application would be more suited for a stream oriented parser because of the large scale and dynamic nature of twitter. Furthermore, developers working on hardware with strict requirements would need to use a stream based parser due to its garbage collection capabilities. For example, phone developers - an important requirement for android or iOS developers is maximising battery life. Therefore, minimising the use of memory and the processor is necessary to fulfill this requirement. 

One disadvantage of using stream oriented parsers is you can only view one infoset at a time. This narrows the scope of your document. In certain situations it may be beneficial to keep previously parsed objects around. Secondly, you need to know what you’re processing before you start. Lastly, stream oriented parsers require userland code (code outside of the kernel) which is prone to error. Moreover, it gives you more lines of code to maintain. However, a stream parser can be built into a tree parser through building the tree along the way. However, this gives you more lines of code to maintain.
## Graphs
### Scatter Chart
blah
### Line Chart
blah
