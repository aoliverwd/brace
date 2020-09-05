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

### <a name="each_statement_blocks">1. Each statement blocks</>

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

### <a name="conditional_statement_blocks">2. Conditional statement blocks</a>

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

### <a name="inline_conditional_statements">3. Shorthand/inline conditional statements</a>

```html
{{first_name !== "test" ? "__first_name__" : "is test"}}
```

```html
{{first_name EXISTS ? "__first_name__" : "is test"}}
```

```html
{{first_name EXISTS ? "my first name is __first_name__"}}
```

#### <a name="conditions">3.1 Conditions</a>

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


### <a name="variables">4. Variables</a>


```html
{{firstname}}
```
```html
{{firstname || "No first name found"}}
```

```html
{{firstname || fname || "No first name found"}}
```


#### <a name="nested_variables">4.1 Nested variables</a>

```html
{{website->title}}
```

### <a name="includes">5. Includes</a>

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