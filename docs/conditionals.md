# Conditional Statements

Brace supports block-level `{{if}}` statements and compact in-line conditions.

---

## `if` block

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->parseInputString(
    "{{if first_name EXISTS}}\n<p>Hello {{first_name}}</p>\n{{end}}",
    ['first_name' => 'John'],
    false
);
// Output: <p>Hello John</p>
```

Template form:

```
{{if first_name EXISTS}}
<p>Hello {{first_name}}</p>
{{end}}
```

---

## `if … else`

```php
<?php

use Brace\Parser;

$brace = new Parser();

$brace->parse('greeting', ['first_name' => 'John', 'last_name' => 'Doe']);
```

`greeting.tpl`:

```
{{if first_name EXISTS}}
Hello {{first_name}} {{last_name}}
{{else}}
Name does not exist
{{end}}
```

---

## `if … elseif … else`

Multiple branches can be chained with `{{elseif}}`.

```
{{if name->first && name->last}}
Hello {{name->first}} {{name->last}}
{{elseif name->first}}
Hello {{name->first}}
{{elseif name->last}}
Hello Mr {{name->last}}
{{else}}
Name does not exist
{{end}}
```

```php
<?php

use Brace\Parser;

$brace = new Parser();
$brace->template_path = __DIR__ . '/templates/';

// Only last name is available — renders "Hello Mr Doe"
$brace->parse('greeting', ['name' => ['last' => 'Doe']]);
```

---

## Conditions inside iterators

Conditions work inside `{{each}}` blocks and have access to the iteration variables.

```
{{each names as name}}
    {{if _ITERATION === "is_first_item"}}
        <span class="first" data-id="{{_ROW_ID}}">{{name}}</span>
    {{elseif _ITERATION === "is_last_item"}}
        <span class="last" data-id="{{_ROW_ID}}">{{name}}</span>
    {{elseif _ITERATION == 2}}
        <span class="second" data-id="{{_ROW_ID}}">{{name}}</span>
    {{else}}
        <span data-id="{{_ROW_ID}}">{{name}}</span>
    {{end}}
{{end}}
```

---

## AND / OR conditions

Combine multiple conditions with `&&` (AND) and `||` (OR).

```
{{if first_name EXISTS && first_name == "John"}}
<p>My first name is {{first_name}}</p>
{{else}}
<p>Please enter your first name</p>
{{end}}
```

```
{{if role == "admin" || role == "editor"}}
<p>You have write access</p>
{{end}}
```

---

## Static method as a condition

Any static PHP method that returns a boolean can be used as a condition directly in the template.

```
{{if \Namespace\ClassName::methodName}}
success
{{else}}
fail
{{end}}
```

The method can also accept a single string argument:

```
{{if \App\Helpers::isFeatureEnabled("dark-mode")}}
<link rel="stylesheet" href="dark.css">
{{end}}
```

```php
namespace App\Helpers;

class Helpers
{
    public static function isFeatureEnabled(string $flag): bool
    {
        return in_array($flag, ['dark-mode', 'beta'], true);
    }
}
```

---

## In-line conditions

In-line conditions are written inside `{{ }}` and produce a string result directly.

### Syntax

```
{{condition ? "true output" : "false output"}}
{{condition ? "true output"}}
```

Placeholders in the output string use `__variable__` syntax (double underscores).

### EXISTS check

```php
<?php

use Brace\Parser;

$brace = new Parser();

echo $brace->parseInputString(
    '{{name EXISTS ? "Hello __name__" : "No name"}}',
    ['name' => 'Alice'],
    false
)->return();
// Output: Hello Alice
```

### Comparison

```php
echo $brace->parseInputString(
    '{{first_name !== "test" ? "__first_name__" : "is test"}}',
    ['first_name' => 'John'],
    false
)->return();
// Output: John
```

### Escaping quotes in the output

```php
echo $brace->parseInputString(
    '{{first_name !== "test" ? "Name is \"__first_name__\""}}',
    ['first_name' => 'John'],
    false
)->return();
// Output: Name is "John"
```

### AND / OR in in-line conditions

```php
// AND
echo $brace->parseInputString(
    '{{name EXISTS && age >= 21 ? "Welcome __name__, you are __age__"}}',
    ['name' => 'Simon', 'age' => 21],
    false
)->return();
// Output: Welcome Simon, you are 21

// OR
echo $brace->parseInputString(
    '{{name === "Dave" || name === "Simon" ? "Hello __name__" : "Stranger"}}',
    ['name' => 'Simon'],
    false
)->return();
// Output: Hello Simon
```

### Static method in an in-line condition

```php
echo $brace->parseInputString(
    '{{\App\Helpers::isFeatureEnabled("dark-mode") ? "dark" : "light"}}',
    [],
    false
)->return();
// Output: dark  (if the method returns true)
```

---

## Conditions reference

| Operator  | Description                                              |
|-----------|----------------------------------------------------------|
| `==`      | Equal (loose comparison)                                 |
| `===`     | Identical (strict comparison)                            |
| `!=`      | Not equal (loose comparison)                             |
| `!!`      | Not identical                                            |
| `!==`     | Not identical (strict, same as `!!`)                     |
| `>`       | Greater than                                             |
| `<`       | Less than                                                |
| `>=`      | Greater than or equal to                                 |
| `<=`      | Less than or equal to                                    |
| `EXISTS`  | Variable exists and is non-empty                         |
| `!EXISTS` | Variable does not exist or is empty                      |
