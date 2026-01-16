import "./bootstrap";

import Alpine from "alpinejs";

// Persist Alpine to global scope for debugging
window.Alpine = Alpine;

// Initialize Alpine.js for server-side Blade templates
Alpine.start();
