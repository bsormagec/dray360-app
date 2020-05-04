/*
  TODO:
  - Hover on yellow block, updates its style to hover state and sets hover state in its related field on the form
  - Upon accepting changes in form field component it updates its field value and document field is no longer selected *
  - Hovering on any field (viewing) triggers hover state in its related document field
  - Adjust EditingSetByDocument styles (height, though it may be needed to change to input)
  - Add scrolling
*/

<template>
  <div class="document">
    <div
      v-for="(page, pIndex) in pages"
      :key="pIndex"
      class="document__page"
    >
      <img :src="page.image">

      <div
        v-for="(highlight, hIndex) in page.highlights"
        :key="hIndex"
        :style="getPos(highlight)"
        :class="{page__highlight: true, edit: highlight.edit}"
        @click="startEdit({
          fieldName: highlight.field,
          pageIndex: pIndex,
          highlightIndex: hIndex
        })"
        @mouseover="startHover({
          fieldName: highlight.field
        })"
        @mouseleave="stopHover({
          fieldName: highlight.field
        })"
      />
    </div>
  </div>
</template>

<script>
import { detailsState, detailsMethods } from '@/views/Details/inner_store'
import { modes, pools } from '@/views/Details/inner_types'
import { getFieldLocation } from '@/views/Details/inner_utils/get_field_location'

export default {
  name: 'DetailsDocument',

  data: () => ({
    dimensions: {
      width: 2550,
      height: 3300
    },
    lastMode: undefined
  }),

  computed: {
    pages () {
      return detailsState.document
    }
  },

  methods: {
    getPos ({ bottom, left, right, top }) {
      return {
        top: `${(top / this.dimensions.height) * 100}%`,
        left: `${(left / this.dimensions.width) * 100}%`,
        width: `${((right - left) / this.dimensions.width) * 100}%`,
        height: `${((bottom - top) / this.dimensions.height) * 100}%`
      }
    },

    /* TODO: These methods must me moved to inner_store */
    startEdit ({ fieldName, pageIndex, highlightIndex }) {
      if (!fieldName) return

      this.$set(this.pages[pageIndex].highlights[highlightIndex], 'edit', true)
      detailsMethods.setFormFieldEditingByDocument({
        value: modes.edit,
        location: getFieldLocation({ pool: detailsState.form, poolType: pools.form, fieldName })
      })
      this.lastMode = modes.edit
    },

    startHover ({ fieldName }) {
      if (!fieldName) return
      if (this.lastMode === modes.edit) return

      detailsMethods.setFormFieldEditingByDocument({
        value: modes.hover,
        location: getFieldLocation({ pool: detailsState.form, poolType: pools.form, fieldName })
      })
      this.lastMode = modes.hover
    },

    stopHover ({ fieldName }) {
      if (!fieldName) return

      if (this.lastMode === modes.hover) {
        detailsMethods.setFormFieldEditingByDocument({
          value: undefined,
          location: getFieldLocation({ pool: detailsState.form, poolType: pools.form, fieldName })
        })
        this.lastMode = undefined
      }
    }
  }
  /* end */
}
</script>

<style lang="scss" scoped>
.document {
  height: 100vh;
  flex-grow: 1;
  overflow-y: auto;
  padding: 2.6rem;
  background: map-get($colors, grey-8);
}

.document__page {
  position: relative;
  width: 100%;

  img {
    width: 100%;
    object-fit: contain;
  }

  &:not(:last-child) {
    margin-bottom: 2.6rem;
  }
}

.page__highlight {
  cursor: pointer;
  position: absolute;
  background: rgba(yellow, 0.3);
  border: 0.1rem solid rgba(yellow, 0.3);
  transition: all 200ms ease-in-out;

  &:hover, &.edit {
    border-color: map-get($colors , blue);
    background: rgba(map-get($colors, blue), 0.3);
  }
}
</style>
