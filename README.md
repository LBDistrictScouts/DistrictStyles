# @lbd-scouts/district-styles

Bootstrap-based SCSS styles for LB District Scout projects. Ships both prebuilt CSS and SCSS source so consuming projects can either drop in the compiled stylesheet or apply local overrides.

## Installing from GitHub Packages

### 1. Configure your project to use GitHub Packages for the `@lbd-scouts` scope

Add or update `.yarnrc.yml` in your consuming project:

```yaml
nodeLinker: node-modules

npmScopes:
  lbd-scouts:
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
yarn add @lbd-scouts/district-styles
```

## Usage

### React — import prebuilt CSS

```ts
// In your app entry point (e.g. main.tsx / index.tsx)
import '@lbd-scouts/district-styles/css';
```

Or reference the file directly:

```ts
import '@lbd-scouts/district-styles/dist/css/district-styles.css';
```

### React — SCSS override pattern

If you want to override Bootstrap variables before they are compiled, import the SCSS entrypoint instead of the prebuilt CSS. Configure your bundler (Vite / Webpack / Next.js) with Sass support, then in your own SCSS file:

```scss
// app/styles/theme.scss

// 1. Override Bootstrap variables BEFORE importing the library
$primary: #ff6600;
$font-family-sans-serif: "Inter", system-ui, sans-serif;

// 2. Import the library entrypoint (compiles Bootstrap + district styles)
@use "@lbd-scouts/district-styles/scss";
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

### CakePHP — copy `dist/` into webroot

If you are not running a JS build pipeline on the PHP side, copy the compiled assets during your deploy step:

```bash
cp -r node_modules/@lbd-scouts/district-styles/dist/ webroot/district-styles/
```

Then include the stylesheet in your layout:

```php
<?= $this->Html->css('/district-styles/css/district-styles.css') ?>
```

Or use the minified variant for production:

```php
<?= $this->Html->css('/district-styles/css/district-styles.min.css') ?>
```

## Build outputs

| File | Description |
|------|-------------|
| `dist/css/district-styles.css` | Full expanded CSS with sourcemap |
| `dist/css/district-styles.css.map` | Sourcemap for the expanded CSS |
| `dist/css/district-styles.min.css` | Minified CSS (no sourcemap) |

## Package exports

| Import specifier | Resolves to |
|-----------------|-------------|
| `@lbd-scouts/district-styles/css` | `dist/css/district-styles.css` |
| `@lbd-scouts/district-styles/css/min` | `dist/css/district-styles.min.css` |
| `@lbd-scouts/district-styles/scss` | `scss/style.scss` |
| `@lbd-scouts/district-styles/scss/*` | `scss/*` |

## Development

```bash
# Install dependencies
yarn install

# Build all outputs
yarn build

# Build expanded CSS only (with sourcemap)
yarn build:css

# Build minified CSS only
yarn build:css:min

# Remove dist/
yarn clean
```

## Publishing (maintainers)

Releases are published automatically to GitHub Packages when a git tag matching `v*.*.*` is pushed:

```bash
git tag v0.2.0
git push origin v0.2.0
```

The GitHub Actions workflow (`.github/workflows/publish.yml`) will run `yarn build` and `yarn npm publish` using the built-in `GITHUB_TOKEN`.

## License

MIT © Jacob Tyler
