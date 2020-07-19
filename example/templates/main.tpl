
[@include header]

My name is {{firstname || fname || "no first name found"}} {{lastname}}

{{about->age EXISTS && about->age >= 18 ? "I am __about->age__ years old"}}

Profession: {{about->profession}}

