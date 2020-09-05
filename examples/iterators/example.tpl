
{{each products}}
    {{title}}
    Cost: £{{price}}
    {{stock}} items(s) in stock

    Categories:

    {{each categories as category}}
    - {{category}} 
    {{end}}

{{end}}

