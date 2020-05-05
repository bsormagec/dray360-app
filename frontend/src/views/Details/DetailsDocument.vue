/*
  TODO:
  - Hover on yellow block, updates its style to hover state and sets hover state in its related field on the form
  - Upon accepting changes in form field component it updates its field value and document field is no longer selected
  - Hovering on any field (viewing) triggers hover state in its related document field *
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
        :class="{
          page__highlight: true,
          edit: highlight.edit,
          hover: highlight.hover
        }"
        @click="startEdit({
          fieldName: highlight.field,
          pageIndex: pIndex,
          highlightIndex: hIndex
        })"
        @mouseover="startHover({
          fieldName: highlight.field,
          pageIndex: pIndex,
          highlightIndex: hIndex
        })"
        @mouseleave="stopHover({
          fieldName: highlight.field,
          pageIndex: pIndex,
          highlightIndex: hIndex
        })"
      />
    </div>
  </div>
</template>

<script>
import { documentModule } from '@/views/Details/inner_store/index'

export default {
  name: 'DetailsDocument',

  data: () => ({
    dimensions: {
      width: 2550,
      height: 3300
    }
  }),

  computed: {
    pages () {
      return documentModule.state.document
    }
  },

  methods: {
    startEdit: documentModule.methods.startEdit,
    startHover: documentModule.methods.startHover,
    stopHover: documentModule.methods.stopHover,

    getPos ({ bottom, left, right, top }) {
      return {
        top: `${(top / this.dimensions.height) * 100}%`,
        left: `${(left / this.dimensions.width) * 100}%`,
        width: `${((right - left) / this.dimensions.width) * 100}%`,
        height: `${((bottom - top) / this.dimensions.height) * 100}%`
      }
    }
  }
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

  &.hover, &.edit {
    border-color: map-get($colors , blue);
    background: rgba(map-get($colors, blue), 0.3);
  }
}
</style>
