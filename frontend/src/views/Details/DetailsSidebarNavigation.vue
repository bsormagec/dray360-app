<template>
  <div class="navigation">
    <v-stepper
      v-model="current"
      vertical
      non-linear
    >
      <Fragment
        v-for="(step, i) in steps"
        :key="step.id"
      >
        <v-stepper-step
          :class="{navigation__step: true, active: isActive(step.id), hide: shouldHide(step.id)}"
          :step="step.id"
          @click="setStep(step.id)"
        >
          {{ step.text }}
        </v-stepper-step>

        <v-stepper-content
          :class="{ navigation__separator: true, hide: shouldHide(step.id) }"
          :step="i"
        />
      </Fragment>
    </v-stepper>
  </div>
</template>

<script>
import { Fragment } from 'vue-fragment'

export default {
  name: 'DetailsSidebarNavigation',

  components: {
    Fragment
  },

  data: () => ({
    current: 1,
    steps: [
      {
        id: 1,
        text: 'Shipment'
      },
      {
        id: 1.1,
        text: 'Equipment'
      },
      {
        id: 1.2,
        text: 'Origin'
      },
      {
        id: 1.3,
        text: 'Billing'
      },
      {
        id: 2,
        text: 'Itinerary'
      },
      {
        id: 3,
        text: 'Inventory'
      },
      {
        id: 4,
        text: 'Notes'
      }
    ]
  }),

  methods: {
    setStep (n) {
      this.current = n
    },

    isActive (n) {
      if (this.current === n) return true
      if (String(this.current).split('.')[0] === String(n)) return true
    },

    shouldHide (n) {
      const isTitle = String(n).split('.').length === 1

      if (isTitle) return false
      const titlePart = String(n).split('.')[0]
      const titlePartLeading = String(this.current).split('.')[0] === titlePart
      return !titlePartLeading
    }
  }
}
</script>

<style lang="scss" scoped>
.navigation {
  display: flex;
  margin-top: 3.1rem;
  margin-bottom: 3.1rem;
}
</style>
