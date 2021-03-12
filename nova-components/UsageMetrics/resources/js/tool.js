Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'usage-metrics',
      path: '/usage-metrics',
      component: require('./components/Tool'),
    },
  ])
})
