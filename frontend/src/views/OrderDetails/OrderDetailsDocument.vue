<template>
  <div
    v-if="hasTabularDataFile"
    :class="`document ${loadedImages === pages.length ? 'loaded' : ''}`"
  >
    <div
      v-for="(page, index) in pages"
      :key="page.name"
      class="document__page"
    >
      <div
        v-show="!page.loaded"
        class="loader"
      >
        <img
          src="@/assets/images/loading-animation.gif"
        >
      </div>
      <img
        v-show="page.loaded"
        :class="{ loaded: page.loaded }"
        :src="page.image"
        @load="(e) => getDimensions(e, index)"
      >

      <div v-if="page.loaded">
        <div
          v-for="(highlight, highlightKey) in getHighlightsForPage(page.number)"
          :id="`${cleanStrForId(highlightKey)}-document`"
          :key="highlightKey"
          :style="getPos(highlight, page)"
          :class="{
            page__highlight: true,
            edit: highlight.edit,
            hover: highlight.hover
          }"
          @click="() => scrollToAndStartFieldEdit(highlightKey)"
          @mouseover="isMobile ? () => {} : startHover({ path: highlightKey })"
          @mouseleave="isMobile ? () => {} : stopHover({ path: highlightKey })"
        />
      </div>
    </div>
  </div>
  <div
    v-else
    class="document"
  >
    <v-simple-table class="table">
      <template v-slot:default>
        <tbody>
          <tr
            v-for="row in tableData"
            :key="row.field_key"
            :class="{
              'table-highlight': true,
              edit: safeGet(row, 'highlight.edit', false),
              hover: safeGet(row, 'highlight.hover', false)
            }"
            @click.prevent="() => scrollToAndStartFieldEdit(row.field_key)"
            @mouseover="isMobile || row.highlight === undefined ? () => {} : startHover({ path: row.field_key })"
            @mouseleave="isMobile || row.highlight === undefined ? () => {} : stopHover({ path: row.field_key })"
          >
            <th v-text="row.name" />
            <td v-text="row.value" />
          </tr>
        </tbody>
      </template>
    </v-simple-table>
  </div>
</template>

<script>
import orderForm, { types } from '@/store/modules/order-form'
import { cleanStrForId } from '@/utils/clean_str_for_id.js'
import { mapState, mapActions } from 'vuex'
import isMobile from '@/mixins/is_mobile'
import { scrollTo } from '@/utils/scroll_to'
import { getNonPDFHighlightsParsedFieldName } from '@/utils/order_form_general_functions.js'
import safeGet from 'lodash/get'

export default {
  name: 'OrderDetailsDocument',

  mixins: [isMobile],

  data: () => ({
    loadedImages: 0
  }),

  computed: {
    ...mapState(orderForm.moduleName, {
      highlights: state => state.highlights,
      pages: state => state.pages,
      currentOrder: state => state.order
    }),
    tableData () {
      const fields = this.currentOrder.ocr_data.original_fields
      return Object.keys(fields)
        .map(field => {
          const fieldKey = getNonPDFHighlightsParsedFieldName(field)
          return {
            name: fields[field].name,
            value: fields[field].value,
            field_key: fieldKey,
            highlight: this.highlights[fieldKey]
          }
        })
    },
    hasTabularDataFile () {
      try {
        ocrDataFilename = this.currentOrder.ocr_data.ocr_data_filename.value
        // after feb2021 "pdftext" variant type datafile uploads will have their parsed data json filename stored here
        // after feb2021, any non ".json" datafile name indicates "hasFile"
        return !ocrDataFilename.toLowerCase().endsWith('json')
      } catch (error) {
        return true // before feb2021, ocr_data_filename being undefined indicated a csv/xlsx datafile upload
      }
  },

  methods: {
    ...mapActions(orderForm.moduleName, {
      startHover: types.startHover,
      stopHover: types.stopHover,
      startFieldEdit: types.startFieldEdit,
      setPage: types.setPage
    }),
    safeGet,
    cleanStrForId,
    getHighlightsForPage (pageNumber) {
      const pageHighlights = {}
      for (const key in this.highlights) {
        if (parseInt(this.highlights[key].page_index) === parseInt(pageNumber) + 1) {
          pageHighlights[key] = this.highlights[key]
        }
      }
      return pageHighlights
    },

    scrollToAndStartFieldEdit (highlightKey) {
      const offsetElement = this.$parent.$children[0].$refs.orderHeading
      scrollTo(`${cleanStrForId(highlightKey)}-formfield`, '.form', offsetElement.scrollHeight)
      this.startFieldEdit({ path: highlightKey })
    },

    getDimensions (evt, index) {
      const dimensions = (evt.path && evt.path[0]) || evt.target
      const page = {
        ...this.pages[index],
        loaded: true,
        width: dimensions.naturalWidth,
        height: dimensions.naturalHeight
      }

      this.setPage({ index, page })
      this.loadedImages++
    },

    getPos ({ bottom, left, right, top }, { width, height }) {
      return {
        top: `${(top / height) * 100}%`,
        left: `${(left / width) * 100}%`,
        width: `${((right - left) / width) * 100}%`,
        height: `${((bottom - top) / height) * 100}%`
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

  .loader {
    background-color: white;
    display: flex;
    width: 100%;
    min-height: rem(700);
    justify-content: center;
    align-items: center;

    img {
      opacity: 1;
      width: rem(125);
    }
  }

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
  background: rgba($sandy, .4);
  transition: background-color 200ms ease-in-out;

  &.hover, &.edit {
    background: rgba($blue--lt, .4);
  }

}

.v-data-table.table {
  .v-data-table__wrapper {
    table {
      tbody {
        box-shadow: -1px -1px 0px 0px #E6ECF1 inset;

        tr {
          &.table-highlight {
            cursor: pointer;
            transition: background-color 200ms ease-in-out;
          }
          &:hover,
          &.hover,
          &.edit {
            background-color: #eaf3ee !important;
          }
          &:last-child th,
          &:last-child td {
            border-bottom: none;
          }
          td, th {
            padding: rem(6) rem(12);
            font-size: rem(12);
            line-height: (24 / 12);
            letter-spacing: rem(.15);
            font-weight: 500;
            border-top: 1px solid #FFF;
            border-bottom: 1px solid #FFF;
          }
          th {
            width: rem(120);
            text-align: right;
            color: white;
            background-color: #478854;
            border-color: #FFF;
          }
          td {
            font-weight: 400;
            letter-spacing: rem(.25);
            line-height: (20 / 12);
            border-color: map-get($colors, gray-4);
          }
        }
      }
    }
  }
}
</style>
