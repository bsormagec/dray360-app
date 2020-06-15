<template>
  <div :class="`details ${loaded && 'loaded'} ${isMobile && 'mobile'}`">
    <ContentLoading :loaded="loaded">
      <div :class="`details__content ${isMobile && 'mobile'}`">
        <DetailsSidebar />

        <div
          :class="`details__form ${isMobile && 'mobile'}`"
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

        <DetailsDocument :class="`${isMobile && 'mobile'}`" />
      </div>
    </ContentLoading>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import DetailsSidebar from '@/views/Details/DetailsSidebar'
import DetailsFormEditing from '@/views/Details/DetailsFormEditing'
import DetailsFormViewing from '@/views/Details/DetailsFormViewing'
import DetailsDocument from '@/views/Details/DetailsDocument'
import { reqStatus } from '@/enums/req_status'

import ContentLoading from '@/components/ContentLoading'
import { formModule, documentModule } from '@/views/Details/inner_store/index'
import { exampleForm as form } from '@/views/Details/inner_utils/example_form'
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

  mixins: [isMobile],

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

  beforeCreate () {
    formModule.methods.setForm(form)
  },

  async beforeMount () {
    await this.requestOrderDetail()
    this.loaded = true
  },

  destroyed () {
    documentModule.methods.reset()
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrderDetail]),

    async requestOrderDetail () {
      const id = process.env.NODE_ENV === 'test' ? 119 : this.$route.params.id // assuming 119 works when testing
      const status = await this[types.getOrderDetail](id)

      if (status === reqStatus.success) {
        documentModule.methods.setDocument(
          parse({
            data: this.currentOrder()
          })
        )
        return
      }
      console.log('error')
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

  &.mobile {
    padding-left: unset;
  }
}

.details__content {
  display: flex;

  &.mobile {
    flex-direction: column;
  }
}

.details__form {
  position: relative;
  transition: width 300ms ease;

  &.mobile {
    height: 50vh;
  }
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
