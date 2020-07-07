module.exports = {
  css: {
    loaderOptions: {
      scss: {
        prependData: '@import "@/assets/styles/variables.scss"; @import "@/assets/styles/mixins.scss";'
      }
    }
  },

  // proxy API requests to Valet during development
  // reference: https://cli.vuejs.org/config/#devserver
  devServer: {
    proxy: process.env.APP_URL
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
