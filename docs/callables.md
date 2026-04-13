# Callable Methods

Callable methods let you invoke custom PHP functions directly from template content using a function-call syntax: `name(content)`.

Unlike [shortcodes](shortcodes.md), callables are matched against any function-call-style text in the rendered output — not just special `[tag]` blocks.

---

## Registering a callable

Use `registerCallable(name, callable)` before parsing. The callable receives the content passed between the parentheses (stripped of surrounding quotes).

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->registerCallable('shout', fn($text) => strtoupper($text));

echo $brace->parseInputString(
    'shout("hello world")',
    [],
    false
)->return();
// Output: HELLO WORLD
```

---

## Callable without arguments

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->registerCallable('foo', fn() => 'bar');

echo $brace->parseInputString('foo()', [], false)->return();
// Output: bar
```

---

## Callable with a double-quoted argument

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->registerCallable('echo_it', fn($content) => $content);

echo $brace->parseInputString('echo_it("hello")', [], false)->return();
// Output: hello
```

---

## Callable with a single-quoted argument

```php
echo $brace->parseInputString("echo_it('hello')", [], false)->return();
// Output: hello
```

---

## Callable with an unquoted argument

```php
echo $brace->parseInputString('echo_it(hello)', [], false)->return();
// Output: hello
```

---

## Callable with nested parentheses in the argument

If the argument itself contains parentheses, the matched content includes them verbatim.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->registerCallable('wrap', fn($content) => $content);

echo $brace->parseInputString('wrap(bar(foo))', [], false)->return();
// Output: bar(foo)
```

---

## Registering multiple callables

`registerCallable()` returns the parser instance, so calls can be chained.

```php
$brace
    ->registerCallable('upper',   fn($s) => strtoupper($s))
    ->registerCallable('lower',   fn($s) => strtolower($s))
    ->registerCallable('reverse', fn($s) => strrev($s));
```

---

## Combining callables with variables

Because callables are resolved after variable substitution, you can pass a variable's value through a callable by first rendering the variable into the template string.

`template.tpl`:

```
upper({{username}})
```

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

$brace->registerCallable('upper', fn($s) => strtoupper($s));

$brace->parse('template', ['username' => 'alice']);
// Output: ALICE
```

> **Note:** Callables are matched on the final rendered line, so the content between parentheses is the literal string that results after all variable substitution has already occurred.
