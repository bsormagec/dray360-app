import store from '@/store/index'

/**
 * Vue-router Middleware based on https://blog.logrocket.com/vue-middleware-pipelines/
 * @module @/router/middleware
 */

export function runMiddleware (to, from, next) {
  if (!to.matched.some(route => route.meta.middleware)) {
    return next()
  }

  // Collect middleware from all matching parent and child routes.
  const middleware = to.matched.reduce(
    (middleware, route) => (route.meta.middleware ? middleware.concat(route.meta.middleware) : middleware),
    []
  )

  const context = {
    to,
    from,
    next,
    store
  }

  return middleware[0]({
    ...context,
    next: pipeline(context, middleware, 1)
  })
}

export function pipeline (context, middleware, index) {
  const nextMiddleware = middleware[index]

  if (!nextMiddleware) {
    return context.next
  }

  return (...parameters) => {
    if (parameters.length) {
      // Short-circuit
      return context.next(...parameters)
    }

    const nextPipeline = pipeline(context, middleware, index + 1)

    return nextMiddleware({ ...context, next: nextPipeline })
  }
}
