
<!-- Multiple elseif statements -->
{{each names as name}}
    {{if _ITERATION === "is_first_item"}}
    <span class="first_item" data-rowid="{{_ROW_ID}}">{{name}}</span>
    {{elseif _ITERATION === "is_last_item"}}
    <span class="last_item" data-rowid="{{_ROW_ID}}">{{name}}</span>
    {{elseif _ITERATION == 2}}
    <span class="third_item" data-rowid="{{_ROW_ID}}">{{name}}</span>
    {{else}}
    <span data-rowid="{{_ROW_ID}}">{{name}}</span>
    {{end}}
{{end}}



<!-- inline last item -->
{{each names as name}}
    <span{{_ITERATION === "is_first_item" ? " class=\"is_first\""}} data-rowid="{{_ROW_ID}}">{{name}}</span>
{{end}}


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

