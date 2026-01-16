import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [
                    "Inter",
                    "Plus Jakarta Sans",
                    ...defaultTheme.fontFamily.sans,
                ],
            },
            colors: {
                // Primary Colors - Lime (Brand color for CTAs and success)
                primary: {
                    DEFAULT: "#84cc16", // lime-500
                    50: "#f7fee7",
                    100: "#ecfccb",
                    200: "#d9f99d",
                    300: "#bef264",
                    400: "#a3e635",
                    500: "#84cc16", // Main brand color
                    600: "#65a30d",
                    700: "#4d7c0f",
                    800: "#3f6212",
                    900: "#365314",
                },
                // Secondary Colors - Blue (Links, info, secondary actions)
                secondary: {
                    DEFAULT: "#3b82f6", // blue-500
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                },
                // Accent Color - Amber (Warnings, highlights)
                accent: {
                    DEFAULT: "#f59e0b", // amber-500
                    50: "#fffbeb",
                    100: "#fef3c7",
                    200: "#fde68a",
                    300: "#fcd34d",
                    400: "#fbbf24",
                    500: "#f59e0b",
                    600: "#d97706",
                    700: "#b45309",
                    800: "#92400e",
                    900: "#78350f",
                },
                // Success Color - Green
                success: {
                    DEFAULT: "#10b981", // green-500
                    50: "#f0fdf4",
                    100: "#dcfce7",
                    200: "#bbf7d0",
                    300: "#86efac",
                    400: "#4ade80",
                    500: "#10b981",
                    600: "#059669",
                    700: "#047857",
                    800: "#065f46",
                    900: "#064e3b",
                },
                // Warning Color - Amber
                warning: {
                    DEFAULT: "#f59e0b", // amber-500
                    50: "#fffbeb",
                    100: "#fef3c7",
                    200: "#fde68a",
                    300: "#fcd34d",
                    400: "#fbbf24",
                    500: "#f59e0b",
                    600: "#d97706",
                    700: "#b45309",
                    800: "#92400e",
                    900: "#78350f",
                },
                // Danger Color - Red
                danger: {
                    DEFAULT: "#ef4444", // red-500
                    50: "#fef2f2",
                    100: "#fee2e2",
                    200: "#fecaca",
                    300: "#fca5a5",
                    400: "#f87171",
                    500: "#ef4444",
                    600: "#dc2626",
                    700: "#b91c1c",
                    800: "#991b1b",
                    900: "#7f1d1d",
                },
                // Info Color - Blue
                info: {
                    DEFAULT: "#3b82f6", // blue-500
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                },
                // Neutral - Slate palette for dark theme
                neutral: {
                    50: "#f8fafc",
                    100: "#f1f5f9",
                    200: "#e2e8f0",
                    300: "#cbd5e1",
                    400: "#94a3b8",
                    500: "#64748b",
                    600: "#475569",
                    700: "#334155",
                    800: "#1e293b",
                    900: "#0f172a",
                    950: "#020617",
                },
            },
            boxShadow: {
                xs: "0 1px 2px 0 rgba(0, 0, 0, 0.05)",
                sm: "0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)",
                md: "0 4px 6px 0 rgba(0, 0, 0, 0.1), 0 2px 4px 0 rgba(0, 0, 0, 0.06)",
                lg: "0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)",
                xl: "0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04)",
                "2xl": "0 25px 50px -12px rgba(0, 0, 0, 0.25)",
                card: "0 4px 6px 0 rgba(0, 0, 0, 0.1)",
                modal: "0 20px 25px -5px rgba(0, 0, 0, 0.15)",
                dropdown: "0 10px 15px -3px rgba(0, 0, 0, 0.1)",
            },
            borderRadius: {
                sm: "0.375rem", // 6px
                md: "0.5rem", // 8px
                lg: "0.75rem", // 12px
                xl: "1rem", // 16px
                "2xl": "1.5rem", // 24px
                "3xl": "2rem", // 32px
            },
            spacing: {
                card: "1.5rem", // 24px - Card padding
                section: "2rem", // 32px - Section gaps
                element: "1rem", // 16px - Element gaps
            },
            zIndex: {
                base: "0",
                dropdown: "10",
                sticky: "20",
                fixed: "30",
                "modal-backdrop": "40",
                modal: "50",
                popover: "60",
                tooltip: "70",
                notification: "80",
                max: "9999",
            },
        },
    },

    plugins: [forms],
};
