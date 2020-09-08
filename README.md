![PHPUnit](https://github.com/aoliverwd/brace/workflows/PHPUnit/badge.svg?branch=master)

# Introduction

brace is a simple template language written in PHP. Brace uses a handlebar style syntax.

## Installation and usage

### Including brace, creating a new class instance and setting instance variables.

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

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

### Compiling to external file

```php
<?php
    /** Include brace */
    include __DIR__.'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and compile to external file */
    $template_string = $brace->compile('example', 'example.html', [
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

### Considerations for future development

#### Add first and last item reference to iterators

It would be beneficial to know if the current iteration is currently the first or last.

```html
<ul>
{{each products as product}}
    <li {{is_first_item ? "class='is_first'"}}>{{product->title}}</li>
{{end}}
</ul>
```

#### Add COUNT() condition to conditional statements

Could be beneficial to run conditions based on how many items are in an array of parsed data.

```html
{{if COUNT(products) > 0}}
    Do something
{{end}}
```

#### Add in-line iterator statements

```html
{{names as name "<li>__name__</li>"}}
```