<template>
  <div class="navigation">
    <v-stepper
      v-model="current"
      vertical
      non-linear
    >
      <Fragment
        v-for="(step, i) in steps"
        :key="i"
      >
        <v-stepper-step
          v-if="step.text"
          class="navigation__step"
          :complete="current >= step.id"
          :step="step.id"
          @click="setStep(step.id)"
        >
          {{ step.text }}
        </v-stepper-step>

        <v-stepper-content
          v-if="step.type"
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
      { type: 'separator' },
      {
        id: 2,
        text: 'Equipment'
      }
    ]
  }),

  methods: {
    setStep (n) {
      this.current = n
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
