import { defineConfig } from "@playwright/test";

const launchOptions = process.env.PLAYWRIGHT_CHROME_EXECUTABLE_PATH
  ? {
      executablePath: process.env.PLAYWRIGHT_CHROME_EXECUTABLE_PATH,
    }
  : {};

export default defineConfig({
  testDir: "./tests/styles/playwright",
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  reporter: "list",
  use: {
    headless: true,
    browserName: "chromium",
    launchOptions,
    viewport: {
      width: 1440,
      height: 1100,
    },
  },
});
