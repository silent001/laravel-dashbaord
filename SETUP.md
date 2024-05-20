# Setup process for creating this project

## Creating the project

### Setup project using sail
```shell
curl -s "https://laravel.build/laravel-dashboard?with=mysql,mailpit&devcontainer" | bash
```

### Create bash alias
```shell
echo "alias sail='sh \$( [ -f sail ] && echo sail || echo vendor/bin/sail )'" > ~/.bash_aliases
source ~/.bashrc
```

### Start the container with default .env values
```shell
sail up -d
```
## Migrate from Vite to Laravel Mix <sup>[1](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-vite-to-laravel-mix)</sup>

We need to repalce some lines in [package.json](package.json) to remove Vite and switch to old style [Laravel Mix](https://laravel.com/docs/11.x/mix)

### Install Laravel Mix

First, you will need to install Laravel Mix using the npm package manager:

```shell
sail npm install --save-dev laravel-mix
```

### Configure Mix

Create a `webpack.mix.js` file in the root of your project:

```
const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
```

### Update NPM scripts

Update your NPM scripts in `package.json`:

```diff
  "scripts": {
-     "dev": "vite",
-     "build": "vite build"
+     "dev": "npm run development",
+     "development": "mix",
+     "watch": "mix watch",
+     "watch-poll": "mix watch -- --watch-options-poll=1000",
+     "hot": "mix watch --hot",
+     "prod": "npm run production",
+     "production": "mix --production"
  }
```

You should also remove the `type` key by running the following command:

```shell
sail npm pkg delete type
```

#### Inertia

Vite requires a helper function to import page components which is not required with Laravel Mix. You can remove this as follows:

```diff
- import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

  createInertiaApp({
      title: (title) => `${title} - ${appName}`,
-     resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
+     resolve: (name) => require(`./Pages/${name}.vue`),
      setup({ el, app, props, plugin }) {
          return createApp({ render: () => h(app, props) })
              .use(plugin)
              .mixin({ methods: { route } })
              .mount(el);
      },
  });
```

### Update environment variables

You will need to update the environment variables that are explicitly exposed in your `.env` files and in hosting environments such as Forge to use the `MIX_` prefix instead of `VITE_`:

```diff
- VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
- VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
+ MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
+ MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

You will also need to update these references in your JavaScript code to use the new variable name and Node syntax:

```diff
-    key: import.meta.env.VITE_PUSHER_APP_KEY,
-    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
+    key: process.env.MIX_PUSHER_APP_KEY,
+    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
```

### Remove CSS imports from your JavaScript entry point(s)

If you are importing your CSS via JavaScript, you will need to remove these statements:

```js
- import '../css/app.css';
```

### Replace `@vite` with `mix()`

You will need to replace the `@vite` Blade directive with `<script>` and `<link rel="stylesheet">` tags and the `mix()` helper:

```diff
- @viteReactRefresh
- @vite('resources/js/app.js')
+ <link rel="stylesheet" href="{{ mix('css/app.css') }}">
+ <script src="{{ mix('js/app.js') }}" defer></script>
```

### Remove Vite and the Laravel Plugin

Vite and the Laravel Plugin can now be uninstalled:

```shell
sail npm remove vite laravel-vite-plugin
```

Next, you may remove your Vite configuration file:

```shell
rm vite.config.js
```

You may also wish to remove any `.gitignore` paths you are no longer using:

```gitignore
- /bootstrap/ssr
- /public/build
```

## Setting up Laravel UI and Bootstrap Auth

### Install Laravel UI
```shell
sail composer require laravel/ui
```

### Install Bootstrap Auth Scaffolding
```shell
sail artisan ui bootstrap --auth
```

### Install and Compile NPM Packages
```shell
sail npm install && sail npm run dev
```
