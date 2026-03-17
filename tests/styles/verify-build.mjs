import assert from "node:assert/strict";
import { existsSync, readFileSync } from "node:fs";

const requiredFiles = [
  "dist/css/district-styles.css",
  "dist/css/district-styles.css.map",
  "dist/css/district-styles.min.css",
  "dist/assets/topography.svg",
];

for (const file of requiredFiles) {
  assert.ok(existsSync(file), `Expected build artifact to exist: ${file}`);
}

const expandedCss = readFileSync("dist/css/district-styles.css", "utf8");
const minifiedCss = readFileSync("dist/css/district-styles.min.css", "utf8");

assert.match(
  expandedCss,
  /background-image:\s*url\("\.\.\/assets\/topography\.svg"\);/,
  "Expanded CSS should reference the built topography asset",
);

assert.match(
  minifiedCss,
  /\.\.\/assets\/topography\.svg/,
  "Minified CSS should reference the built topography asset",
);

console.log("Style tests passed.");
