export function copyToClipboard(content, onSuccess = function () {}, onError = function () {}) {
  if (content === "") return;

  if (typeof content === "function") {
    content = content();
  }

  navigator.clipboard.writeText(content).then(
    function () {
      onSuccess();
    },
    function (err) {
      onError(err);
    }
  );
}
