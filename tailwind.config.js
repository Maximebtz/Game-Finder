/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  theme: {
    extend: {
      fontFamily: {
        museo: ["MuseoModerno"],
      },
      colors: {
        "light-purple": "#FAECFF",
        "purple": "#D86AFF",
        "dark-purple": "#BD00FF",
      },
      backgroundImage: {
        "purple-gradient": "linear-gradient(-118deg, #BD00FF 0%, #D86AFF 80%)",
      },
      dropShadow: {
        btn: "0 0 15px rgba(189,0,255,0.50)",
        card: "0 0 15px rgba(98,25,109,0.20)",
      },
      boxShadow: {
        btn: " 0 0 15px rgba(189,0,255,0.50)",
        card: " 0 0 15px rgba(98,25,109,0.20)",
      },
    },
  },
  plugins: [],
}