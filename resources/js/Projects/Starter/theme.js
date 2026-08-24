const colors = require("tailwindcss/colors");

// Tailwind theme for the Starter project. This object is merged into
// `theme.extend` by tailwind.config.js (keyed off VITE_PROJECT_PATH), so the
// classes below (e.g. `bg-primary`, `text-accent`, `bg-primary-700`) are
// available throughout the Starter designs. Swap these values to re-skin the
// whole project in one place.
module.exports = {
    colors: {
        primary: {
            DEFAULT: "#1e293b", // slate-800
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
        accent: {
            DEFAULT: "#6366f1", // indigo-500
            400: "#818cf8",
            500: "#6366f1",
            600: "#4f46e5",
        },
        main: "#6366f1",
        secondary: "#22d3ee",
        danger: colors.rose,
        success: colors.emerald,
        warning: colors.amber,
    },
};
