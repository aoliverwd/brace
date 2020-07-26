[@include header]

My name is {{firstname || fname || "no first name found"}} {{lastname}}

{{about->age EXISTS && about->age >= 18 ? "I am __about->age__ years old"}}

Profession: {{about->profession}}

{{if jobs EXISTS}}
    has jobs

    {{each jobs as job}}
        {{job}}
    {{end}}
{{else}}
    There are no jobs

    damn it
{{end}}

foo

{{each jobs as job}}
    {{job}}
{{end}}

[@include footer]