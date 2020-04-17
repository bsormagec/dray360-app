/*
  TODO:
    - styles / markup*
    - rename because this is not a form but a type of select
    - add global addresses array as inner_utils in Details
    - use those addresses in exampleForm
    - convert field objects to fns (bit refactor to make it shorter and prettier)
*/

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

          <div class="card__item">
            <h4 class="item__title">
              Ladson
            </h4>

            <div class="item__left">
              <span class="left__contact-name">
                <span>Managed by: </span>
                <span>Seth Ling</span>
              </span>

              <span class="left__phone">
                <v-icon color="primary">mdi-phone</v-icon>
                (555) 555-555
              </span>

              <span class="left__email">
                <v-icon color="primary">mdi-email</v-icon>
                mail@mail.com
              </span>
            </div>

            <div class="item__center">
              <span class="center__address">
                <v-icon color="primary">mdi-map-marker</v-icon>
                3016 Loxley Lane Ladson, CA, 90210
              </span>
            </div>

            <div class="item__right">
              <v-btn
                color="primary"
                outlined
              >
                select
              </v-btn>
            </div>
          </div>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementModalForm',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    isOpen: false
  }),

  methods: {
    toggleModal () {
      this.isOpen = !this.isOpen
    }
  }
}
</script>

<style lang="scss" scoped>
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
  border-bottom: 0.1rem solid map-get($colors , grey-11);

  h3 {
    font-size: 1.8rem;
  }
}

.card__item {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  padding: 1.5rem 1.5rem 2.1rem 1.9rem;

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
