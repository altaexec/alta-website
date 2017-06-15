(TeX-add-style-hook "altw-template"
 (lambda ()
    (LaTeX-add-bibliographies
     "sample")
    (LaTeX-add-labels
     "table1")
    (TeX-run-style-hooks
     "colacl"
     "latex2e"
     "art11"
     "article"
     "11pt")))

