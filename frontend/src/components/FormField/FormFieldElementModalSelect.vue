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
        :field="{...el, name: key}"
        :value="childrenData[key]"
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
$border-bottom: 0.1rem solid map-get($colors , grey-11);

.modal-select__link {
  cursor: pointer;
  font-size: 1.44rem !important;
  text-decoration: underline;
  text-transform: capitalize;
  color: map-get($colors , blue);
  margin-bottom: 1rem;
}

.modal-select__card {
  display: flex;
  flex-direction: column;
}

.card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 2.1rem 2rem 1.8rem 1.7rem;
  border-bottom: $border-bottom;

  h3 {
    font-size: 1.8rem;
  }
}

.card__item {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  padding: 1.5rem 1.5rem 2.1rem 1.9rem;
  border-bottom: $border-bottom;

  span {
    font-size: 1.44rem !important;
  }
}

.item__left {
  display: flex;
  flex-direction: column;
}

.item__title {
  width: 100%;
  font-size: 1.44rem;
  margin-bottom: 0.5rem;
}

.left__contact-name {
  margin-bottom: 0.5rem;

  span {
    &:last-child {
      color: map-get($colors , blue);
    }
  }
}

.left__phone {
  display: flex;
  align-items: center;

  i {
    margin-right: 1rem;
  }
}

.left__email {
  i {
    margin-right: 1rem;
  }
}

.item__center {
  max-width: 20.5rem;
}

.center__address {
  display: flex;
  align-items: flex-start;

  i {
    margin-right: 1rem;
  }
}
</style>
