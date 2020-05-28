<template>
  <div :class="`details ${loaded && 'loaded'}`">
    <ContentLoading :loaded="loaded">
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
    </ContentLoading>
  </div>
</template>

<script>
import DetailsSidebar from '@/views/Details/DetailsSidebar'
import DetailsFormEditing from '@/views/Details/DetailsFormEditing'
import DetailsFormViewing from '@/views/Details/DetailsFormViewing'
import DetailsDocument from '@/views/Details/DetailsDocument'
import { reqStatus } from '@/enums/req_status'
import { uuid } from '@/utils/uuid_valid_id'
import { defaultsTo } from '@/utils/defaults_to'

import ContentLoading from '@/components/ContentLoading'
import { formModule, documentModule } from '@/views/Details/inner_store/index'
import { exampleForm as form, buildField } from '@/views/Details/inner_utils/example_form'
import { parse } from '@/views/Details/inner_utils/parse_document'
import orders, { types } from '@/store/modules/orders'
import { mapState, mapActions } from '@/utils/vuex_mappings'

export default {
  name: 'Details',

  components: {
    DetailsSidebar,
    DetailsFormEditing,
    DetailsFormViewing,
    DetailsDocument,
    ContentLoading
  },

  data: () => ({
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    }),
    resizeDiff: 50,
    startPos: 0,
    loaded: false
  }),

  computed: {
    isEditing () {
      return formModule.state.isEditing
    }
  },

  beforeMount () {
    formModule.methods.setForm(form)
  },

  async mounted () {
    await this.requestOrderDetail()
    this.loaded = true
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrderDetail]),

    async requestOrderDetail () {
      const status = await this[types.getOrderDetail](this.$route.params.id)

      if (status === reqStatus.success) {
        documentModule.methods.setDocument(
          parse({
            data: this.currentOrder(),
            valSetter: this.docValToFormSetter
          })
        )
        return
      }
      console.log('error')
    },

    docValToFormSetter ({ dray360name = '', data }) {
      const nameParts = dray360name.split('.')

      if (dray360name.includes('order_address_events')) {
        return this.handleOrderAddressEvents(data, nameParts)
      } else if (dray360name.includes('order_line_items')) {
        return this.handleOrderLineItems(data, nameParts)
      }

      return { value: data[dray360name] }
    },

    handleOrderAddressEvents (data, nameParts) {
      const addrEvents = formModule.state.form.sections.itinerary.rootFields
      const evtName = defaultsTo(() => data[nameParts[0]][nameParts[1]].unparsed_event_type, uuid())
      const evtValue = defaultsTo(() => data[nameParts[0]][nameParts[1]].t_address_raw_text, '--')

      this.$set(
        addrEvents,
        evtName,
        buildField({
          type: 'text-area',
          placeholder: evtName
        })
      )

      return { name: evtName, value: evtValue }
    },

    handleOrderLineItems (data, nameParts) {
      /*
          TODO: handle multiple line items, currently for line items we're parsing the prop "contents"
          it'll be easier to have it being multiple props like order_address_events (for highlighting)
         */
      const lineItems = formModule.state.form.sections.inventory.subSections
      const itemName = uuid()
      let itemDescription

      data[nameParts[0]].forEach(item => {
        itemDescription = defaultsTo(() => item.description, '--')

        this.$set(
          lineItems,
          itemName,
          {
            fields: {
              [`${itemName} description`]: buildField({
                presentationName: 'description',
                type: 'text-area',
                placeholder: 'description'
              })
            }
          }
        )
      })

      return {
        name: `${itemName} description`,
        value: itemDescription
      }
    },

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

  &.loaded {
    padding-left: map-get($sizes, sidebar-desktop-width);
  }
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
