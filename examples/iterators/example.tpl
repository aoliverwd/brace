<!-- Each products -->
{{each products}}
    {{title}}
    Cost: £{{price}}
    {{stock}} items(s) in stock

    Categories:

    <!-- Each categories as category -->
    {{each categories as category}}
    - {{category}} 
    {{end}}

{{end}}

<!-- In-line iterator -->
{{names as name "__name__"}}