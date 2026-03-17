import { test, expect } from "@playwright/test";
import path from "node:path";
import { pathToFileURL } from "node:url";

const fixtureUrl = pathToFileURL(
  path.resolve("tests/styles/fixtures/custom-components.html"),
).href;

test.beforeEach(async ({ page }) => {
  await page.goto(fixtureUrl);
});

test("page shell applies the custom branded surface treatment", async ({ page }) => {
  const shell = page.getByTestId("page-shell");
  const surface = page.getByTestId("page-surface");

  await expect(shell).toHaveCSS("min-height", "1100px");
  await expect(shell).toHaveCSS("padding-top", "24px");
  await expect(shell).toHaveCSS("background-image", /linear-gradient/);
  await expect(surface).toHaveCSS("border-radius", "24px");
  await expect(surface).toHaveCSS("backdrop-filter", /blur\(10px\)/);
});

test("page shell modifiers expose the expected custom variables", async ({ page }) => {
  const shell = page.getByTestId("page-shell");

  await expect(shell).toHaveCSS("--lbd-page-shell-topography-size", "600px");
  await expect(shell).toHaveCSS("--lbd-page-shell-topography-opacity", "0.65");
  await expect(shell).toHaveCSS("--lbd-page-shell-topography-rotation", "7deg");
});

test("navigation and branding keep the custom district palette", async ({ page }) => {
  await expect(page.locator("#site-top-bar-brand")).toHaveCSS("color", "rgb(116, 19, 219)");
  await expect(page.locator("#site-navbar")).toHaveCSS("color", "rgb(116, 19, 220)");
  await expect(page.locator("#site-navbar-brand")).toHaveCSS("color", "rgb(116, 19, 220)");
  await expect(page.locator(".navigation-clean")).toHaveCSS("padding-top", "16px");
  await expect(page.locator(".navigation-clean .nav-link.active")).toHaveCSS("pointer-events", "none");
});

test("footer layout uses the project-specific flex treatment", async ({ page }) => {
  const footerCol = page.getByTestId("footer-col");
  const footer = footerCol.locator("footer");

  await expect(footerCol).toHaveCSS("display", "flex");
  await expect(footerCol).toHaveCSS("align-items", "center");
  await expect(footer).toHaveCSS("display", "flex");
  await expect(footer).toHaveCSS("justify-content", "center");
  await expect(footer).toHaveCSS("margin-top", "-60px");
  await expect(footer).toHaveCSS("background-color", "rgb(116, 19, 220)");
});
