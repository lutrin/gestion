{
  /* Not supported in HTML5 */
  "acronym":  "abbr",
  "applet":   "object",
  "b":        "strong",
  "basefont": "span",
  "big":      "span",
  "center":   "span",
  "font":     "span",
  "frame":    null,
  "frameset": null,
  "i":        "em",
  "noframes": null,
  "s":        "span",
  "strike":   "span",
  "u":        "span",
  "xmp":      "pre",

  /* application */
  "ui.form": {
    "form", "*", [
      { "fn.if", { "test": "[closable]" }, {
          "button", { "class": "close", "data-trigger": "close" }, {
            "span", { "class": "hidden" }, "Fermer"
          }
        }
    }, "*" ]
  },

  "ui.field": [ {
    "fn.choose", false, [ {
      "fn.when", { "test": "[type=textarea]" }, {
        "div", false, [ {
          "apply.topfield"
        }, {
          "textarea", false, "Ceci est un text area"
        } ]
      }
    } ]
  } ],

  /* apply */
  "apply.topfield": [
    { "apply.classfield" },
    { "apply.label" }
  ]
}
