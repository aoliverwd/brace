# Brace
## Simple Reference

### Index

1. [Each statement blocks](#each_statement_blocks)
2. [Conditional statement blocks](#conditional_statement_blocks)
3. [Shorthand/inline conditional statements](#inline_conditional_statements)
    1. [Conditions](#conditions)
4. [Variables](#variables)
    1. [Nested variables](#nested_variables)
5. [Includes](#includes)

--

<h3 id="each_statement_blocks">1. Each statement blocks</h3>

```html
{{each products}}
    <p>{{title}}</p>
{{end}}
```

```html
{{each products as product}}
    <p>{{product->title}}</p>
{{end}}
```

```html
{{each names as name}}
    <p>{{name}}</p>
{{end}}
```

<h3 id="conditional_statement_blocks">2. Conditional statement blocks</h3>

```html
{{if first_name !== "test" || first_name !! null && first_name == "alex"}}
    May first name is {{first_name}}
{{elseif first_name === "test"}}
    your first name is test
{{else}}
    please enter your first name 
{{end}}
```

```html
{{if first_name EXISTS}}
    Hello {{first_name}}
{{end}}
```

<h3 id="inline_conditional_statements">3. Shorthand/inline conditional statements</h3>

```html
{{first_name !== "test" ? "__first_name__" : "is test"}}
```

```html
{{first_name EXISTS ? "__first_name__" : "is test"}}
```

```html
{{first_name EXISTS ? "my first name is __first_name__"}}
```

<h4 id="conditions">3.1 Conditions</h3>

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


__For consideration__

| Condition  | Description                              |
|------------|------------------------------------------|
| COUNT()    | Returns a count of an array              |


<h3 id="variables">4. Variables</h3>


```html
{{firstname}}
```
```html
{{firstname || "No first name found"}}
```

```html
{{firstname || fname || "No first name found"}}
```


<h4 id="nested_variables">4.1 Nested variables</h4>

```html
{{website->title}}
```

<h3 id="includes">5. Includes</h3>

```html
[@include footer]
```

```html
[@include header footer]
```

```html
[@include sections/footer]
```

```html
[@include {{variable}}]
```