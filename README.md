# Introduction

Brace is a simple document templating language written in PHP. Brace uses a handlebar notation syntax, this in-turn enables documents to still keep easily manageable.

Brace has been designed to be used in web pages that render HTML, however, Brace is not restricted to just use in HTML, it can be used to output to any type of text based file I.E TXT,CSV,JSON etc.

## Installation/Usage


## Reference

### Variables

```txt
{{firstname}}
```

#### In-line 'OR' operator

```txt
{{firstname || "No first name found"}}
```

#### Multiple In-line 'OR' operators

```txt
{{firstname || fname || "No first name found"}}
```



### Iterators


#### Iterator Blocks


```txt
{{each products}}
    <p>{{title}}</p>
{{end}}
```

```txt
{{each products as product}}
    <p>{{product->title}}</p>
{{end}}
```

```txt
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


```txt
{{if first_name EXISTS}}
    Hello {{first_name}}
{{end}}
```

```txt
{{if first_name !== "test" || first_name !! null && first_name == "alex"}}
    May first name is {{first_name}}
{{else}}
    please enter your first name 
{{end}}
```


#### In-line Statements


```txt
{{first_name !== "test" ? "__first_name__" : "is test"}}
```

```txt
{{first_name EXISTS ? "__first_name__" : "is test"}}
```

```txt
{{first_name EXISTS ? "my first name is __first_name__"}}
```

### Includeing Templates

```txt
[@include sections/footer]
```

```txt
[@include header footer]
```