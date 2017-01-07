hanneskod/clean
===============

[![Packagist Version](https://img.shields.io/packagist/v/hanneskod/clean.svg?style=flat-square)](https://packagist.org/packages/hanneskod/clean)
[![Build Status](https://img.shields.io/travis/hanneskod/clean/master.svg?style=flat-square)](https://travis-ci.org/hanneskod/clean)
[![Quality Score](https://img.shields.io/scrutinizer/g/hanneskod/clean.svg?style=flat-square)](https://scrutinizer-ci.com/g/hanneskod/clean)

A clean (as in simple) data cleaner (as in validation tool)

Why?
----
Sometimes it's necessary to perform complex input validation, and a number of
tools exist for this purpose (think Respect\\Validation). At other times
(arguably most times) built in php functions such as the ctype-family and
regular expressions are simply good enough. At these times pulling in a heavy
validation library to perform basic tasks can be unnecessarily complex.

Clean acts as a thin wrapper around callables and native php functions, *in less
than 200 lines of code*, and allows you to filter and validate user input
through a simple and compact fluent interface.

Installation
------------
Install using [composer][]. Exists as `hanneskod/clean` in the [packagist][]
repository:

    composer require hanneskod/clean:^1.0

Clean requires php 5.4 or later and has no userland dependencies.

Clean attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please submit a pull request.

Usage
-----
Basic usage consists of grouping a set of [Rules](src/Rule.php) in an
[ArrayValidator](src/ArrayValidator.php).

<!-- @expectOutput 1234FOO -->
```php
use hanneskod\clean;

$validator = new clean\ArrayValidator([
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

Rules are defined using the `pre()`, `match()` and `post()` methods.

1. `pre()` takes any number of `callable` arguments to act as pre-match
    filters. Filters take an argument and return it in it's filtered state.
1. `post()` takes any number of `callable` arguments to act as post-match
    filters. Filters take an argument and return it in it's filtered state.
1. `match()` takes any number of `callable` arguments to act as validators. The
    callable should take an argument and return true if the argument is valid
    and false if it is not.

A rule definition might look like this:

<!-- @expectOutput FOOBAR -->
```php
use hanneskod\clean\Rule;

$rule = (new Rule)->pre('trim')->match('ctype_alpha')->post('strtoupper');

// outputs FOOBAR
echo $rule->validate('   foobar   ');
```

### Making input optional

Rules may define a default value using the `def()` method. The default value is
used as a replacement for `null`. This effectively makes the field optional in
an ArrayValidator setting.

<!-- @expectOutput baz -->
```php
use hanneskod\clean;

$validator = new clean\ArrayValidator([
    'key' => (new clean\Rule)->def('baz')
]);

$data = $validator->validate([]);

// outputs baz
echo $data['key'];
```

### Specifying custom exception messages

When validation fails an exception is thrown with a generic message describing
the error. Each rule may define a custom exception message using the `msg()`
method to fine tune this behaviour.

<!-- @expectOutput "Expecting numerical input" -->
```php
use hanneskod\clean;

$rule = (new clean\Rule)->msg('Expecting numerical input')->match('ctype_digit');

try {
    $rule->validate('foo');
} catch (clean\Exception $e) {
    // outputs Expecting numerical input
    echo $e->getMessage();
}
```

### Ignoring unknown input items

By default unkown intput items triggers exceptions.

<!-- @expectException hanneskod\clean\Exception -->
```php
use hanneskod\clean;

$validator = new clean\ArrayValidator([]);

// throws a clean\Exception as key is not present in validator
$validator->validate(['this-key-is-not-definied' => '']);
```

Use `ignoreUnknown()` to switch this functionality off.

<!-- @expectOutput empty -->
```php
use hanneskod\clean;

$validator = (new clean\ArrayValidator)->ignoreUnknown();
$clean = $validator->validate(['this-key-is-not-definied' => 'foobar']);

// outputs empty
echo empty($clean) ? 'empty' : 'not empty';
```

### Nesting validators

ArrayValidators can be nested as follows:

<!-- @expectOutput bar -->
```php
use hanneskod\clean;

$validator = new clean\ArrayValidator([
    'nested' => new clean\ArrayValidator([
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

For logging purposes it can be helpful to programmatically identify a failing
rule. This is done using the `getSourceValidatorName()` in
[`Exception`](src/Exception.php).

<!-- @expectOutput foo -->
```php
use hanneskod\clean;

$validator = new clean\ArrayValidator([
    'foo' => (new clean\Rule)->match('ctype_digit'),
]);

try {
    $validator->validate(['foo' => 'non-digits']);
} catch (clean\Exception $e) {
    // outputs foo
    echo $e->getSourceValidatorName();
}
```

This also works well with nested validators.

<!-- @expectOutput foo::bar -->
```php
use hanneskod\clean;

$validator = new clean\ArrayValidator([
    'foo' => new clean\ArrayValidator([
        'bar' => (new clean\Rule)->match('ctype_digit')
    ])
]);

try {
    $validator->validate(['foo' => ['bar' => 'non-digits']]);
} catch (clean\Exception $e) {
    // outputs foo::bar
    echo $e->getSourceValidatorName();
}
```

### Using the exception callback

Validators throws exceptions as soon as a match fails. This may of course not
always be what you want and this behaviour can be overridden by specifying an
`onException()` callback (both at the Rule and ArrayValidator level).

This can for example be used to set default values used on failure only.

<!-- @expectOutput 12345 -->
```php
use hanneskod\clean\Rule;

$rule = (new Rule)->match('ctype_digit')->onException(function () {
    return '12345';
});

// outputs 12345
echo $rule->validate('these are no digits');
```

Or to compose a list of all failures.

<!-- @expectOutput "failure onefailure two" -->
```php
use hanneskod\clean;

$validator = new clean\ArrayValidator([
    'foo' => (new clean\Rule)->match('ctype_digit')->msg('failure one'),
    'bar' => (new clean\Rule)->match('ctype_digit')->msg('failure two')
]);

$exceptions = [];

// Note the typehint using \Exception, any exception thrown in a filter or
// matcher is redirected here, so typehinting on clean\Exception is a misstake..
$validator->onException(function (\Exception $e) use (&$exceptions) {
    $exceptions[] = $e;
});

// Both foo and bar will fail as they are not numerical
$validator->validate(['foo' => '', 'bar' => '']);

//outputs failure onefailure two
foreach ($exceptions as $e) {
    echo $e->getMessage();
}
```

Testing
-------
To run the unit tests at the command line, issue `composer install` and then
`phpunit` at the package root. This requires [composer][] to be available as
`composer`, and [PHPUnit][] to be available as `phpunit`.

Credits
-------
Clean is covered under the WTFPL license.

@author Hannes Forsgård (hannes.forsgard@fripost.org)

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[composer]: http://getcomposer.org/
[packagist]: https://packagist.org/
[PHPUnit]: http://phpunit.de/manual/
