import js from "@eslint/js";
import globals from "globals";
import pluginSecurity from "eslint-plugin-security";
import pluginNoUnsanitized from "eslint-plugin-no-unsanitized";
import { defineConfig } from "eslint/config";

export default defineConfig([
  {
    // ignore vendor and 3rd-party code inside your repo
    ignores: [
      "public/assets/libs/**",
      "vendor/**",
      "node_modules/**"
    ]
  },
  {
    // apply ESLint to ALL JS files in your repo
    files: ["**/*.{js,mjs,cjs}"],

    languageOptions: {
      ecmaVersion: "latest",
      sourceType: "module",
      globals: globals.browser
    },

    plugins: {
      js,
      security: pluginSecurity,
      "no-unsanitized": pluginNoUnsanitized
    },

    rules: {
      ...js.configs.recommended.rules,

      // Security rules
      "security/detect-eval-with-expression": "error",

      // Prevent DOM XSS
      "no-unsanitized/method": "warn",
      "no-unsanitized/property": "warn"
    }
  }
]);
