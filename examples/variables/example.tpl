
<!-- Singular variable -->
Hello {{fullname}}

<!-- Optional message when no variable is found -->
Hello {{firstname || "No first name found"}}

<!-- Optional message when multiple variables are not found -->
Hello {{fname || firstname || "No first name found"}}

<!-- nested variables -->
Hello {{name->first}} {{name->last}}

