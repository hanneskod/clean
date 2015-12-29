hanneskod/clean
===============

[![Packagist Version](https://img.shields.io/packagist/v/hanneskod/clean.svg?style=flat-square)](https://packagist.org/packages/hanneskod/clean)
[![Build Status](https://img.shields.io/travis/hanneskod/clean/master.svg?style=flat-square)](https://travis-ci.org/hanneskod/clean)
[![Quality Score](https://img.shields.io/scrutinizer/g/hanneskod/clean.svg?style=flat-square)](https://scrutinizer-ci.com/g/hanneskod/clean)

A clean (as in simple) data cleaner (as in validation tool)

Why?
----
Sometimes it's necessary to perform complex input validation, and a number of
tools exist for this purpose (think Respect\\Validation). At other times (arguably
most times) built in php functions such as the ctype-family and regular expressions
are just good enough. For me libraries like respect simply feels to heavy for these
times. Clean acts as a thin wrapper around callables and native php functions,
*in just 200 lines of code*, and allows you to filter and validate user input
using a simple and compact fluent interface.

Clean attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please submit a pull request.

Installation
------------
Install using [composer][]. Exists as `hanneskod/clean` in the [packagist][]
repository:

    composer require hanneskod/clean:^1.0

Clean requires php 5.4 or later and has no userland dependencies.

Usage
-----
Basic usage consists of grouping a set of [Rules](src/Rule.php) in a
[Validator](src/Validator.php):

<!-- @expectOutput 1234FOO -->
```php
use hanneskod\clean;

$validator = new clean\Validator([
    'foo' => (new clean\Rule)->pre('trim')->match('ctype_digit'),
    'bar' => (new clean\Rule)->match('ctype_alpha')->post('strtoupper')
]);

$tainted = [
    'foo' => ' 1234 ',
    'bar' => 'foo'
];

$clean = $validator->validate($tainted);

// outputs 1234
echo $clean['foo'];

// outputs FOO
echo $clean['bar'];
```

### Defining rules

TODO

`pre()`, `match()` and `post()`

även att det går att skicka med fler callables till dessa...

### Making input optional

TODO

`def()`

### Specifying custom exception messages

TODO

`msg()`

### Ignoring unknown input items

By default unkown intput items triggers exceptions:

<!-- @expectException hanneskod\clean\Exception -->
```php
use hanneskod\clean;

$validator = new clean\Validator([]);

// throws a clean\Exception as key is not present in validator
$validator->validate(['this-key-is-not-definied' => '']);
```

Use `ignoreUnknown()` to switch this functionality off:

<!-- @expectOutput /^1$/ -->
```php
use hanneskod\clean;

$validator = (new clean\Validator)->ignoreUnknown();
$clean = $validator->validate(['this-key-is-not-definied' => 'foobar']);

// outputs 1 (true) as the $clean array contains nothing
echo empty($clean);
```

### Nesting validators

Validators can be nested as follows:

<!-- @expectOutput bar -->
```php
use hanneskod\clean;

$validator = new clean\Validator([
    'nested' => new clean\Validator([
        'foo' => new clean\Rule
    ])
]);

$tainted = [
    'nested' => [
        'foo' => 'bar'
    ]
];

$clean = $validator->validate($tainted);

//outputs bar
echo $clean['nested']['foo'];
```

### Identifying a failing rule

For logging purposes it can be helpful to programmatically identify the failing
rule. This is done using the `getSourceRuleName()` in [`Exception`](src/Exception.php).

<!-- @expectOutput foo -->
```php
use hanneskod\clean;

$validator = new clean\Validator([
    'foo' => (new clean\Rule)->match('ctype_digit'),
]);

try {
    $validator->validate(['foo' => 'non-digits']);
} catch (clean\Exception $e) {
    // outputs foo
    echo $e->getSourceRuleName();
}
```

This also works well with nested validators.

<!-- @expectOutput foo::bar -->
```php
use hanneskod\clean;

$validator = new clean\Validator([
    'foo' => new clean\Validator([
        'bar' => (new clean\Rule)->match('ctype_digit')
    ])
]);

try {
    $validator->validate(['foo' => ['bar' => 'non-digits']]);
} catch (clean\Exception $e) {
    // outputs foo::bar
    echo $e->getSourceRuleName();
}
```

### Using the exception callback

TODO

`onException()`

Testing
-------
To run the unit tests at the command line, issue `composer install` and then
`phpunit` at the package root. This requires [composer][] to be available as
`composer`, and [PHPUnit][] to be available as `phpunit`.

Credits
-------
clean is covered under the WTFPL license.

@author Hannes Forsgård (hannes.forsgard@fripost.org)

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[composer]: http://getcomposer.org/
[packagist]: https://packagist.org/
[PHPUnit]: http://phpunit.de/manual/
