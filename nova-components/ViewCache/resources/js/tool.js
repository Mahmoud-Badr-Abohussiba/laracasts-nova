Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'viewCache',
      path: '/viewCache',
      component: require('./components/Tool'),
    },
  ])
})
