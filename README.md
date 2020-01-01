# hanneskod/clean

[![Packagist Version](https://img.shields.io/packagist/v/hanneskod/clean.svg?style=flat-square)](https://packagist.org/packages/hanneskod/clean)
[![Build Status](https://img.shields.io/travis/hanneskod/clean/master.svg?style=flat-square)](https://travis-ci.org/hanneskod/clean)
[![Quality Score](https://img.shields.io/scrutinizer/g/hanneskod/clean.svg?style=flat-square)](https://scrutinizer-ci.com/g/hanneskod/clean)

A clean (as in simple) data cleaner (as in validation tool)

## Why?

Sometimes it's necessary to perform complex input validation, and a number of
tools exist for this purpose (think Respect\\Validation). At other times
(arguably most times) built in php functions such as the ctype-family and
regular expressions are simply good enough. At these times pulling in a heavy
validation library to perform basic tasks can be unnecessarily complex.

Clean acts as a thin wrapper around callables and native php functions, *in less
than 100 logical lines of code*, and allows you to filter and validate user input
through a simple and compact fluent interface.

## Installation

```shell
composer require hanneskod/clean
```

Clean requires php 7.4 or later and has no userland dependencies.

## Usage

Basic usage consists of grouping a set of [Rules](src/Rule.php) in an
[ArrayValidator](src/ArrayValidator.php).

<!-- @example "basic usage" -->
<!-- @expectOutput -->
```php
use hanneskod\clean\ArrayValidator;
use hanneskod\clean\Rule;

$validator = new ArrayValidator([
    'foo' => (new Rule)->match('ctype_digit'),
    'bar' => (new Rule)->match('ctype_alpha'),
]);

$tainted = [
    'foo' => 'not-valid only digits allowed',
    'bar' => 'valid'
];

try {
    $validator->validate($tainted);
} catch (Exception $e) {
    echo $e->getMessage();
}
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

<!-- @example "defining rules" -->
<!-- @expectOutput FOOBAR -->
```php
use hanneskod\clean\Rule;

$rule = (new Rule)->pre('trim')->match('ctype_alpha')->post('strtoupper');

// outputs FOOBAR
echo $rule->validate('   foobar   ');
```

### Using the regexp matcher

The `Rule` validator comes with one special case matcher: `regexp()` to match
string input against a posix style regular expression (`preg_match()`).

<!-- @example "regexp()" -->
<!-- @expectOutput ABC -->
```php
use hanneskod\clean\Rule;

$rule = (new Rule)->regexp('/A/');

// outputs ABC
echo $rule->validate('ABC');
```

### Making input optional

Rules may define a default value using the `def()` method. The default value is
used as a replacement for `null`. This effectively makes the field optional in
an ArrayValidator setting.

<!-- @example "optional input" -->
<!-- @expectOutput baz -->
```php
use hanneskod\clean\ArrayValidator;
use hanneskod\clean\Rule;

$validator = new ArrayValidator([
    'key' => (new Rule)->def('baz')
]);

$data = $validator->validate([]);

// outputs baz
echo $data['key'];
```

### Specifying custom exception messages

When validation fails an exception is thrown with a generic message describing
the error. Each rule may define a custom exception message using the `msg()`
method to fine tune this behaviour.

<!-- @example "custom exception message" -->
<!-- @expectOutput "Expecting numerical input" -->
```php
use hanneskod\clean\Rule;

$rule = (new Rule)->msg('Expecting numerical input')->match('ctype_digit');

try {
    $rule->validate('foo');
} catch (Exception $e) {
    // outputs Expecting numerical input
    echo $e->getMessage();
}
```

### Ignoring unknown input items

By default unkown intput items triggers exceptions.

<!-- @example "exception on unknown array item" -->
<!-- @expectError -->
```php
use hanneskod\clean\ArrayValidator;

$validator = new ArrayValidator([]);

// throws a clean\Exception as key is not present in validator
$validator->validate(['this-key-is-not-definied' => '']);
```

Use `ignoreUnknown()` to switch this functionality off.

<!-- @example "ignoring an unknown array item" -->
<!-- @expectOutput empty -->
```php
use hanneskod\clean\ArrayValidator;

$validator = (new ArrayValidator)->ignoreUnknown();

$clean = $validator->validate(['this-key-is-not-definied' => 'foobar']);

// outputs empty
echo empty($clean) ? 'empty' : 'not empty';
```

### Nesting validators

ArrayValidators can be nested as follows:

<!-- @example "nesting validators" -->
<!-- @expectOutput bar -->
```php
use hanneskod\clean\ArrayValidator;
use hanneskod\clean\Rule;

$validator = new ArrayValidator([
    'nested' => new ArrayValidator([
        'foo' => new Rule
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

### Inspecting validation results using applyTo()

The `validate()` method throws an exception as soon as a match fails. This may
of course not always be what you want. You can inspect the validation result
directly by using the `applyTo()` method instead.

<!-- @example "inspecting validation results" -->
<!-- @expectOutput 12345 -->
```php
use hanneskod\clean\Rule;

$rule = (new Rule)->match('ctype_digit');

$result = $rule->applyTo('12345');

$result->isValid() == true;

// outputs 12345
echo $result->getValidData();
```

### Catching all of the failures

Individual errors can be accessed using the result object.

<!-- @example "catching all of the failures" -->
<!-- @expectOutput "failure 1failure 2" -->
```php
use hanneskod\clean\ArrayValidator;
use hanneskod\clean\Rule;

$validator = new ArrayValidator([
    '1' => (new Rule)->match('ctype_digit')->msg('failure 1'),
    '2' => (new Rule)->match('ctype_digit')->msg('failure 2'),
]);

// Both 1 and 2 will fail as they are not numerical
$result = $validator->applyTo(['1' => '', '2' => '']);

//outputs failure 1failure 2
foreach ($result->getErrors() as $errorMsg) {
    echo $errorMsg;
}
```

### Identifying a failing rule

<!-- @example "identifying a failing rule" -->
<!-- @expectOutput foo -->
```php
use hanneskod\clean\ArrayValidator;
use hanneskod\clean\Rule;

$validator = new ArrayValidator([
    'foo' => (new Rule)->match('ctype_digit'),
    'bar' => (new Rule)->match('ctype_digit'),
]);

$result = $validator->applyTo([
    'foo' => 'not-valid',
    'bar' => '12345'
]);

// outputs foo
echo implode(array_keys($result->getErrors()));
```

### Implementing custom validators

<!-- @example "implementing custom validators" -->
<!-- @expectOutput 1234 -->
```php
use hanneskod\clean\AbstractValidator;
use hanneskod\clean\Rule;
use hanneskod\clean\ValidatorInterface;

class NumberRule extends AbstractValidator
{
    protected function create(): ValidatorInterface
    {
        return (new Rule)->match('ctype_digit');
    }
}

// Outputs 1234
echo (new NumberRule)->validate('1234');
```
