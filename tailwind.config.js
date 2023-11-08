/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./app/View/Components/TicketField.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
      extend: {
          width: {
              '2.5': '10rem',
          },
          scale: {
              '101': '1.01',
          }
      }
    },
  plugins: [],
}

