/**
 * Dialog Management Utility
 * Provides a clean API for opening, closing, and managing dialogs across the application
 */

export const DialogManager = {
    /**
     * Open a dialog by name
     * @param {string} name - The dialog name
     */
    open(name) {
        window.dispatchEvent(new CustomEvent("open-dialog", { detail: name }));
    },

    /**
     * Close a dialog by name
     * @param {string} name - The dialog name
     */
    close(name) {
        window.dispatchEvent(new CustomEvent("close-dialog", { detail: name }));
    },

    /**
     * Toggle a dialog
     * @param {string} name - The dialog name
     */
    toggle(name) {
        // This would require Alpine.js state management
        window.dispatchEvent(
            new CustomEvent("toggle-dialog", { detail: name })
        );
    },

    /**
     * Close all dialogs
     */
    closeAll() {
        window.dispatchEvent(new CustomEvent("close-all-dialogs"));
    },
};

/**
 * Confirmation Dialog Utility
 * Provides a clean API for showing confirmation dialogs
 */
export const ConfirmDialog = {
    /**
     * Show a confirmation dialog
     * @param {string} name - The dialog name
     */
    show(name) {
        window.dispatchEvent(new CustomEvent("open-confirm", { detail: name }));
    },

    /**
     * Close a confirmation dialog
     * @param {string} name - The dialog name
     */
    close(name) {
        window.dispatchEvent(
            new CustomEvent("close-confirm", { detail: name })
        );
    },
};

/**
 * Simple Alert/Toast notification utility
 */
export const Notify = {
    /**
     * Show a success notification
     * @param {string} message - The message to display
     */
    success(message) {
        this.show(message, "success");
    },

    /**
     * Show an error notification
     * @param {string} message - The message to display
     */
    error(message) {
        this.show(message, "error");
    },

    /**
     * Show a warning notification
     * @param {string} message - The message to display
     */
    warning(message) {
        this.show(message, "warning");
    },

    /**
     * Show an info notification
     * @param {string} message - The message to display
     */
    info(message) {
        this.show(message, "info");
    },

    /**
     * Generic show method
     * @param {string} message - The message to display
     * @param {string} type - The notification type (success, error, warning, info)
     */
    show(message, type = "info") {
        window.dispatchEvent(
            new CustomEvent("show-notification", {
                detail: { message, type },
            })
        );
    },
};

export default { DialogManager, ConfirmDialog, Notify };
