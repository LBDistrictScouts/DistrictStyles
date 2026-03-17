# District Styles Monorepo

This repository now publishes two things:

- `@lbdistrictscouts/district-styles` for npm consumers
- `lbdistrictscouts/district-ui` for CakePHP/Composer consumers

The npm package ships Bootstrap-based SCSS styles for LB District Scout
projects. The Composer package provides the CakePHP plugin that installs and
publishes those assets into a Cake application.

## CakePHP / Composer

The repository root is also the Composer package for the `DistrictUI` CakePHP
plugin.

Consumer applications can install it with Composer from this repository, then
run:

```bash
bin/cake district_ui install --overwrite
```

The plugin build runs from this repository's local frontend source. It does not
download the private npm package during install, but it does require `yarn` to
be available so it can install dependencies and build `dist/` before copying
compiled assets into `webroot`.

## npm package

The npm package remains `@lbdistrictscouts/district-styles`.

## Installing from GitHub Packages

### 1. Configure your project to use GitHub Packages for the `@lbdistrictscouts` scope

Add or update `.yarnrc.yml` in your consuming project:

```yaml
nodeLinker: node-modules

npmScopes:
  lbdistrictscouts:
    npmRegistryServer: "https://npm.pkg.github.com"
    npmAlwaysAuth: true
```

### 2. Authenticate

Set the `YARN_NPM_AUTH_TOKEN` environment variable to a GitHub token with `read:packages` scope (and `repo` if the repository is private):

```bash
export YARN_NPM_AUTH_TOKEN="ghp_your_token_here"
```

For CI/CD, store the token as a repository secret and pass it via the environment.

### 3. Install

```bash
yarn add @lbdistrictscouts/district-styles
```

## Usage

### React — import prebuilt CSS

```ts
// In your app entry point (e.g. main.tsx / index.tsx)
import '@lbdistrictscouts/district-styles/css';
```

Or reference the file directly:

```ts
import '@lbdistrictscouts/district-styles/dist/css/district-styles.css';
```

### React — SCSS override pattern

If you want to override Bootstrap variables before they are compiled, import the SCSS entrypoint instead of the prebuilt CSS. Configure your bundler (Vite / Webpack / Next.js) with Sass support, then in your own SCSS file:

```scss
// app/styles/theme.scss

// 1. Override Bootstrap variables BEFORE importing the library
$primary: #ff6600;
$font-family-sans-serif: "Inter", system-ui, sans-serif;

// 2. Import the library entrypoint (compiles Bootstrap + district styles)
@use "@lbdistrictscouts/district-styles/scss";
```

**Vite example** (`vite.config.ts`):

```ts
import { defineConfig } from 'vite';
export default defineConfig({
  css: {
    preprocessorOptions: {
      scss: {
        // Allow @use with node_modules paths
        includePaths: ['node_modules'],
      },
    },
  },
});
```

### Page shell variants

The package includes a reusable shell and surface treatment for branded landing pages:

```html
<div class="lbd-page-shell lbd-page-shell--vivid lbd-page-shell--topography">
  <main class="container-fluid">
    <section class="lbd-page-surface">...</section>
  </main>
</div>
```

Available modifiers:

- `lbd-page-shell--vivid`: stronger district colour wash
- `lbd-page-shell--topography`: larger, softer repeated topography pattern
- `lbd-page-shell--topography-dense`: smaller, higher-density topography pattern
- `lbd-page-shell--plum`: alternate primary-led colourway using the district purple

You can also mix and match with local overrides by setting CSS variables on `.lbd-page-shell`, for example:

```scss
.lbd-page-shell {
  --lbd-page-shell-topography-size: 420px;
  --lbd-page-shell-topography-opacity: 0.7;
  --lbd-page-shell-topography-rotation: 4deg;
}
```

### CakePHP — copy `dist/` into webroot

If you are not running a JS build pipeline on the PHP side, copy the compiled assets during your deploy step:

```bash
cp -r node_modules/@lbdistrictscouts/district-styles/dist/ webroot/district-styles/
```

Then include the stylesheet in your layout:

```php
<?= $this->Html->css('/district-styles/css/district-styles.css') ?>
```

Or use the minified variant for production:

```php
<?= $this->Html->css('/district-styles/css/district-styles.min.css') ?>
```

The page shell topography asset is included in `dist/assets`, so the copied
`dist/` directory needs to stay intact.

## Build outputs

| File | Description |
|------|-------------|
| `dist/css/district-styles.css` | Full expanded CSS with sourcemap |
| `dist/css/district-styles.css.map` | Sourcemap for the expanded CSS |
| `dist/css/district-styles.min.css` | Minified CSS (no sourcemap) |
| `dist/assets/topography.svg` | Shared runtime asset used by the page shell |

## Package exports

| Import specifier | Resolves to |
|-----------------|-------------|
| `@lbdistrictscouts/district-styles/css` | `dist/css/district-styles.css` |
| `@lbdistrictscouts/district-styles/css/min` | `dist/css/district-styles.min.css` |
| `@lbdistrictscouts/district-styles/dist/assets/*` | `dist/assets/*` |
| `@lbdistrictscouts/district-styles/scss` | `scss/style.scss` |
| `@lbdistrictscouts/district-styles/scss/*` | `scss/*` |
| `@lbdistrictscouts/district-styles/assets/*` | `assets/*` |

## Development

```bash
# Install dependencies
yarn install

# Build all outputs
yarn build

# Run the frontend/style testsuite
yarn test:styles

# Run the Playwright browser checks only
yarn test:styles:ui

# Build expanded CSS only (with sourcemap)
yarn build:css

# Build minified CSS only
yarn build:css:min

# Remove dist/
yarn clean
```

```bash
# Install PHP test dependencies
composer install

# Run the CakePHP plugin testsuite
composer test:plugin
```

## Publishing (maintainers)

Releases are published automatically to GitHub Packages when a git tag matching `v*.*.*` is pushed:

```bash
git tag v0.2.0
git push origin v0.2.0
```

The GitHub Actions workflow ([`ci-cd.yml`](/Users/jacob/Development/district-styles/.github/workflows/ci-cd.yml)) will run the Yarn test/build commands and publish using the built-in `GITHUB_TOKEN`.

## Plugin asset build

For the CakePHP plugin path, `bin/cake district_ui install` now does this from
the plugin directory:

```bash
yarn install --immutable
yarn build
```

It then copies the built CSS, JS, fonts, and `dist/assets` output into the
plugin `webroot/` before `plugin assets copy` publishes them into the host
application.

## License

MIT © Jacob Tyler
