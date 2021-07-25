{{each names as name}}
<span{{_ITERATION === "is_first_item" ? " class=\"is_first\""}}>{{name}}</span>
{{end}}