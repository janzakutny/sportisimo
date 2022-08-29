import LiveFormValidation from "live-form-validation";
import naja from "naja";

import "./scss/app.scss";
require("./js/forms");
require("materialize-css/dist/js/materialize");

window.LiveForm = LiveFormValidation.LiveForm;
window.Nette = LiveFormValidation.Nette;

const najaExtension = {
  initialize(naja) {
    naja.uiHandler.addEventListener("interaction", ({ detail }) => {
      detail.options.loadingIndicator = document.querySelector("#ajax-loader");
    });

    naja.addEventListener("before", (event) => {
      setConfirmDialog(event);
    });

    naja.addEventListener("start", ({ detail }) => {
      let disabled = detail.options.nette?.el[0]?.dataset?.ajaxLoaderDisabled;
      if (typeof disabled !== "undefined") {
        return;
      }

      if (detail.options.loadingIndicator) {
        let simple = detail.options.nette?.el[0]?.dataset?.ajaxLoaderSimple;
        if (typeof simple !== "undefined") {
          detail.options.loadingIndicator.classList.add(
            "ajax-loader--active-simple"
          );
        } else {
          detail.options.loadingIndicator.classList.add("ajax-loader--active");
        }
      }
    });

    naja.addEventListener("complete", ({ detail }) => {
      if (detail.options.loadingIndicator) {
        detail.options.loadingIndicator.classList.remove("ajax-loader--active");
        detail.options.loadingIndicator.classList.remove(
          "ajax-loader--active-simple"
        );
      }
    });
  },
};

naja.registerExtension(najaExtension);

document.addEventListener("DOMContentLoaded", () => {
  naja.initialize();
  flashMessage(document);
  initSelect(document);
});

naja.snippetHandler.addEventListener("afterUpdate", (event) => {
  let loadedSnippet = event.detail.snippet;

  if (loadedSnippet.id === "snippet--modalWindow") {
    if (loadedSnippet.querySelector(".modal")) {
      let placeModal = M.Modal.init(loadedSnippet.querySelector(".modal"));
      placeModal.open();
    }
  }

  flashMessage(loadedSnippet);
  initSelect(loadedSnippet);
});

function setConfirmDialog(event) {
	if (event.detail.options.nette.el[0]?.hasAttribute("data-confirm")) {
    event.preventDefault();

		let modalElement = document.querySelector("#confirmModal");
		let placeModal = M.Modal.init(modalElement);
    placeModal.open(modalElement);

		modalElement.querySelector(".modal-body p").innerText = event.detail.options.nette.el[0].getAttribute("data-confirm");
		modalElement.querySelector(".modal-footer a.ajax").setAttribute("href", event.detail.request.url);
	}
}

function flashMessage(document)
{
    var flashMessages = document.getElementsByClassName('flashMessage');
    for (var i = 0; i < flashMessages.length; i++) {
        var message = flashMessages[i].value;
        M.toast({ 
            html:message, 
            displayLength: 10000, 
            classes: 'teal darken-3 white-text card-panel flash-container'
        });
    }
}

function initSelect(document)
{
  var elems = document.querySelectorAll('select');
  M.FormSelect.init(elems);
}

LiveForm.setOptions({
  messageErrorPrefix: "",
  showMessageClassOnParent: false,
  messageErrorClass: "invalid-feedback",
  messageTag: "div",
  controlErrorClass: "is-invalid",
  wait: 500,
});

import "ublaboo-datagrid";