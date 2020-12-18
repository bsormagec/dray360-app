<template>
  <div
    v-if="hasFile"
    :class="`document ${dimensions.width && dimensions.height ? 'loaded' : ''}`"
  >
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
            @click="() => scrollToAndStartFieldEdit(row.field_key)"
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
    dimensions: {
      width: undefined,
      height: undefined
    }
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
    hasFile () {
      return !!this.currentOrder.ocr_data.ocr_data_filename.value
    }
  },

  methods: {
    ...mapActions(orderForm.moduleName, {
      startHover: types.startHover,
      stopHover: types.stopHover,
      startFieldEdit: types.startFieldEdit
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
      scrollTo(`${cleanStrForId(highlightKey)}-formfield`)
      this.startFieldEdit({ path: highlightKey })
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
