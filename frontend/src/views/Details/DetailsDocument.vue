/*
  TODO:
  - Hover on yellow block, updates its style to hover state and sets hover state in its related field on the form *
  - Hovering on any field (viewing) triggers hover state in its related document field
  - Clicking the edit icon in the hover component (viewing) on the form changes it to edit mode
  - Upon accepting changes in hover component it updates its field value
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
        class="page__highlight"
        @click="requestField(highlight.field)"
      />
    </div>
  </div>
</template>

<script>
import exampleDocument from '@/views/Details/inner_utils/example_document'
import { detailsState, detailsMethods } from '@/views/Details/inner_store'

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
    },

    requestField (fieldName) {
      try {
        for (const sectionKey in detailsState.form.sections) {
          for (const rootFieldKey in detailsState.form.sections[sectionKey].rootFields) {
            if (rootFieldKey === fieldName) {
              detailsMethods.setFormFieldEditingByDocument({
                value: true,
                location: `${sectionKey}/rootFields/${fieldName}`
              })
              throw new Error()
            }
          }

          for (const subSectionKey in detailsState.form.sections[sectionKey].subSections) {
            for (const fieldKey in detailsState.form.sections[sectionKey].subSections[subSectionKey].fields) {
              if (fieldKey === fieldName) {
                detailsMethods.setFormFieldEditingByDocument({
                  value: true,
                  location: `${sectionKey}/subSections/${subSectionKey}/fields/${fieldName}`
                })
                throw new Error()
              }
            }
          }
        }
      } catch (e) {
        return e
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
