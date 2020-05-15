<template>
  <div class="details">
    <DetailsSidebar />

    <div
      class="details__form"
      :style="{ minWidth: `${resizeDiff}%` }"
    >
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

    <DetailsDocument />
  </div>
</template>

<script>
import DetailsSidebar from '@/views/Details/DetailsSidebar'
import DetailsFormEditing from '@/views/Details/DetailsFormEditing'
import DetailsFormViewing from '@/views/Details/DetailsFormViewing'
import DetailsDocument from '@/views/Details/DetailsDocument'
import { formModule, documentModule } from '@/views/Details/inner_store/index'
import { exampleForm as form } from '@/views/Details/inner_utils/example_form'
import { parsedDocument } from '@/views/Details/inner_utils/parse_document'

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
      return formModule.state.isEditing
    }
  },

  beforeMount () {
    formModule.methods.setForm(form)
    documentModule.methods.setDocument(parsedDocument)
  },

  methods: {
    handleResize (e) {
      this.startPos = e.clientX
      window.onmousemove = this.startDragging
      window.onmouseup = this.stopDragging
    },

    startDragging (e) {
      e.preventDefault()
      const endPos = e.clientX - this.startPos
      this.setResizeDiff(endPos >= 0 ? 1.5 : -1.5)
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
