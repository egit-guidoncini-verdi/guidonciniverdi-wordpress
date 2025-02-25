/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,php}"],
  theme: {
    extend: {
      typography: () => ({
        DEFAULT: {
          css: {
            maxWidth: "100%",
          },
        },
      }),
    },
  },
  plugins: [require("@tailwindcss/typography")],
};
