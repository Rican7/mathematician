# Mathematician

[![Build Status](https://travis-ci.org/Rican7/mathematician.svg?branch=master)](https://travis-ci.org/Rican7/mathematician)
[![Total Downloads](https://poser.pugx.org/Rican7/mathematician/downloads.png)](https://packagist.org/packages/Rican7/mathematician)
[![Latest Stable Version](https://poser.pugx.org/Rican7/mathematician/v/stable.png)](https://packagist.org/packages/Rican7/mathematician)

Mathematician is a PHP mathematics library for simpler, more reliable math operations... even on big numbers.

Face it: working with numbers in PHP is sub-par. This library aims to change that. The design goal of this library is to make it easier to work with numbers, regardless of size/precision or what extension the system has loaded. Portability and ease of use.

## Why?

Why create or even use a library like this? Well, working with numbers in PHP is quite the struggle.

Unlike many languages (Python, Ruby, etc), when dividing an `integer` by an `integer` you get a result that is of the type: `float`. In fact, there's actually no `//` operation like there is in JavaScript to do integer division.

Not only that, but what happens when you want to work with numbers that are larger than `PHP_INT_MAX`? When adding `1` to `PHP_INT_MAX`... you get a float again. Which means you instantly lose precision. Try to cast it back to an integer and you'll get a negative number (thanks overflow). Its rough... so naturally you turn to one of two extensions for working with big numbers: `bcmath` or `gmp`. Well, now your application/library is dependent on a compiled extension and is less portable, not to mention that they aren't equal in functionality (have fun with binary operations in BC Math).

Anyway, I think you get the point here. This library was born out of my frustrations with the above. Hopefully this library eases the pain for you so you don't have to experience what I did. :P

## Installation

1. [Get Composer](https://getcomposer.org/)
2. Add **"rican7/mathematician"** to your composer.json: `composer require rican7/mathematician 0.x.x`
3. Include the Composer autoloader `<?php require 'vendor/autoload.php';`

## Usage

Using mathematician is easy:

```php
use Mathematician\Number;

$number = Number::factory(100);

// Basic arithmetic
$number->add(10); // 110
$number->sub(10); // 90
$number->mul(10); // 1000
$number->div(10); // 10

// Powers
$number->pow(2); // 10000
$number->powMod(2, 3); // 1
$number->sqrt(); // 10
$number->mod(40); // 20

// Bitwise
$number->bitAnd(50); // 32
$number->bitOr(50); // 118
$number->bitXor(50); // 86
$number->bitNot(); // -101
$number->bitShiftLeft(2); // 400
$number->bitShiftRight(2); // 25

// Big numbers!!!!
$big_number = Number::factory(PHP_INT_MAX)
$big_number->pow(2)->toString(); // 85070591730234615847396907784232501249
```

## TODO

- [x] First release!
- [ ] Implement decimal adapter
- [ ] Cleaner cloning/serialization via magic method overrides
- [ ] More helper methods
