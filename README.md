# Introduction

Brace is a simple template language written in PHP. Brace uses a handlebar notation syntax, this in-turn enables documents to still be easily manageable.

Brace has been designed to be used in web applications that render HTML, however, Brace is not restricted to just use in HTML, it can be used to output to any type of text based file I.E TXT,CSV,JSON etc.

## Installation/Usage


## Reference

### Variables

```html
<p>{{firstname}}</p>
```

#### In-line 'OR' operator

```html
<p>{{firstname || "No first name found"}}</p>
```

#### Multiple In-line 'OR' operators

```html
<p>{{firstname || fname || "No first name found"}}</p>
```

### Iterators

```html
{{each products}}
    <p>{{title}}</p>
{{end}}
```

```html
<ul>
{{each products as product}}
    <li>{{product->title}}</li>
{{end}}
</ul>
```

```html
{{each names as name}}
    <p>{{name}}</p>
{{end}}
```

#### Iterator Blocks


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
    <li>{{product->title}}</li>
{{end}}
</ul>
```

```html
{{each names as name}}
    <p>{{name}}</p>
{{end}}
```


### Conditional Statements

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


#### Condition Blocks


```html
{{if first_name EXISTS}}
    <p>Hello {{first_name}}</p>
{{end}}
```

```html
{{if first_name !== "test" || first_name !! null && first_name == "alex"}}
    <p>May first name is {{first_name}}</p>
{{else}}
    <p>please enter your first name</p>
{{end}}
```


#### In-line Statements


```html
<p>{{first_name !== "test" ? "__first_name__" : "is test"}}</p>
```

```html
<p>{{first_name EXISTS ? "__first_name__" : "is test"}}</p>
```

```html
<p>{{first_name EXISTS ? "my first name is __first_name__"}}</p>
```

### Includeing Templates

```txt
[@include sections/footer]
```

```txt
[@include header footer]
```

```txt
[@include {{section}}]
```

### Shortcodes


#### PHP Implementation Example

```php
/** New brace parser */
$brace = new brace\parser;

/**
 * [$button_function Return HTML button string]
 * @var     [array]  $attributes  [Array or attributes]
 * @return  [string]              [Return HTML button string]
 */
$button_function = function ($attributes){
    return '<a href="'.$attributes['url'].'" alt="'.$attributes['alt'].'" target="'.$attributes['target'].'" rel="noreferrer noopener">'.$attributes['title'].'</a>';
};

/** Register shortcode */
$brace->reg_shortcode('button', 'button_function');

/** Process content template */
$brace->parse('content', []);
```


#### Content Template

```txt
<!-- Button shortcode -->
[button title="Hello world" url="https://hello.world" alt="Hello world button" target="_blank"]
```

### Comment Blocks

#### In-line Code Block

```html
<!-- Inline comment block -->
```

#### Multi-line Code Block

```html
<!-- 
    Comment block over multiple lines
-->
```

### Considerations for future development

#### Add first and last item reference to iterators

It would be beneficial to know if the current iteration is currently the first or last.

```txt
<ul>
{{each products as product}}
    <li {{is_first_item ? "class='is_first'"}}>{{product->title}}</li>
{{end}}
</ul>
```

#### Add COUNT() condition to IF operators

Could be beneficial to run conditions based on how many items are in an array of parsed data.

```txt
{{if COUNT(products) > 0}}
    Do something
{{end}}
```