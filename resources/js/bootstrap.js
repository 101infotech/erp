import axios from "axios";

// Configure axios as global window object
window.axios = axios;

// Automatically set X-Requested-With header
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Add CSRF token from meta tag
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
}
