# Comment Blocks

Brace uses standard HTML comment syntax for comment blocks.
By default, comments are **stripped** from the rendered output.

---

## In-line comment

Anything between `<!--` and `-->` on a single line is removed.

```html
<!-- This comment will not appear in the output -->
<p>Hello {{name}}</p>
```

Output:

```html

<p>Hello Alice</p>
```

---

## Multi-line comment

A comment that spans several lines is also stripped entirely.

```html
<!--
    This is a multi-line comment.
    It can span as many lines as needed.
    Variables like {{name}} are not processed here.
-->
<p>Visible content</p>
```

Output:

```html

<p>Visible content</p>
```

---

## Keeping comment blocks in output

Set `remove_comment_blocks` to `false` on the parser instance to preserve all HTML comments.

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->remove_comment_blocks = false;

echo $brace->parseInputString(
    "<!-- still here -->\n<p>Hello</p>",
    [],
    false
)->return();
// Output:
// <!-- still here -->
// <p>Hello</p>
```

> **Note:** When `remove_comment_blocks` is `false`, comments are preserved literally — any `{{variables}}` inside them are still **not** processed.
