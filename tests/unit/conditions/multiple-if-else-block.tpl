{{if name->first && name->last}}
Hello {{name->first}} {{name->last}}
{{elseif name->first}}
Hello {{name->first}}
{{elseif name->last}}
Hello Mr {{name->last}}
{{else}}
Name does not exist
{{end}}