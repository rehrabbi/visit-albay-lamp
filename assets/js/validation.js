(function () {
  document.addEventListener("submit", function (event) {
    var form = event.target;
    if (!form.matches("[data-prevent-double-submit]")) return;

    var button = form.querySelector("button[type='submit']");
    if (button) {
      button.disabled = true;
      button.dataset.originalText = button.textContent;
      button.textContent = "Working...";
    }
  });
})();
