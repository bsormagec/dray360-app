<template>
  <div class="create">
    <div class="create__close">
      <v-icon
        :style="{ position: 'absolute', top: '2.4rem', left: '1.5rem' }"
        @click="toggleMobileSidebar"
      >
        mdi-menu
      </v-icon>
    </div>

    <div class="create__header">
      <v-btn
        class="header__btn"
        color="primary"
        outlined
      >
        Create Order Manually
      </v-btn>
    </div>

    <OrdersCreateUpload
      :files="files"
      :add-files="addFiles"
      :delete-all="deleteAll"
    />

    <OrdersCreateSubmitted
      :files="files"
      :delete-file="deleteFile"
    />

    <v-btn
      color="primary"
      :style="{ marginLeft: 'auto', marginTop: '1.1rem' }"
    >
      create order
    </v-btn>
  </div>
</template>

<script>
import OrdersCreateUpload from '@/views/Orders/OrdersCreateUpload'
import OrdersCreateSubmitted from '@/views/Orders/OrdersCreateSubmitted'

export default {
  name: 'OrdersCreate',

  components: {
    OrdersCreateUpload,
    OrdersCreateSubmitted
  },

  props: {
    toggleMobileSidebar: {
      type: Function,
      required: true
    }
  },

  data: () => ({
    files: []
  }),

  methods: {
    deleteFile (file) {
      this.files = this.files.filter(f => f.name !== file.name)
    },

    deleteAll () {
      this.files = []
    },

    addFiles (newFiles) {
      const filtered = [...this.files, ...newFiles].filter(f => f.name.includes('.pdf'))
      const unique = []
      const uniqueNames = []

      filtered.forEach(fil => {
        if (uniqueNames.includes(fil.name)) return
        uniqueNames.push(fil.name)
        unique.push(fil)
      })

      this.files = unique
    }
  }
}
</script>

<style lang="scss" scoped>
.create {
  display: flex;
  flex-direction: column;
  width: 100%;
  padding: 5rem 1rem;
  overflow-x: hidden;

  @media screen and (min-width: map-get($breakpoints, med)) {
    min-width: 25%;
    max-width: 25%;
    box-shadow: map-get($properties, inset-shadow-left);
    border-left: 0.1rem solid map-get($colors, grey-2);
    padding: 5.2rem 1.6rem;
    padding-bottom: 3rem;
  }

  @media screen and (min-width: map-get($breakpoints, lg)) {
    padding: 5.2rem 3.6rem;
  }
}

.create__close {
  @media screen and (min-width: map-get($breakpoints, med)) {
    display: none;
  }
}

.create__header {
  display: flex;
  width: 100%;
  border-bottom: .2rem solid map-get($colors, grey-2);
}

.header__btn {
  margin: 0 auto;
  margin-bottom: 3.2rem;
}
</style>
