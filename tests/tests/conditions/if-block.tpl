{{if name EXISTS}}
Hello {{name->first}} {{name->last}}
{{else}}
Name does not exist
{{end}}