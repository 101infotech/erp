import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
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
                // Primary Colors - Modern Indigo (matching reference design)
                primary: {
                    50: "#EEF2FF",
                    100: "#E0E7FF",
                    200: "#C7D2FE",
                    300: "#A5B4FC",
                    400: "#818CF8",
                    500: "#6366F1",
                    600: "#4F46E5",
                    700: "#4338CA",
                    800: "#3730A3",
                    900: "#312E81",
                },
                // Secondary Colors - Purple
                secondary: {
                    50: "#FAF5FF",
                    100: "#F3E8FF",
                    200: "#E9D5FF",
                    300: "#D8B4FE",
                    400: "#C084FC",
                    500: "#A78BFA",
                    600: "#9333EA",
                    700: "#7C3AED",
                    800: "#6D28D9",
                    900: "#5B21B6",
                },
                // Success Color - Green
                success: {
                    50: "#F0FDF4",
                    100: "#DCFCE7",
                    200: "#BBF7D0",
                    300: "#86EFAC",
                    400: "#4ADE80",
                    500: "#10B981",
                    600: "#059669",
                    700: "#047857",
                    800: "#065F46",
                    900: "#064E3B",
                },
                // Warning Color - Amber
                warning: {
                    50: "#FFFBEB",
                    100: "#FEF3C7",
                    200: "#FDE68A",
                    300: "#FCD34D",
                    400: "#FBBF24",
                    500: "#F59E0B",
                    600: "#D97706",
                    700: "#B45309",
                    800: "#92400E",
                    900: "#78350F",
                },
                // Danger Color - Red
                danger: {
                    50: "#FEF2F2",
                    100: "#FEE2E2",
                    200: "#FECACA",
                    300: "#FCA5A5",
                    400: "#F87171",
                    500: "#EF4444",
                    600: "#DC2626",
                    700: "#B91C1C",
                    800: "#991B1B",
                    900: "#7F1D1D",
                },
                // Info Color - Blue
                info: {
                    50: "#EFF6FF",
                    100: "#DBEAFE",
                    200: "#BFDBFE",
                    300: "#93C5FD",
                    400: "#60A5FA",
                    500: "#3B82F6",
                    600: "#2563EB",
                    700: "#1D4ED8",
                    800: "#1E40AF",
                    900: "#1E3A8A",
                },
                // Neutral - Slate palette for modern look
                neutral: {
                    50: "#F8FAFC",
                    100: "#F1F5F9",
                    200: "#E2E8F0",
                    300: "#CBD5E1",
                    400: "#94A3B8",
                    500: "#64748B",
                    600: "#475569",
                    700: "#334155",
                    800: "#1E293B",
                    900: "#0F172A",
                },
            },
            boxShadow: {
                xs: "0 1px 2px 0 rgba(0, 0, 0, 0.03)",
                sm: "0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px 0 rgba(0, 0, 0, 0.03)",
                md: "0 4px 12px 0 rgba(0, 0, 0, 0.08), 0 2px 6px 0 rgba(0, 0, 0, 0.04)",
                lg: "0 10px 20px -3px rgba(0, 0, 0, 0.10), 0 4px 8px -2px rgba(0, 0, 0, 0.05)",
                xl: "0 20px 30px -5px rgba(0, 0, 0, 0.12), 0 10px 15px -5px rgba(0, 0, 0, 0.06)",
                elevated: "0 25px 50px -12px rgba(0, 0, 0, 0.15)",
            },
            borderRadius: {
                sm: "6px",
                md: "8px",
                lg: "10px",
                xl: "12px",
                "2xl": "16px",
                "3xl": "20px",
            },
        },
    },

    plugins: [forms],
};
