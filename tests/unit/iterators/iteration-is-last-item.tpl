{{each names as name}}
<span{{_ITERATION === "is_last_item" ? " class=\"is_last\""}}>{{name}}</span>
{{end}}