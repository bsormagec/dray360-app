<template>
  <div :class="`document ${dimensions.width && dimensions.height ? 'loaded' : ''}`">
    <div
      v-for="(page) in pages"
      :key="page.name"
      class="document__page"
    >
      <img
        :class="{ loaded: dimensions.width && dimensions.height }"
        :src="page.image"
        @load="getDimensions"
      >

      <div v-if="dimensions.width && dimensions.height">
        <div
          v-for="(highlight, highlightKey) in getHighlightsForPage(page.number)"
          :id="`${cleanStrForId(highlightKey)}-document`"
          :key="highlightKey"
          :style="getPos(highlight)"
          :class="{
            page__highlight: true,
            edit: highlight.edit,
            hover: highlight.hover
          }"
          @click="() => startFieldEdit({ path: highlightKey })"
          @mouseover="isMobile ? () => {} : startHover({ path: highlightKey })"
          @mouseleave="isMobile ? () => {} : stopHover({ path: highlightKey })"
        />
      </div>
    </div>
  </div>
</template>

<script>
import orderForm, { types } from '@/store/modules/order-form'
import { cleanStrForId } from '@/utils/clean_str_for_id.js'
import { mapState, mapActions } from 'vuex'
import isMobile from '@/mixins/is_mobile'

export default {
  name: 'OrderDetailsDocument',

  mixins: [isMobile],

  data: () => ({
    dimensions: {
      width: undefined,
      height: undefined
    }
  }),

  computed: {
    ...mapState(orderForm.moduleName, {
      highlights: state => state.highlights,
      pages: state => state.pages
    })
  },

  methods: {
    ...mapActions(orderForm.moduleName, {
      startHover: types.startHover,
      stopHover: types.stopHover,
      startFieldEdit: types.startFieldEdit
    }),
    cleanStrForId,
    getHighlightsForPage (pageNumber) {
      const pageHighlights = {}
      for (const key in this.highlights) {
        if (parseInt(this.highlights[key].page_index) === parseInt(pageNumber)) {
          pageHighlights[key] = this.highlights[key]
        }
      }
      return pageHighlights
    },

    getDimensions (evt) {
      const dimensions = (evt.path && evt.path[0]) || evt.target

      this.dimensions.width = dimensions.naturalWidth
      this.dimensions.height = dimensions.naturalHeight
    },

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
  scroll-behavior: smooth;
  width: 100%;

  &.loaded {
    width: unset;
  }

  &.mobile {
    order: -1;
    height: 50vh;
    max-width: 100vw;
    width: 100%;
    padding: 1.6rem;

    & .document__page {
      min-width: 100%;
    }
  }
}

.document__page {
  position: relative;
  width: 100%;

  img {
    opacity: 0;
    width: 100%;
    object-fit: contain;
    transition: opacity 200ms ease-in-out;

    &.loaded {
      opacity: 1;
    }
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
    border-color: var(--v-primary-base);
    background: rgba(var(--v-primary-base-rgb), 0.3);
  }
}
</style>
