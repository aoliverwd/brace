{{if name->first && name->last EXISTS}}
Hello {{name->first}} {{name->last}}
{{elseif name->first}}
Hello {{name->first}}
{{else}}
Name does not exist
{{end}}