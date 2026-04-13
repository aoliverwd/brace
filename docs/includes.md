# Including Templates

Use the `[@include]` directive to embed one template file inside another.

---

## Basic include

```html
[@include footer]
```

Brace looks for `footer.tpl` (or whatever extension is configured via `template_ext`) in the `template_path` directory and inserts its rendered content at that point.

---

## Multiple includes on one line

Space-separate the template names to include several files in sequence.

```html
[@include header footer]
```

---

## Dynamic include with a variable

The template name can be a `{{variable}}` that resolves at render time.

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

$brace->parse('layout', ['section' => 'hero'], false);
```

`layout.tpl`:

```html
<main>
[@include {{section}}]
</main>
```

This will include `hero.tpl`.

---

## Include from PHP

You can also call `parse()` with multiple template names directly from PHP:

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

// Render header, then content, then footer in one shot
$brace->parse('header,content,footer', ['title' => 'My Page']);
```

---

## Nested includes

Included templates may themselves contain `[@include]` directives.

`page.tpl`:

```html
[@include header]
<main>
[@include content]
</main>
[@include footer]
```

`header.tpl`:

```html
<header>
    <h1>{{title}}</h1>
    [@include nav]
</header>
```

All included templates share the same dataset that was passed to the top-level `parse()` call.
