# Getting Started

Brace is a simple template language written in PHP, using a Handlebars-style `{{double-brace}}` syntax.

## Requirements

PHP **8.4** or later.

## Installation

### Via Composer

```bash
composer require alexoliverwd/brace
```

### Manual include

```php
include __DIR__ . '/src/brace.php';
```

---

## Basic usage

### Echo a processed template

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

$brace->parse('home', [
    'title' => 'Welcome',
    'user'  => ['first' => 'John', 'last' => 'Doe'],
]);
```

`home.tpl`:

```html
<h1>{{title}}</h1>
<p>Hello, {{user->first}} {{user->last}}!</p>
```

Output:

```html
<h1>Welcome</h1>
<p>Hello, John Doe!</p>
```

---

### Parse a raw string

Use `parseInputString()` when you already have the template content as a PHP string rather than a file.

```php
<?php

use Brace\Parser;

$brace = new Parser();

$html = $brace->parseInputString(
    'Hello {{name}}!',
    ['name' => 'Alice'],
    false        // false = don't echo; collect output instead
)->return();

echo $html; // "Hello Alice!"
```

---

### Return the processed result as a string

Call `parse()` with `$render = false` and then chain `->return()`.

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

$html = $brace->parse('home', ['title' => 'Welcome'], false)->return();
```

---

### Compile to a file

`compile()` renders a template and writes the result to a file on disk.

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

$brace->compile(
    'home',              // template name (without extension)
    'dist/home.html',    // output file path
    ['title' => 'Welcome']
);
```

---

### Process multiple templates in one call

Pass a comma-separated list of template names to `parse()` or `compile()`.

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

// Renders header, then main, then footer in sequence
$brace->parse('header,main,footer', ['title' => 'My Site']);
```

---

### Reuse an instance with `clear()`

By default Brace accumulates output across calls on the same instance.
Call `clear()` before a new `parse()` to reset the internal buffer.

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

// First render
$brace->parse('page-one', ['title' => 'Page One'], false);
$first = $brace->return();

// Second render on the same instance
$brace->clear()->parse('page-two', ['title' => 'Page Two'], false);
$second = $brace->return();
```

---

## Instance variables

| Variable                   | Type      | Default                  | Description                                          |
|----------------------------|-----------|--------------------------|------------------------------------------------------|
| `remove_comment_blocks`    | `bool`    | `true`                   | Strip HTML comment blocks from rendered output.      |
| `template_path`            | `string`  | Current working directory| Directory from which template files are loaded.      |
| `template_ext`             | `string`  | `tpl`                    | File extension used when looking up template files.  |

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->remove_comment_blocks = false;   // keep <!-- comments --> in output
$brace->template_path = __DIR__ . '/views/';
$brace->template_ext  = 'html';          // load *.html files instead of *.tpl
```

---

## Next steps

| Topic | File |
|-------|------|
| Variables | [variables.md](variables.md) |
| Iterators (`each`) | [iterators.md](iterators.md) |
| Loops | [loops.md](loops.md) |
| Conditional statements | [conditionals.md](conditionals.md) |
| Including templates | [includes.md](includes.md) |
| Shortcodes | [shortcodes.md](shortcodes.md) |
| Callable methods | [callables.md](callables.md) |
| Array counting | [array-counting.md](array-counting.md) |
| Comment blocks | [comment-blocks.md](comment-blocks.md) |
