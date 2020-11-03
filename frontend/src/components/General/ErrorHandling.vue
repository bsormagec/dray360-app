<template>
  <div class="errorhandling_wrapper">
    <div
      v-if="label === 'error'"
    >
      <v-dialog
        v-model="localdialog"
        width="450px"
        @change="e => $emit('change', e)"
      >
        <div class="errormodal">
          <div class="title_modal">
            <h2 class="mx-3 my-3">
              <v-icon
                color="red darken-2"
                class="mr-2 mb-2"
              >
                mdi-close-circle
              </v-icon>{{ label }}
            </h2>
          </div>
          <div class="row body_modal">
            <div class="col-10">
              <p class="">
                {{ message }}
              </p>
            </div>
          </div>

          <div class="footer_modal">
            <v-btn
              class="btn primary_modal "
              @click="localdialog = false"
            >
              Cancel
            </v-btn>
            <v-btn
              class="btn btn_ok"
              @click="localdialog = false"
            >
              Ok
            </v-btn>
          </div>
        </div>
      </v-dialog>
    </div>
    <div
      v-else-if="label === 'alert'"
    >
      <v-dialog
        v-model="dialog2"
        width="400px"
      >
        <div class="errormodal">
          <div class="alert_modal">
            <h2 class="ml-3 mt-3">
              <v-icon
                color="red darken-2"
                class="mr-2 mb-2"
              >
                mdi-close-circle
              </v-icon>
            </h2>
            <div class="row body_modal">
              <div class="col-12">
                <p><strong>Error: </strong>{{ message }}</p>
              </div>
            </div>
          </div>
        </div>
        <template v-slot:activator="{ on }">
          <p>
            <v-btn
              class="btn_ok ml-0"
              v-on="on"
            >
              Alert
            </v-btn>
          </p>
        </template>
      </v-dialog>
    </div>
    <div
      v-else-if="label === 'snackbar'"
    >
      <div class="text-left snackbar">
        <v-snackbar
          v-model="snackbar"
          :top="true"
        >
          {{ message }}
          <v-btn
            color="white"
            text
            @click="close"
          >
            Close
          </v-btn>
        </v-snackbar>
      </div>
    </div>
  </div>
</template>
<script>

const callbacks = {
  startEdit: (obj) => {
    obj.field.highlight = 'edit'
  },
  stopEdit: (obj) => {
    obj.field.highlight = undefined
  },
  startHover: (obj) => {
    if (obj.field.highlight === 'edit') return
    obj.field.highlight = 'hover'
  },
  stopHover: (obj) => {
    if (obj.field.highlight === 'edit') return
    obj.field.highlight = undefined
  }
}

export default {

  props: {
    label: {
      type: String,
      required: true
    },
    type: {
      type: String,
      required: true
    },
    message: {
      type: String,
      required: true
    },
    dialog: {
      type: Boolean,
      required: true
    }
  },
  data () {
    return {
      localdialog: this.dialog,
      dialog2: this.dialog,
      snackbar: this.dialog
    }
  },
  updated () {
    // this.snackbar = this.dialog
  },
  methods: {
    close () {
      this.snackbar = !this.snackbar
    }
  }
}
</script>

<style lang="scss" >
.errormodal {
  background-color: map-get($colors, white);
  border: rem(1) solid map-get($colors, red );
}
.title_modal {
  display:inline-flex;
  border-bottom: rem(1) solid map-get($colors, grey-11 );
  width: 100%;
}
.alert_modal {
  display:inline-flex;
  border-bottom: rem(1) solid map-get($colors, grey-11 );
  width: 100%;
  color: map-get($colors, red );
  border-left: rem(5) solid map-get($colors, red );
  .body_modal p {
    color: map-get($colors, red );
    letter-spacing: rem(.5);
    line-height: (28 / 19);
    font-size: rem(19);
  }
}
.body_modal {
  margin: 0 !important;
}
.error_handling_formfield {
  margin-top: rem(50);
  width: rem(200);
  .form-field-presentation .form-field-highlight.edit.input {
    border-color: map-get($colors, red) !important;
    .action-btns {
      border-color: map-get($colors, red) !important;
      .btns__edit {
        .btns__accept {
          background: map-get($colors, red) !important;
        }
        .btns__close {
          i {
            color: map-get($colors, red) !important;
          }
        }
      }
    }
  }
}
.footer_modal {
  display: flex;
  justify-content: flex-end;
  align-content: center;
  padding: rem(15);
  .primary_modal{
    background-color: map-get($colors, white ) !important;
    border: 0;
    border-radius: rem(3);
    color: map-get($colors, red );
    text-transform: uppercase;
    box-shadow: none
  }
}
.btn_ok{
  background-color: map-get($colors,red ) !important;
  border: rem(2) solid map-get($colors, red );
  border-radius: rem(3);
  color:map-get($colors, white ) !important;
  text-transform: uppercase;
  margin: 0 rem(10);
  padding: 0 rem(30);
}
.snackbar{
  .v-snack__content{
    color: map-get($colors, white ) !important;
  }
}
</style>
