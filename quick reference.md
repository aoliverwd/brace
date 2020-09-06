# Brace
## Simple Reference

### 1. Iterators

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

#### 1.1 In-line iterator statements

__For consideration__

```html
{{names as name "<li>__name__</li>"}}
```

### 2. Conditional statement blocks

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

### 3. Shorthand/In-line conditional statements

```html
{{first_name !== "test" ? "__first_name__" : "is test"}}
```

```html
{{first_name EXISTS ? "__first_name__" : "is test"}}
```

```html
{{first_name EXISTS ? "my first name is __first_name__"}}
```

#### 3.1 Conditions

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


### 4. Variables


```html
{{firstname}}
```
```html
{{firstname || "No first name found"}}
```

```html
{{firstname || fname || "No first name found"}}
```


#### 4.1 Nested variables

```html
{{website->title}}
```

### 5. Includes

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

### 6. Shortcodes

```html
[button title="Click me" url="https://example.com" target="_blank"]
```