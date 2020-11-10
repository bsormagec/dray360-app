<template>
  <div :class="`document ${dimensions.width && dimensions.height ? 'loaded' : ''}`">
    <slot />
    <div
      v-for="(page, pIndex) in pages"
      :key="pIndex"
      class="document__page"
    >
      <img
        :class="{ loaded: dimensions.width && dimensions.height }"
        :src="page.image"
        @load="getDimensions"
      >

      <div v-if="dimensions.width && dimensions.height">
        <div
          v-for="(highlight, hIndex) in page.highlights"
          :id="`${cleanStrForId(highlight.name)}-document`"
          :key="hIndex"
          :style="getPos(highlight)"
          :class="{
            page__highlight: true,
            edit: highlight.edit,
            hover: highlight.hover
          }"
          @click="startEdit({
            field: { name: highlight.name },
            pageIndex: pIndex,
            highlightIndex: hIndex
          })"
          @mouseover="isMobile ? () => {} : startHover({
            field: { name: highlight.name },
            pageIndex: pIndex,
            highlightIndex: hIndex
          })"
          @mouseleave="isMobile ? () => {} : stopHover({
            field: { name: highlight.name },
            pageIndex: pIndex,
            highlightIndex: hIndex
          })"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { documentModule } from '@/views/Details/inner_store/index'
import { cleanStrForId } from '@/views/Details/inner_utils/clean_str_for_id'
import isMobile from '@/mixins/is_mobile'

export default {
  name: 'DetailsDocument',

  mixins: [isMobile],

  data: () => ({
    dimensions: {
      width: undefined,
      height: undefined
    }
  }),

  computed: {
    pages () {
      return documentModule.state.document
    }
  },

  methods: {
    cleanStrForId,
    startEdit: documentModule.methods.startEdit,
    startHover: documentModule.methods.startHover,
    stopHover: documentModule.methods.stopHover,

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
  padding: rem(26);
  background-color: #E5EAEF;
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
    padding: rem(16);

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
    margin-bottom: rem(26);
  }
}

.page__highlight {
  cursor: pointer;
  position: absolute;
  background: rgba(yellow, 0.3);
  border: rem(1) solid rgba(yellow, 0.3);
  transition: all 200ms ease-in-out;

  &.hover, &.edit {
    border-color: var(--v-primary-base);
    background: rgba(var(--v-primary-base-rgb), 0.3);
  }
}
</style>
