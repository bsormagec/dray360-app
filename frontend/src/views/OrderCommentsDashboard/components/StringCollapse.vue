<template>
  <div>
    <div
      v-if="slicedText[1] === ''"
      class="string-collapser"
    >
      <span>
        {{ slicedText[0] }}
      </span>
    </div>
    <div
      v-else
      class="string-collapser"
    >
      <span>{{ slicedText[0] }}</span>
      <span v-if="expanded">{{ slicedText[1] }}</span>
      <span
        class="string-collapser__button primary--text"
        @click="expanded = !expanded"
      >
        {{ !expanded ? ' more...' : ' less...' }}
      </span>
    </div>
  </div>
</template>

<script>
export default ({
  name: 'StringCollapse',

  props: {
    string: {
      type: String,
      required: true,
      default: ''
    },
    maxLength: {
      type: Number,
      required: false,
      default: 58
    }
  },

  data: (vm) => ({
    text: vm.string,
    limit: vm.maxLength,
    expanded: false
  }),

  computed: {
    slicedText () {
      const string = this.text
      return [
        string.slice(0, this.limit),
        string.slice(this.limit, string.length)
      ]
    }
  }
})
</script>

<style lang="scss" scoped>
.string-collapser {
    & &__button {
        display: inline-block;
        margin-left: rem(6);
        cursor: pointer;
    }
}
</style>
