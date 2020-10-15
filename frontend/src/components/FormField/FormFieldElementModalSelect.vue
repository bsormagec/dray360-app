<template>
  <div class="form-field-element-modal-select">
    <v-dialog
      v-model="isOpen"
      :close-on-content-click="false"
      :nudge-right="40"
      transition="scale-transition"
      offset-y
      width="612px"
    >
      <template v-slot:activator="{ on }">
        <p
          class="modal-select__link"
          @click="toggleModal"
          v-on="on"
        >
          <strong>{{ field.el.placeholder }}</strong>: {{ field.name }}
        </p>
      </template>

      <v-card>
        <div class="modal-select__card">
          <div class="card__header">
            <h3>Addresses</h3>
            <v-btn
              icon
              @click="toggleModal"
            >
              <v-icon>
                mdi-close
              </v-icon>
            </v-btn>
          </div>

          <div
            v-for="(option, index) in field.el.options.preselected"
            :key="option.name"
            class="card__item"
          >
            <h4 class="item__title">
              {{ option['company name'] }}
            </h4>

            <div class="item__left">
              <span class="left__contact-name">
                <span>Managed by: </span>
                <span>{{ option['contact name'] }}</span>
              </span>

              <span class="left__phone">
                <v-icon color="primary">mdi-phone</v-icon>
                ({{ option.ext }}) {{ option.phone }}
              </span>

              <span class="left__email">
                <v-icon color="primary">mdi-email</v-icon>
                {{ option.email }}
              </span>
            </div>

            <div class="item__center">
              <span class="center__address">
                <v-icon color="primary">mdi-map-marker</v-icon>
                {{ option.address }}
              </span>
            </div>

            <div class="item__right">
              <v-btn
                color="primary"
                outlined
                @click="select(index)"
              >
                select
              </v-btn>
            </div>
          </div>
        </div>
      </v-card>
    </v-dialog>

    <div
      v-for="(el, key) in field.el.options.fields"
      :key="key"
    >
      <FormFieldElement
        :field="{...el, name: key, value:childrenData[key] }"
        :is-editing="isEditing"
        @change="e => changeChildEl({ e, name: key })"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementModalForm',

  components: {
    FormFieldElement: () => import('@/components/FormField/FormFieldElement')
  },

  props: {
    field: {
      type: Object,
      required: true
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    isOpen: false,
    childrenData: {}
  }),

  methods: {
    toggleModal () {
      this.isOpen = !this.isOpen
    },
    select (index) {
      this.childrenData = this.field.el.options.preselected[index]
      this.$emit('change', this.childrenData)
      this.toggleModal()
    },
    changeChildEl ({ e, name }) {
      this.$set(this.childrenData, name, e)
      this.$emit('change', this.childrenData)
    }
  }
}
</script>

<style lang="scss" scoped>
$border-bottom: rem(1) solid map-get($colors , grey-11);

.modal-select__link {
  cursor: pointer;
  font-size: rem(14.4) !important;
  text-decoration: underline;
  text-transform: capitalize;
  color: var(--v-primary-base);
  margin-bottom: rem(10);
}

.modal-select__card {
  display: flex;
  flex-direction: column;
}

.card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: rem(21) rem(20) rem(18) rem(17);
  border-bottom: $border-bottom;

  h3 {
    font-size: rem(18);
  }
}

.card__item {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  padding: rem(15) rem(15) rem(21) rem(19);
  border-bottom: $border-bottom;

  span {
    font-size: rem(14.4) !important;
  }
}

.item__left {
  display: flex;
  flex-direction: column;
}

.item__title {
  width: 100%;
  font-size: rem(14.4);
  margin-bottom: rem(5);
}

.left__contact-name {
  margin-bottom: rem(5);

  span {
    &:last-child {
      color: var(--v-primary-base);
    }
  }
}

.left__phone {
  display: flex;
  align-items: center;

  i {
    margin-right: rem(10);
  }
}

.left__email {
  i {
    margin-right: rem(10);
  }
}

.item__center {
  max-width: rem(205);
}

.center__address {
  display: flex;
  align-items: flex-start;

  i {
    margin-right: rem(10);
  }
}
</style>
