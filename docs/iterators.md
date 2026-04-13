# Iterators

Iterators let you loop over an array in a template using `{{each}}...{{end}}` blocks or a compact in-line syntax.

---

## Basic `each` block

When no alias is given, the properties of each item are available directly.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$data = [
    'products' => [
        ['title' => 'Widget',  'price' => 9.99],
        ['title' => 'Gadget',  'price' => 24.99],
        ['title' => 'Doohickey', 'price' => 4.50],
    ],
];

$brace->parseInputString(
    "{{each products}}\n{{title}} — \${{price}}\n{{end}}",
    $data,
    false
);
```

Template (`.tpl` file):

```
{{each products}}
{{title}} — ${{price}}
{{end}}
```

Output:

```
Widget — $9.99
Gadget — $24.99
Doohickey — $4.50
```

---

## `each … as alias`

Use `as` to assign each row to a named variable, which is useful when nesting iterators.

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

$data = [
    'products' => [
        ['title' => 'Widget',   'categories' => ['Tools', 'Hardware']],
        ['title' => 'Gadget',   'categories' => ['Electronics']],
    ],
];

$brace->parse('product-list', $data);
```

`product-list.tpl`:

```
{{each products as product}}
<h2>{{product->title}}</h2>
<ul>
{{each product->categories as category}}
    <li>{{category}}</li>
{{end}}
</ul>
{{end}}
```

---

## Simple flat array with alias

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->parseInputString(
    "{{each names as name}}\n<p>{{name}}</p>\n{{end}}",
    ['names' => ['Alice', 'Bob', 'Charlie']],
    false
);
```

Output:

```html
<p>Alice</p>
<p>Bob</p>
<p>Charlie</p>
```

---

## `each … as key value`

Expose both the array key and the value in the same block.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$data = [
    'colors' => [
        'primary'   => 'Red',
        'secondary' => 'Blue',
        'accent'    => 'Green',
    ],
];

$brace->parseInputString(
    "{{each colors as key value}}\n<span data-key=\"{{key}}\">{{value}}</span>\n{{end}}",
    $data,
    false
);
```

Output:

```html
<span data-key="primary">Red</span>
<span data-key="secondary">Blue</span>
<span data-key="accent">Green</span>
```

You can also access the key via the built-in `_KEY` variable (see [Iteration data variables](#iteration-data-variables)):

```
{{each colors as color}}
<span data-key="{{_KEY}}">{{color}}</span>
{{end}}
```

---

## Nth children

Use the `_ITERATION` variable to target specific items.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$data = ['names' => ['Dave', 'John', 'Barry', 'Fred', 'Cindy']];
```

**First item:**

```
{{each names as name}}
<span{{_ITERATION === "is_first_item" ? " class=\"first\""}}>{{name}}</span>
{{end}}
```

**Last item:**

```
{{each names as name}}
<span{{_ITERATION === "is_last_item" ? " class=\"last\""}}>{{name}}</span>
{{end}}
```

**Nth item (e.g. second):**

```
{{each names as name}}
<span{{_ITERATION == 2 ? " class=\"second\""}}>{{name}}</span>
{{end}}
```

---

## Offset row ID

Use `offset_row_id` to start the row counter at a value other than 1. This
affects `_ROW_ID` and `_ITERATION`.

```php
<?php

use Brace\Parser;

$brace = new Parser();

// Row IDs will be 11, 12, 13 instead of 1, 2, 3
$brace->parseInputString(
    "{{each names as name offset_row_id 10}}\n{{_ROW_ID}}\n{{end}}",
    ['names' => ['Dave', 'John', 'Barry']],
    false
);
```

The offset can also reference a dataset variable:

```
{{each names as name offset_row_id offset}}
{{_ROW_ID}}
{{end}}
```

```php
$brace->parseInputString(
    "{{each names as name offset_row_id offset}}\n{{_ROW_ID}}\n{{end}}",
    ['names' => ['Dave', 'John', 'Barry'], 'offset' => 22],
    false
);
// Row IDs: 23, 24, 25
```

---

## Iteration data variables

The following variables are injected automatically into every iteration block:

| Variable     | Type      | Description                                                     |
|--------------|-----------|-----------------------------------------------------------------|
| `_ITERATION` | `string`  | `"is_first_item"`, `"is_last_item"`, or the integer position (2, 3 …) |
| `_ROW_ID`    | `integer` | 1-based row counter (affected by `offset_row_id`)               |
| `_KEY`       | mixed     | The original array key for the current item                     |
| `GLOBAL`     | `array`   | A copy of the outer dataset, accessible from inside the block   |

---

## In-line iterators

For simple cases you can write the entire iterator on a single line without an `{{each}}...{{end}}` block.

Use `__variable__` (double underscores) as placeholders in the template string.

### In-line iterator — value only

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '{{names as name "<li>__name__</li>"}}',
    ['names' => ['Alice', 'Bob', 'Charlie']],
    false
)->return();
```

Output:

```html
<li>Alice</li>
<li>Bob</li>
<li>Charlie</li>
```

### In-line iterator — key and value

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '{{colors as key value "<span data-key=\"__key__\">__value__</span>"}}',
    ['colors' => ['primary' => 'Red', 'secondary' => 'Blue']],
    false
)->return();
```

Output:

```html
<span data-key="primary">Red</span>
<span data-key="secondary">Blue</span>
```
