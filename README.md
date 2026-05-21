![PHPUnit](https://github.com/aoliverwd/brace/actions/workflows/ci.yml/badge.svg) [![Latest Stable Version](https://poser.pugx.org/alexoliverwd/brace/v)](//packagist.org/packages/alexoliverwd/brace) [![License](https://poser.pugx.org/alexoliverwd/brace/license)](//packagist.org/packages/alexoliverwd/brace)

<img src="https://github.com/aoliverwd/brace/wiki/branding/brace.svg" alt="Brace Logo" width="100">

# Brace

Brace is a lightweight templating syntax for PHP that uses a familiar `{{ double-brace }}` syntax. It shares similarities with popular templating engines such as [Mustache](https://mustache.github.io/), [Twig](https://twig.symfony.com/), [Smarty](https://www.smarty.net/), and [Latte](https://latte.nette.org/).

Designed to be simple and easy to adopt, Brace focuses on a low learning curve while remaining flexible enough for most templating needs. It provides essential functionality out of the box without requiring additional dependencies.

## Key Features:

1. [Variables](https://github.com/aoliverwd/brace/wiki/variables)
2. [Filters](https://github.com/aoliverwd/brace/wiki/filters)
3. [Conditional Statements](https://github.com/aoliverwd/brace/wiki/conditionals)
4. [Iterators](https://github.com/aoliverwd/brace/wiki/Iterators)
5. [Loops](https://github.com/aoliverwd/brace/wiki/loops)
6. [Function Hooks](https://github.com/aoliverwd/brace/wiki/callables)
7. [Shortcodes](https://github.com/aoliverwd/brace/wiki/shortcodes)
8. [Includes](https://github.com/aoliverwd/brace/wiki/includes)

## Syntax

By default, Brace loads `.tpl` files from the current directory. The following example loads multiple templates, `document-header.tpl`, `template.tpl`, and `document-footer.tpl`, and outputs them as a single HTML file.

```php
<?php

use Brace\Parser;

$brace = new Parser();

// Register filters
$brace->registerFilter('int', fn($content) => (int) $content);
$brace->registerFilter('decimal', fn($number) => number_format($number, 2));
$brace->registerFilter('uppercase', fn($content) => strtoupper($content));
$brace->registerFilter('titlecase', fn($content) => ucwords($content));
$brace->registerFilter('striptags', fn($content) => strip_tags($content));

// Register shortcode
$brace->regShortcode('add_to_basket_button', fn($attrs) => sprintf(
    '<button id="%d">Add to basket</button>',
    $attrs['product_id'],
));

$brace->parse('template, document-footer', [
    'name' => 'Jane Doe',
    'products' => [
        0 => [
            'id' => 1154,
            'price' => 22.66,
            'title' => 'Product Number One',
            'description' => 'This is a product description',
        ],
        1 => [
            'id' => 1156,
            'price' => 16,
            'title' => 'Product Number Two',
            'description' => 'This is another product description',
        ],
    ],
]);
```

Template:

```html
<!-- Simple HTML template -->
[@include document-header]

<main>
    <p>Hello World. My name is {{ name|titlecase || "John Doe" }}</p>

    <h1>Product List:</h1>
    {{ if products }}
    <p>Showing {{ COUNT(products) }} item(s)</p>
    <ul>
        {{ each products as product }}
        <li>
            <span>{{ product->title|uppercase }}</span>
            <span> {{ product->currancy_symbol || "&pound;" }}{{ product->price|decimal }} </span>
            <p>{{ product->description|striptags }}</p>

            [add_to_basket_button product_id="{{ product->id|int }}"]
        </li>
        {{ end }}
    </ul>
    {{ else }}
    <p>No products to list</p>
    {{ end }}
</main>
```

Result:

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Brace Examples</title>
    </head>
    <body>
        <main>
            <p>Hello World. My name is Jane Doe</p>

            <h1>Product List:</h1>
            <p>Showing 2 item(s)</p>
            <ul>
                <li>
                    <span>PRODUCT NUMBER ONE</span>
                    <span> &pound;22.66 </span>
                    <p>This is a product description</p>

                    <button id="1154">Add to basket</button>
                </li>
                <li>
                    <span>PRODUCT NUMBER TWO</span>
                    <span> &pound;16.00 </span>
                    <p>This is another product description</p>

                    <button id="1156">Add to basket</button>
                </li>
            </ul>
        </main>
    </body>
</html>
```

## Next Steps

[Getting Started](https://github.com/aoliverwd/brace/wiki/getting-started)
