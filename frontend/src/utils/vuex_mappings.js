import {
  mapState as vMapState,
  mapMutations as vMapMutations,
  mapActions as vMapActions,
  mapGetters as vMapGetters
} from 'vuex'

const testing = process.env.NODE_ENV === 'test'

export const mapState = (moduleName, bindings) => {
  if (testing) return vMapState(bindings)
  return vMapState(moduleName, bindings)
}

export const mapMutations = (moduleName, bindings) => {
  if (testing) return vMapMutations(bindings)
  return vMapMutations(moduleName, bindings)
}

export const mapActions = (moduleName, bindings) => {
  if (testing) return vMapActions(bindings)
  return vMapActions(moduleName, bindings)
}

export const mapGetters = (moduleName, bindings) => {
  if (testing) return vMapGetters(bindings)
  return vMapGetters(moduleName, bindings)
}
