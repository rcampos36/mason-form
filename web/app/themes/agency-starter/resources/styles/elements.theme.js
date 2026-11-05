export default {
  settings: {
    spacing: {
      spacingSizes: [
        {
          name: "Section",
          slug: "section",
          size: "clamp(4.5rem, 3rem + 6vw, 8.5rem)",
        },
        {
          name: "Section small",
          slug: "section-sm",
          size: "clamp(3rem, 2rem + 3vw, 5rem)",
        },
      ],
    },
  },
  styles: {
    color: {
      background: "var(--wp--preset--color--background)",
      text: "var(--wp--preset--color--foreground)",
    },
    typography: {
      fontFamily: "var(--wp--preset--font-family--sans)",
      fontSize: "var(--wp--preset--font-size--body)",
      lineHeight: "1.65",
    },
    elements: {
      heading: {
        color: {
          text: "var(--wp--preset--color--foreground)",
        },
        typography: {
          fontFamily: "var(--wp--preset--font-family--sans)",
          fontWeight: "500",
        },
      },
      h1: {
        typography: {
          fontSize: "var(--wp--preset--font-size--h-1)",
          lineHeight: "0.96",
          letterSpacing: "-0.035em",
        },
      },
      h2: {
        typography: {
          fontSize: "var(--wp--preset--font-size--h-2)",
          lineHeight: "1.05",
          letterSpacing: "-0.03em",
        },
      },
      h3: {
        typography: {
          fontSize: "var(--wp--preset--font-size--h-3)",
          lineHeight: "1.15",
          letterSpacing: "-0.02em",
        },
      },
      h4: {
        typography: {
          fontSize: "var(--wp--preset--font-size--h-4)",
          lineHeight: "1.25",
        },
      },
      h5: {
        typography: {
          fontSize: "var(--wp--preset--font-size--h-5)",
          lineHeight: "1.35",
        },
      },
      h6: {
        typography: {
          fontSize: "var(--wp--preset--font-size--h-6)",
          lineHeight: "1.4",
          letterSpacing: "0.12em",
          textTransform: "uppercase",
        },
      },
      link: {
        color: {
          text: "var(--wp--preset--color--foreground)",
        },
        ":hover": {
          color: {
            text: "var(--wp--preset--color--accent)",
          },
        },
      },
      button: {
        border: {
          radius: "0px",
          width: "0",
        },
        color: {
          background: "var(--wp--preset--color--foreground)",
          text: "var(--wp--preset--color--background)",
        },
        typography: {
          fontSize: "var(--wp--preset--font-size--button)",
          fontWeight: "500",
          letterSpacing: "0.14em",
          textTransform: "uppercase",
        },
        ":hover": {
          color: {
            background: "var(--wp--preset--color--accent)",
            text: "var(--wp--preset--color--background)",
          },
        },
      },
      caption: {
        color: {
          text: "var(--wp--preset--color--muted)",
        },
        typography: {
          fontSize: "var(--wp--preset--font-size--caption)",
        },
      },
    },
  },
};
