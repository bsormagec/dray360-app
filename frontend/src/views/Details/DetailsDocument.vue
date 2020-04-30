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
        class="page__highlight"
      />
    </div>
  </div>
</template>

<script>
import exampleDocument from '@/views/Details/inner_utils/example_document'

export default {
  name: 'DetailsDocument',

  data: () => ({
    dimensions: {
      width: 2550,
      height: 3300
    },
    pages: exampleDocument
  }),

  methods: {
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
  background: yellow;
  transition: transform 200ms ease-in-out;
  opacity: 0.4;

  &:hover {
    transform: scale(1.2);
  }
}
</style>
