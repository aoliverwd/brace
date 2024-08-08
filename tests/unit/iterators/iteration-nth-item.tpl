{{each names as name}}
<span{{_ITERATION == 2 ? " class=\"is_second_item\""}}>{{name}}</span>
{{end}}