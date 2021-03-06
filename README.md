![PHPUnit](https://github.com/aoliverwd/brace/workflows/PHPUnit/badge.svg?branch=master) [![Latest Stable Version](https://poser.pugx.org/alexoliverwd/brace/v)](//packagist.org/packages/alexoliverwd/brace) [![License](https://poser.pugx.org/alexoliverwd/brace/license)](//packagist.org/packages/alexoliverwd/brace)

# Introduction

brace is a simple template language written in PHP. Brace uses a handlebar style syntax.

## Installation

### Via composer

```composer require alexoliverwd/brace```

### Or Including the brace class

```php
    /** Include brace */
    include __DIR__.'/src/brace.php';
```

## Usage

```php
<?php
    /** New brace instance */
    $brace = new brace\parser;

    /** Set instance variables (Optional) */
    $brace->remove_comment_blocks = false;
    $brace->template_path = __DIR__.'/';
    $brace->template_ext = 'tpl';

    /** Process template and echo out */
    $brace->parse('example',[
        'name' => [
            'first' => 'John',
            'last' => 'Doe'
        ]
    ]);
?>
```

### Returning processes templates as a string

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and return string */
    $template_string = $brace->parse('example',[
        'name' => [
            'first' => 'John',
            'last' => 'Doe'
        ]
    ], false)->return();
?>
```

### Compiling to an external file

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and compile to external file */
    $brace->compile('example', 'example.html', [
        'name' => [
            'first' => 'John',
            'last' => 'Doe'
        ]
    ]);
?>
```

### Instance variables

| Variable                     | Description                                    | Default value                       |
|------------------------------|------------------------------------------------|-------------------------------------|
| ```remove_comment_blocks```  | Keep or remove comment blocks from templates   | [Boolean] ```true```                |
| ```template_path```          | Set directory to load template files from      | [String] Current working directory  |
| ```template_ext```           | Template file extension                        | [String] ```tpl```                  |

## Template Reference

### Variables

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and echo out */
    $brace->parse('example',[
        'firstname' => 'John Doe'
    ]);
?>
```

```html
<p>{{firstname}}</p>
```

#### In-line ```or``` operator

```html
<p>{{firstname || "No first name found"}}</p>
```

#### Multiple In-line ```or``` operators

```html
<p>{{firstname || fname || "No first name found"}}</p>
```

### Iterators

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and echo out */
    $brace->parse('example',[
        'products' => [
            0 => [
                'title' => 'Product 1',
                'price' => 22.99,
                'stock' => 15,
                'categories' => ['Textile','Cloths']
            ],
            1 => [
                'title' => 'Product 2',
                'price' => 10.00,
                'stock' => 62,
                'categories' => ['Electronics','PC','Hardware']
            ],
            2 => [
                'title' => 'Product 3',
                'price' => 89.98,
                'stock' => 120,
                'categories' => ['PC Game']
            ]
        ]
    ]);
>
```

```html
<ul>
{{each products}}
    <li>{{title}}</li>
{{end}}
</ul>
```

```html
<ul>
{{each products as product}}
    <li>
        {{product->title}}
        <ul>
        {{each product->categories as category}}
            <li>{{category}}</li>
        {{end}}
        </ul>
    </li>
{{end}}
</ul>
```

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and echo out */
    $brace->parse('example',[
        'names' => ['John','Steve','Bert']
    ]);
?>
```

```html
{{each names as name}}
    <p>{{name}}</p>
{{end}}
```

#### In-line Iterators

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and echo out */
    $brace->parse('example',[
        'names' => ['John','Steve','Bert']
    ]);
?>
```

```html
<ul>
    {{names as name "<li>__name__</li>"}}
</ul>
```

```html
<ul>
    <li>John</li>
    <li>Steve</li>
    <li>Bert</li>
</ul>
```


### Conditional Statements

#### Condition Blocks

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and echo out */
    $brace->parse('example',[
        'first_name' => 'John',
        'last_name' => 'Doe'
    ]);
?>
```

```html
{{if first_name EXISTS}}
    <p>Hello {{first_name}}</p>
{{end}}
```

```html
{{if first_name EXISTS && first_name == "John"}}
    <p>My first name is {{first_name}}</p>
{{else}}
    <p>Please enter your first name</p>
{{end}}
```

#### In-line conditions


```html
<p>{{first_name !== "test" ? "__first_name__" : "is test"}}</p>
```

```html
<p>{{first_name EXISTS ? "__first_name__" : "is test"}}</p>
```

```html
<p>{{first_name EXISTS ? "my first name is __first_name__"}}</p>
```

Ability to add double quotes.

```html
<p>{{first_name !== "test" ? "Name is \"__first_name__\"" : "is test"}}</p>
```

```txt
Name is "John"
```

#### Conditions

| Condition  | Description                              |
|------------|------------------------------------------|
| ===        | Is equal to (Strict equality comparison) |
| >=         | More than or equal to                    |
| <=         | Less than or equal to                    |
| >          | More than                                |
| <          | Less than                                |
| !!         | Is not                                   |
| !==        | Is not equal (Same as !! operator)       |
| EXISTS     | Exists                                   |
| !EXISTS    | Does not exist                           |



### Including Templates

```html
[@include sections/footer]
```

```html
[@include header footer]
```

```html
[@include {{section}}]
```

### Shortcodes


#### PHP Implementation Example

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace parser */
    $brace = new brace\parser;

    /** Return HTML link */
    $button_function = function ($attributes){
        return '<a href="'.$attributes['url'].'" alt="'.$attributes['alt'].'" target="'.$attributes['target'].'" rel="noreferrer noopener">'.$attributes['title'].'</a>';
    };

    /** Register shortcode */
    $brace->reg_shortcode('button', 'button_function');

    /** Process content template */
    $brace->parse('content', []);
?>
```


#### Content Template

```html
<!-- Button shortcode -->
[button title="Hello world" url="https://hello.world" alt="Hello world button" target="_blank"]
```

### Comment Blocks

#### In-line Comment Block

```html
<!-- Inline comment block -->
```

#### Multiple Line Comment Block

```html
<!--
    Comment block over multiple lines
-->
```

### Clearing cached process string

The ```clear``` method is useful when needing to processes multiple templates with differing data using the same brace instance.

By default brace does not clear a processed string at the end of executing a template/string parse.

```php

  // Init brace
  $brace = new brace\parser;
  $brace->template_path = __DIR__.'/';

  // Process first template
  $brace->parse('example',[
      'name' => [
          'first' => 'John',
          'last' => 'Doe'
      ]
  ]);

  // Process second template using the same brace instance
  $brace->clear()->parse('example_two',[
      'name' => [
          'first' => 'Dave',
          'last' => 'Smith'
      ]
  ]);
```