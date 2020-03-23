module.exports = {
  // proxy API requests to Valet during development
  // TODO - test this use of the env var is working right
  devServer: {
    proxy: process.env.APP_URL // was hardcoded: 'http://laracon.test'
  },

  // output built static files to Laravel's public dir.
  // note the "build" script in package.json needs to be modified as well.
  outputDir: '../public',

  // modify the location of the generated HTML file.
  // make sure to do this only in production.
  indexPath: process.env.NODE_ENV === 'production'
    ? '../resources/views/index.blade.php'
    : 'index.html'
}
