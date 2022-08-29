naja.snippetHandler.addEventListener('afterUpdate', (event) => {
    let loadedSnippet = event.detail.snippet;
  
    let form = loadedSnippet.closest("form");
    if (form) {
      Nette.toggleForm(form);
  
      for (var i = 0; i < form.elements.length; i++) {
        LiveForm.setupHandlers(form.elements[i]);
      }
    }
});
