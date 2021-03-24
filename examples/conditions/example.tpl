
<!-- Inline conditions -->
{{fullname EXISTS ? "Hello \"__fullname__\""}}

{{fullname EXISTS ? "Hello __fullname__" : "Fullname not found"}}

{{othername EXISTS ? "Hello __othername__" : "Othername not found"}}

{{fullname EXISTS ? "Hello __fullname__"}}

<!-- Condition blocks -->
{{if name->first EXISTS}}
    Hello {{name->first}}
{{else}}
    First name does not exist
{{end}}

{{if name->first EXISTS && name->last EXISTS}}
    Hello {{name->first}} {{name->last}}
{{else}}
    First and last name do not exist
{{end}}