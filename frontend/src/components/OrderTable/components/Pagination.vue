<template>
  <footer
    :class="['table-pagination', 'py-1', loading ? 'loading' : '']"
  >
    <v-container
      fluid
      px-0
    >
      <v-row no-gutters>
        <v-col cols="2">
          <p class="pagination-info">
            {{ from || 0 }} &mdash; {{ to || 0 }} of {{ total }}
          </p>
        </v-col>
        <v-col
          cols="10"
          class="d-flex align-center justify-end"
        >
          <v-form
            class="page-jump d-flex align-center"
            @submit.prevent="goToPage(pageIndexTarget)"
          >
            <label>Jump to Page</label>
            <v-text-field
              v-model="pageIndexTarget"
              outlined
              height="30"
              color="primary"
              type="number"
              placeholder="#"
              class="page-number"
              hide-details
            />
          </v-form>

          <div class="page-links">
            <v-btn
              class="pagination-btn"
              outlined
              height="30"
              color="primary"
              @click="goToFirstPage"
            >
              First
            </v-btn>
            <div class="page-indexes">
              <v-btn
                v-for="page in pageIndexes"
                :key="page"
                height="30"
                class="page-index-btn"
                :outlined="page !== pageData.current_page"
                color="primary"
                @click="goToPage(page)"
              >
                {{ page }}
              </v-btn>
            </div>
            <v-btn
              class="pagination-btn"
              outlined
              height="30"
              color="primary"
              @click="goToLastPage"
            >
              Last
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-container>
    </v-container-fluid>
  </footer>
</template>
<script>

export default {
  name: 'TablePagination',
  components: {
  },
  props: {
    pageData: {
      type: Object,
      required: false
    },
    links: {
      type: Object,
      required: false
    },
    loading: {
      type: Boolean,
      required: false
    }
  },
  data () {
    return {
      linkLimit: 3,
      pageIndexTarget: null
    }
  },
  computed: {
    pageIndexes () {
      if (this.pageData) {
        const current = this.pageData.current_page
        const linkTotal = Math.min(this.linkLimit, this.pageData.last_page)
        const indexes = []
        const buffer = Math.floor(this.linkLimit / 2)

        let index = Math.max(current - buffer, 1)

        if (current === this.pageData.last_page) {
          index = Math.max(this.pageData.last_page - (linkTotal - 1), 1)
        }

        for (let i = 0; i < linkTotal; i++) {
          indexes.push(index++)
        }
        return indexes
      }
      return []
    },
    total () {
      return this.pageData ? this.pageData.total : 0
    },
    from () {
      return this.pageData ? this.pageData.from : 0
    },
    to () {
      return this.pageData ? this.pageData.to : 0
    }
  },

  created () {

  },

  methods: {
    goToFirstPage () {
      this.goToPage(1)
    },
    goToLastPage () {
      this.goToPage(this.pageData.last_page)
    },
    goToPage (pageIndex) {
      // only emit event if the target page index is different from current_page
      if (this.pageData.current_page !== pageIndex && pageIndex <= this.pageData.last_page && pageIndex > 0) {
        this.$emit('pageIndexChange', pageIndex)
        // reset this so the field doesn't show the current page number
        this.pageIndexTarget = null
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .loading {
    opacity: 0.5;
    transition: opacity 0.3s linear;
  }
  .page-links {
    display: flex;
  }
    .pagination-btn {
        margin: 0 rem(10);
        &:last-child {
          margin-right: 0;
        }
    }
    .page-index-btn {
      border-radius: 0 !important;
      padding: 0 !important;
      min-width: rem(50) !important;
      &:first-child {
        border-top-left-radius: 4px !important;
        border-bottom-left-radius: 4px !important;
        border-right: none;
      }
      &:last-child {
        border-top-right-radius: 4px !important;
        border-bottom-right-radius: 4px !important;
        border-left: none;
      }
    }

    .pagination-btn::v-deep .v-btn__content, .page-index-btn::v-deep .v-btn__content {
      font-size: rem(10);

    }
    .pagination-info {
      font-size: rem(10);
      font-weight: 500;
      letter-spacing: rem(1);
    }
    .page-jump {
        label {
            margin-right:rem(10);
            font-size: rem(10);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: rem(1);
        }
        margin-right: rem(10);
        .page-number {
            max-width: rem(60);
        }
        .page-number::v-deep .v-input__slot {
            min-height: auto;
        }
    }
</style>
