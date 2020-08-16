{{if name->name EXISTS && name->last EXISTS}}
Hello {{name->first}} {{name->last}}
{{else}}
Name does not exist
{{end}}