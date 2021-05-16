# CastXML PHP Wrapper

CastXML is a C-family abstract syntax tree XML output tool.

This project is maintained by [Kitware](https://www.kitware.com/) in support 
of [ITK](https://itk.org/), the Insight Segmentation and Registration Toolkit.

## Installation

Library can be installed into any PHP application using Composer dependency manager.

- `composer require serafim/castxml`

In order to access pipe library make sure to include `vendor/autoload.php` in your file.

```php
<?php

require __DIR__ . '/vendor/autoload.php';
```

### Binaries

Don't forget to install the binary dependency. If you are looking for pre-built
binaries, or a compact way to build this project, please see 
[CastXMLSuperbuild](https://github.com/CastXML/CastXMLSuperbuild).

Also, some operating systems support installation using dependency managers, 
for example:

```sh
$ sudo apt update
$ apt install castxml
```

## Usage

To create a new CastXML parser, you can use one of the possible options.

```php
$parser = new \Serafim\CastXml\Parser();

// OR

$binary = '/path/to/binary/castxml';
$parser = new \Serafim\CastXml\Parser($binary);
```

> Windows OS is also supported, just write the path to CastXML in PATH
> variable or add the path to `castxml.exe` to the class constructor.

### Availability Check

For information on whether everything is OK, simply use the accessibility 
`isAvailable()` method.

```php
$parser = new \Serafim\CastXml\Parser();

$available = $parser->isAvailable();

// - bool(true)  - CastXML is available
// - bool(false) - CastXML is not available
```

### Version

For version information use methods `getVersion()` and `getClangVersion()`.

```php
$parser = new \Serafim\CastXml\Parser();

echo $parser->getVersion();
// For example: "0.4.2"

echo $parser->getClangVersion();
// For example: "11.0.0"
```

### Parsing

To parse the original header, just use the `parse()` method. The method takes 
one required `$pathname` and one optional `$cwd` string arguments.

```php
$parser = new \Serafim\CastXml\Parser();

$result = $parser->parse('/path/to/header.h');
// OR
$result = $parser->parse('/path/to/header.h', '/path/to/working_directory');
```

As a result, you will get a `Serafim\CastXml\Result` object.

```php
$parser = new \Serafim\CastXml\Parser();
$result = $parser->parse('/path/to/header.h');

echo $result->getContents(); // XML output
// OR
echo $result; // Same XML output
```

### Result Saving

```php
$parser = new \Serafim\CastXml\Parser();
$result = $parser->parse('/path/to/header.h');

// Save to file
$result->saveAs('/path/to/filename.xml');

// Save into directory
$result->saveIn('/path/to/directory');
```

### Result Parsing

```php
$parser = new \Serafim\CastXml\Parser();
$result = $parser->parse('/path/to/header.h');

// Using SimpleXML
$simplexml = $result->toSimpleXml();

// Using XML Reader
$reader = $result->toXmlReader();
```
