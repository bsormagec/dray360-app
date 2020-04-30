<template>
  <div class="details">
    <DetailsSidebar />

    <div class="details__form">
      <DetailsFormEditing v-show="isEditing" />
      <DetailsFormViewing v-show="!isEditing" />

      <div
        class="form__resize"
        @mousedown.prevent="handleResize"
      >
        <div />
        <div />
        <div />
      </div>
    </div>

    <DetailsDocument
      :style="{
        paddingLeft: `${resizeDiff}%`,
        willChange: 'padding-left'
      }"
    />
  </div>
</template>

<script>
import DetailsSidebar from '@/views/Details/DetailsSidebar'
import DetailsFormEditing from '@/views/Details/DetailsFormEditing'
import DetailsFormViewing from '@/views/Details/DetailsFormViewing'
import DetailsDocument from '@/views/Details/DetailsDocument'
import { detailsState, detailsMethods } from '@/views/Details/inner_store'
import { exampleForm as form } from '@/views/Details/inner_utils/example_form'

export default {
  name: 'Details',

  components: {
    DetailsSidebar,
    DetailsFormEditing,
    DetailsFormViewing,
    DetailsDocument
  },

  data: () => ({
    resizeDiff: 50,
    startPos: 0
  }),

  computed: {
    isEditing () {
      return detailsState.isEditing
    }
  },

  beforeMount () {
    detailsMethods.setForm(form)
  },

  methods: {
    handleResize (e) {
      this.startPos = e.clientX
      window.onmousemove = this.startDragging
      window.onmouseup = this.stopDragging
    },

    startDragging (e) {
      e.preventDefault()
      document.body.style.cursor = 'col-resize'
      const endPos = this.startPos - e.clientX
      this.setResizeDiff(endPos >= 0 ? 0.5 : -0.5)
    },

    setResizeDiff (diff) {
      if (this.resizeDiff + diff >= 70) {
        return
      }
      if (this.resizeDiff + diff <= 35) {
        return
      }

      this.resizeDiff += diff
    },

    stopDragging (e) {
      e.preventDefault()
      document.body.style.cursor = 'default'
      window.onmousemove = undefined
      window.onmouseup = undefined
    }
  }
}
</script>

<style lang="scss" scoped>
.details {
  width: 100%;
  height: 100%;
  display: flex;
  padding-left: map-get($sizes, sidebar-desktop-width);
}

.details__form {
  position: relative;
  transition: width 300ms ease;
}

.form__resize {
  z-index: 2;
  cursor: col-resize;
  position: absolute;
  top: 50%;
  right: -1.5rem;
  transform: translateY(-50%);
  transition: transform 200ms ease-in-out;
  display: flex;

  &:active {
    transform: translateY(-50%) scale(0.8);
  }

  div {
    width: 0.2rem;
    height: 6rem;
    background: white;

    &:not(:last-child) {
      margin-right: 0.2rem;
    }
  }
}
</style>
