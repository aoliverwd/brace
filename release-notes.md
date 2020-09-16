# Release Notes

* Type: Hotfix
* Version: 1.0.1

## Changes

1. Spelling update for challenge
2. Added if is true challenge to condition blocks. No need to add EXISTS as this will automatically be added in at run time. See examples below:

```html
{{if first_name EXISTS}}
    <p>My first name is {{first_name}}</p>
{{else}}
    <p>Please enter your first name</p>
{{end}}
```

Can no be processed as:

```html
{{if first_name}}
    <p>My first name is {{first_name}}</p>
{{else}}
    <p>Please enter your first name</p>
{{end}}
```

This addition also applies to inline conditions.

```html
<p>{{first_name EXISTS ? "__first_name__" : "first name missing"}}</p>
```

Can no be processed as:

```html
<p>{{first_name ? "__first_name__" : "first name missing"}}</p>
```