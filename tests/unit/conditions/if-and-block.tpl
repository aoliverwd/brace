{{if name->first && name->last EXISTS}}
Hello {{name->first}} {{name->last}}
{{else}}
Name does not exist
{{end}}