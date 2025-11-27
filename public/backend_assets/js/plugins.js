// (document.querySelectorAll("[toast-list]")||document.querySelectorAll("[data-choices]")||document.querySelectorAll("[data-provider]"))&&(document.writeln("<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/toastify-js'><\/script>"),document.writeln("<script type='text/javascript' src='assets/libs/choices.js/public/assets/scripts/choices.min.js'><\/script>"),document.writeln("<script type='text/javascript' src='assets/libs/flatpickr/flatpickr.min.js'><\/script>"));

function loadScript(src, async = true) {
    if (document.querySelector(`script[src="${src}"]`)) return;

    const script = document.createElement("script");
    script.src = src;
    script.async = async;

    script.onload = () => console.log(`Loaded: ${src}`);
    script.onerror = () => console.error(`Failed to load: ${src}`);

    document.head.appendChild(script);
}

if (document.querySelector("[toast-list]")) {
    loadScript("https://cdn.jsdelivr.net/npm/toastify-js");
}
if (document.querySelector("[data-choices]")) {
    loadScript("assets/libs/choices.js/public/assets/scripts/choices.min.js");
}
if (document.querySelector("[data-provider]")) {
    loadScript("assets/libs/flatpickr/flatpickr.min.js");
}

