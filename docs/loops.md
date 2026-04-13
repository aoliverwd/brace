# Loops

The `{{loop}}` block lets you repeat content a fixed number of times without needing an array in the dataset.

---

## Basic loop

Pass a single integer to repeat a block that many times (1 → N).

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->parseInputString(
    "{{loop 3}}\n<li>Item {{_KEY}}</li>\n{{end}}",
    [],
    false
);
```

Template:

```
{{loop 3}}
<li>Item {{_KEY}}</li>
{{end}}
```

Output:

```html
<li>Item 1</li>
<li>Item 2</li>
<li>Item 3</li>
```

---

## Ascending loop (`N to M`)

Use `from to to` syntax to specify both start and end values.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->parseInputString(
    "{{loop 1 to 5}}\n<li>{{_KEY}}</li>\n{{end}}",
    [],
    false
);
```

Template:

```
{{loop 1 to 5}}
<li>{{_KEY}}</li>
{{end}}
```

Output:

```html
<li>1</li>
<li>2</li>
<li>3</li>
<li>4</li>
<li>5</li>
```

---

## Descending loop

When the first value is greater than the second, the loop counts down.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->parseInputString(
    "{{loop 3 to 1}}\n<li>{{_KEY}}</li>\n{{end}}",
    [],
    false
);
```

Template:

```
{{loop 3 to 1}}
<li>{{_KEY}}</li>
{{end}}
```

Output:

```html
<li>3</li>
<li>2</li>
<li>1</li>
```

---

## Loop data variables

| Variable | Type      | Description             |
|----------|-----------|-------------------------|
| `_KEY`   | `integer` | Current iteration value |
