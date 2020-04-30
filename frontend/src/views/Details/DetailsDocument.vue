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
    pages: [
      {
        image: 'https://firebasestorage.googleapis.com/v0/b/general-f0201.appspot.com/o/2121593_1.jpg?alt=media&token=bf3e2b33-7587-49ae-9f12-ffe11f1d26c6',
        highlights: [
          {
            bottom: 803,
            left: 1715,
            right: 1907,
            top: 775
          }
        ]
      },
      {
        image: 'https://firebasestorage.googleapis.com/v0/b/general-f0201.appspot.com/o/2121593_2.jpg?alt=media&token=aa21dcb3-e8cb-48d7-a513-915e68f909f8',
        highlights: []
      }
    ]
  }),

  beforeMount () {
    this.parseDocument()
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

    parseDocument () {
      const { BlocksInfo } = exampleDocument.Documents[0].AdditionalInfo
      BlocksInfo.forEach(({ Blocks }) => {
        Blocks.forEach(({ Rects, PageIndex }) => {
          Rects.forEach(({ Bottom, Left, Right, Top }) => {
            const invalidRect = Bottom === 3300 && Right === 2550
            if (!invalidRect) {
              this.pages[PageIndex - 1].highlights.push(
                {
                  bottom: Bottom,
                  left: Left,
                  right: Right,
                  top: Top
                }
              )
            }
          })
        })
      })
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
