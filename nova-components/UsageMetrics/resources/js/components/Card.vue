<template>
  <card
      class="report-card"
      style="min-height: 100px"
  >
    <div class="flex justify-between items-center">
      <h5 class="text-xl text-80 font-semibold my-4">{{ label }}</h5>
      <span v-tooltip="{content:tooltip, trigger:'click' }" v-show="tooltip">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path class="fill-current text-80" d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm0-9a1 1 0 0 1 1 1v4a1 1 0 0 1-2 0v-4a1 1 0 0 1 1-1zm0-4a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
      </span>
    </div>
    <p class="text-90 text-3xl">
      {{metric !== undefined ? metric.current : '--'}}
    </p>

    <p v-if="metric !== undefined && !metricEquals" class="flex items-center text-80 font-semibold text-lg mt-3 mb-2">
      <svg
          v-if="metricDecreased"
          xmlns="http://www.w3.org/2000/svg"
          class="text-danger stroke-current mr-2"
          width="24"
          height="24"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
          />
        </svg>
        <svg
          v-if="metricIncreased"
          class="text-success stroke-current mr-2"
          width="24"
          height="24"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
          />
        </svg>
      {{ `${percentageChange}% ${metricDecreased ? 'Decrease': (metricIncreased ? 'Increase' : '')}` }}
    </p>
  </card>
</template>

<script>
export default {
    props: {
      metric: {
        type: Object,
        required: false,
        default: () => undefined
      },
      label: {
        type: String,
        required: true
      },
      tooltip: {
        type: String,
        required: false,
        default: undefined,
      }
    },
    computed: {
      metricIncreased() {
        return this.metric !== undefined
          ? this.metric.current - this.metric.previous > 0
          : false
      },
      metricDecreased() {
        return this.metric !== undefined
          ? this.metric.current - this.metric.previous < 0
          : false
      },
      metricEquals() {
        return this.metric !== undefined
          ? (this.metric.current - this.metric.previous === 0) || this.metric.previous === 0
          : false
      },
      percentageChange() {
        let percentage = 0.0;
        if (this.metric !== undefined && this.metric.previous !== 0) {
          percentage = Math.abs(this.metric.previous - this.metric.current)/this.metric.previous * 100
        }

        return Math.round(percentage);
      }
    }
}
</script>

<style lang="scss" scoped>
.report-card {
    width: calc(1/4 * 100%);
    display: flex;
    flex-direction: column;

    margin: 0.5rem 0.5rem;

    padding: 0 1rem;

}

.animate-spin {
    animation: spin 1s linear infinite!important;
}


</style>
