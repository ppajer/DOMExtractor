# PHP-DOM-Extractor

A PHP library for extracting data from a HTML or XML DOM document into any user-defined data structure, based on user-defined extraction rules. 

## Usage

### Install

Simply download the repo and `include` the class in your project.

### Defining extraction rules

Rules are simple PHP arrays which denote where the extractor must look for their value. They consist of a `key` to store the output in, and an XPath `selector` to match the element required. By default the element's text value will be returned, unless you specify an attribute to return instead. All instruction keys for the extractor are prefixed with a `@` and will be ignored in the output.

#### Basic query & attributes

The package uses XPath selector syntax for getting values from document nodes, including text and attribute nodes. The most basic rule could be written as:

```
array(
	'exampleKey' => array(
		'@selector' => '//title'
	)
)

// Will return:

array(
	'exampleKey' => 'Example Title'
)
```

If the data you're looking for is inside an element attribute, specify it in the selector as per XPath syntax:

```
array(
	'exampleKey' => array(
		'@selector' => '//h1/@class'
	)
)

// Will return: 

array(
	'exampleKey' => 'h1 green-text site-heading'
)
```

#### Lists & nested data

If you need to parse multiple values for a single key, or look for nested data, you can use the `@each` instruction, and nest as many levels of instructions as your memory limit allows:

```
array(
	'exampleKey' => array(
		'@selector' => '//div[@class="some-list-item"]',
		'@each' => array(
			'listItemTitle' => array(
					'@selector' => '//h3'
			),
			'listItemLink' => array(
					'@selector' => '//a/@href'
			),
			'listItemImages' => array(
					'@selector' => '//div[@class="carousel-item"]',
					'@each' => array(
						'src' => array(
							'@selector' => '//img/@src'
						)
					)
			)
		)
	)
)
```

This will return an array where `exampleKey` is an array containing arrays of data about the individual items in the list: in this example, the text content of each `h3` tag, the `href` attribute of each `a` element, and the `src` attribute of every `img` element.

```
array(
	'exampleKey' => array(
		array(
			'listItemTitle' => 'Some title',
			'listItemLink' => 'https://...',
			'listItemImages' => array(
				array('src' => 'https://...'),
				array('src' => 'https://...'),
				...
			)
		),
		array(
			'listItemTitle' => 'Some other title',
			'listItemLink' => 'https://...',
			'listItemImages' => array(
				array('src' => 'https://...'),
				array('src' => 'https://...'),
				...
			)
		),
		...
	)
)
```

### Setting up the rules

Once your rules are ready, you can pass them either to the instance by calling `setRules`, or the constructor as first argument. For convenience, the extractor can also take its instructions as either a JSON string or from an external JSON file as a path.

```

$rules = /* array or JSON string or file path */;

// Constructor 
$extractor = new DOM_Extractor($rules);

// OR Instance
$extractor = new DOM_Extractor();
$extractor->setRules($rules);

```

### Loading the document

Once everything is set, you are ready to load the document to parse and start extraction. As with passing the rules, here too you have the option of using the constructor's second argument or the dedicated `load` method.

```

$html = file_get_contents('https://...');

// Constructor 
$extractor = new DOM_Extractor($rules, $html);

// OR Instance
$extractor = new DOM_Extractor();
$extractor->load($html);

```

### Complete example

```

$rules = 'some/path/to/rules.json';
$html = file_get_contents('https:/...');

// Constructor method
$extractor = new DOM_Extractor($rules, $html);
$data = $extractor->parse();

// Instance method
$extractor = new DOM_Extractor;
$extractor->setRules($rules);
$extractor->load($html);
$data = $extractor->parse();

// Also supports method chaining:
$extractor = new DOM_Extractor
$data = $extractor->setRules($rules)->load($html)->parse();

˙``
