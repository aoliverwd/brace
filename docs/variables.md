# Variables

Variables are written using double-brace syntax: `{{variable_name}}`.

Brace automatically trims whitespace inside the braces, so `{{ name }}`, `{{name}}`, and `{{ name}}` all resolve the same way.

---

## Simple variable

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '<p>Hello, {{name}}!</p>',
    ['name' => 'Alice'],
    false
)->return();
// Output: <p>Hello, Alice!</p>
```

---

## Nested variables

Access array keys with the `->` chain operator.

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '<p>{{user->first}} {{user->last}}</p>',
    [
        'user' => [
            'first' => 'John',
            'last'  => 'Doe',
        ],
    ],
    false
)->return();
// Output: <p>John Doe</p>
```

Chains can be arbitrarily deep:

```
{{address->city->postcode}}
```

---

## In-line "or" operator (`||`)

If a variable is missing or empty, fall back to the next option in the list.
The last item can be a quoted string literal to use as a hard-coded default.

```php
<?php

use Brace\Parser;

$brace = new Parser();

// 'name' is not in the dataset, so the fallback string is used
echo $brace->parseInputString(
    '<p>Hello, {{name || "Guest"}}!</p>',
    [],
    false
)->return();
// Output: <p>Hello, Guest!</p>
```

### Multiple fallback variables

```php
<?php

use Brace\Parser;

$brace = new Parser();

// 'name' is absent, 'fname' is present — 'fname' wins
echo $brace->parseInputString(
    '<p>Hello, {{name || fname || "Guest"}}!</p>',
    ['fname' => 'Dave'],
    false
)->return();
// Output: <p>Hello, Dave!</p>
```

Fallbacks can reference nested paths too:

```php
echo $brace->parseInputString(
    '<p>Hello, {{fname || user->first || "Guest"}}!</p>',
    ['user' => ['first' => 'Alice', 'last' => 'Smith']],
    false
)->return();
// Output: <p>Hello, Alice!</p>
```

---

## Lookup a value by an array field (`->?field[value]`)

Find the first record in an array where a given field matches a value, then
traverse further into that record.

Syntax: `{{array->?field[search_value]->property}}`

```php
<?php

use Brace\Parser;

$brace = new Parser();

$people = [
    ['title' => 'Mr',   'first' => 'John',  'last' => 'Smith'],
    ['title' => 'Miss', 'first' => 'Jane',  'last' => 'Doe'],
    ['title' => 'Dr',   'first' => 'David', 'last' => 'Jones'],
];

echo $brace->parseInputString(
    'Hi {{names->?first[Jane]->title}} {{names->?first[Jane]->last}}',
    ['names' => $people],
    false
)->return();
// Output: Hi Miss Doe
```

If no record matches, the variable resolves to an empty string.

```php
echo $brace->parseInputString(
    '{{names->?last[Brown]->first}}',
    ['names' => $people],
    false
)->return();
// Output: (empty string)
```

---

## Variables inside `<script>` tags

Brace processes `{{variables}}` inside `<script>` blocks, but deliberately skips block-level syntax (`{{if}}`, `{{each}}`, etc.) to avoid interfering with JavaScript code.

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '<script>console.log("{{name}}");</script>',
    ['name' => 'Dave'],
    false
)->return();
// Output: <script>console.log("Dave");</script>
```
