		(function($) {
          $.widget("custom.combobox", {
            _create: function() {
              this.wrapper = $("<span>")
                .addClass("custom-combobox")
                .insertAfter(this.element);

              this.element.hide();
              this._createAutocomplete();
              this._createShowAllButton();
              this.input.data("uiAutocomplete")._renderItem = function(ul, item) {
                var $el = $("<li>");
                if ($(item.option).is(":disabled")) {
                  $el.addClass("ui-state-disabled").text(item.label);
                } else {
                  $el.append("<a>" + item.label + "</a>");
                }

                return $el.appendTo(ul);
              };

            },

            _createAutocomplete: function() {
              var selected = this.element.children(":selected"),
                value = selected.val() ? selected.text() : "";

              this.input = $("<input>")
                .appendTo(this.wrapper)
                .val(value)
                .attr("title", "")
                .addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left")
                .autocomplete({
                  delay: 0,
                  minLength: 0,
                  source: $.proxy(this, "_source")
                })
                .tooltip({
                  tooltipClass: "ui-state-highlight"
                });

              this._on(this.input, {
                autocompleteselect: function(event, ui) {
                  ui.item.option.selected = true;
                  this._trigger("select", event, {
                    item: ui.item.option
                  });
                },
                autocompletefocus: function(event, ui) {
                  if ($(ui.item.option).is(":disabled")) {
                    event.preventDefault();
                    $(event.currentTarget).val('');
                  }
                },
                autocompletechange: "_removeIfInvalid"
              });
            },

            _createShowAllButton: function() {
              var input = this.input,
                wasOpen = false;

              $("<a>")
                .attr("tabIndex", -1)
                .attr("title", "Mostrar todos os itens")
                .tooltip()
				.append("<i class=\"fas fa-bars\"></i>")
                .appendTo(this.wrapper)
                .button({
                  icons: {
                    primary: "ui-icon-triangle-1-s"
                  },
                  text: true
                })
                .removeClass("ui-corner-all")
                .addClass("custom-combobox-toggle ui-corner-right")
                .mousedown(function() {
                  wasOpen = input.autocomplete("widget").is(":visible");
                })
                .click(function() {
                  input.focus();

                  // Close if already visible
                  if (wasOpen) {
                    return;
                  }

                  // Pass empty string as value to search for, displaying all results
                  input.autocomplete("search", "");
                });
            },

            _source: function(request, response) {
              var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
              response(this.element.children("option").map(function() {
                var text = $(this).text();
                if (this.value && (!request.term || matcher.test(text)))
                  return {
                    label: text,
                    value: text,
                    option: this
                  };
              }));
            },

            _removeIfInvalid: function(event, ui) {

              // Selected an item, Check only disabled option item
              if (ui.item) {
                if ($(ui.item.option).is(":disabled")) {
                  $(event.currentTarget).add(this.element).val('');
                }
                return;
              }

              // Search for a match (case-insensitive)
              var value = this.input.val(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
              this.element.children("option:not(:disabled)").each(function() {
                if ($(this).text().toLowerCase() === valueLowerCase) {
                  this.selected = valid = true;
                  return false;
                }
              });

              // Found a match, nothing to do
              if (valid) {
                return;
              }

              // Remove invalid value
              this.input
                .val("")
                .attr("title", value + " não corresponde a nenhum item")
                .tooltip("open");
              this.element.val("");
              this._delay(function() {
                this.input.tooltip("close").attr("title", "");
              }, 2500);
              this.input.autocomplete("instance").term = "";
            },

            _destroy: function() {
              this.wrapper.remove();
              this.element.show();
            }
          });
        })(jQuery);