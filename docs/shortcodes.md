# Shortcodes

Shortcodes let you embed custom PHP logic into templates using a tag-like syntax: `[shortcode_name attribute="value"]`.

---

## Registering a shortcode

Call `regShortcode(name, callable)` before parsing. The callable receives an associative array of the parsed attributes.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->regShortcode('greeting', fn($attrs) => 'Hello, ' . $attrs['name'] . '!');

echo $brace->parseInputString(
    '[greeting name="World"]',
    [],
    false
)->return();
// Output: Hello, World!
```

---

## Shortcode without attributes

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->regShortcode('year', fn() => date('Y'));

echo $brace->parseInputString('[year]', [], false)->return();
// Output: 2024  (or whichever the current year is)
```

---

## Referencing a global function by name

You can pass the name of a global function as a string instead of a closure.

```php
<?php

use Brace\Parser;

$greet = function () {
    return 'Hello from a global function!';
};

$brace = new Parser();
$brace->regShortcode('greet', 'greet');   // pass the variable name as a string

echo $brace->parseInputString('[greet]', [], false)->return();
// Output: Hello from a global function!
```

---

## Shortcode attributes with dataset variables

Attribute values can reference `{{variables}}` from the active dataset.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->regShortcode('year', fn($attrs) => date('Y', strtotime($attrs['date'])));

echo $brace->parseInputString(
    '[year date="{{release_date}}"]',
    ['release_date' => '2024-06-15'],
    false
)->return();
// Output: 2024
```

---

## Accessing the full dataset inside a shortcode

Every shortcode callable receives a `GLOBAL` key in its attributes array that contains the entire dataset passed to `parse()`.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->regShortcode('user_card', function ($attrs) {
    $user = $attrs['GLOBAL']['user'] ?? [];
    return '<div class="card">' . ($user['first'] ?? '') . ' ' . ($user['last'] ?? '') . '</div>';
});

echo $brace->parseInputString(
    '[user_card]',
    ['user' => ['first' => 'Jane', 'last' => 'Doe']],
    false
)->return();
// Output: <div class="card">Jane Doe</div>
```

---

## Shortcodes inside an iterator

Shortcodes are evaluated after variable substitution, so they work inside `{{each}}` blocks.

`data-variables.tpl`:

```html
{{each names as name}}
[greet name="{{name}}"]
{{end}}
```

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

$brace->regShortcode('greet', fn($attrs) => $attrs['name']);

$brace->parse('data-variables', [
    'names' => ['Alex', 'John', 'Andre'],
]);
```

Output:

```
Alex
John
Andre
```

---

## WordPress compatibility

If the WordPress function `do_shortcode()` is available in the environment, Brace delegates shortcode execution to it automatically.

---

## Registering multiple shortcodes

`regShortcode()` returns the parser instance, so calls can be chained.

```php
$brace
    ->regShortcode('year',    fn() => date('Y'))
    ->regShortcode('version', fn() => '1.0.0')
    ->regShortcode('author',  fn() => 'Alex Oliver');
```
