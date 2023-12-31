const mix = require('laravel-mix');

mix
  .setPublicPath('dist')
  .browserSync({
    proxy: "http://test.test",
    files: [
      'dist/**/**',
      '**/*.php',
    ],
  });

mix
  .js('resources/scripts/admin-acfseo.js', 'js')
  .options({
    processCssUrls: false,
  });

mix
  .version()
  .sourceMaps();
