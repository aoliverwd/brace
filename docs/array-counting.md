# Array Counting

Brace can count the number of items in an array using the `COUNT()` function inside `{{ }}` expressions.

---

## Displaying a count

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '<p>Total items: {{COUNT(products)}}</p>',
    [
        'products' => [
            ['name' => 'Widget'],
            ['name' => 'Gadget'],
            ['name' => 'Doohickey'],
        ],
    ],
    false
)->return();
// Output: <p>Total items: 3</p>
```

---

## Using a count in a conditional

`COUNT()` can be used anywhere a value is expected in an `{{if}}` condition.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$data = [
    'items' => ['a', 'b', 'c'],
];

$brace->parseInputString(
    "{{if COUNT(items) == 3}}\n<p>There are exactly three items.</p>\n{{end}}",
    $data,
    false
);
// Output: <p>There are exactly three items.</p>
```

---

## Count with comparison operators

All standard [condition operators](conditionals.md#conditions-reference) work with `COUNT()`.

```
{{if COUNT(cart) > 0}}
<p>You have {{COUNT(cart)}} item(s) in your cart.</p>
{{else}}
<p>Your cart is empty.</p>
{{end}}
```

```
{{if COUNT(results) >= 10}}
<p>Showing first 10 of {{COUNT(results)}} results.</p>
{{end}}
```

---

## Nested array count

`COUNT()` works with nested paths using the `->` operator.

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '{{COUNT(order->items)}} item(s) ordered.',
    [
        'order' => [
            'items' => ['Book', 'Pen', 'Ruler'],
        ],
    ],
    false
)->return();
// Output: 3 item(s) ordered.
```
