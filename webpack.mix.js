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
  .options({
    processCssUrls: false,
  });

mix
  .version()
  .sourceMaps();
