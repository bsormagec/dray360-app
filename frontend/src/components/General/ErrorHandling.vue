<template>
  <div class="errorhandling_wrapper">
    <div
      v-if="label === 'error'"
    >
      <v-dialog
        v-model="dialog"
        width="40rem"
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
            <div class="col-12">
              <p>{{ message }}</p>
            </div>
          </div>

          <div class="footer_modal">
            <v-btn
              class="btn primary_modal "
              @click="dialog = false"
            >
              Cancel
            </v-btn>
            <v-btn
              class="btn btn_ok"
              @click="dialog = false"
            >
              Ok
            </v-btn>
          </div>
        </div>
        <template v-slot:activator="{ on }">
          <p>
            <v-btn
              class="btn_ok ml-0"
              v-on="on"
            >
              {{ label }}
            </v-btn>
          </p>
        </template>
      </v-dialog>
    </div>
    <div
      v-else-if="label === 'alert'"
    >
      <v-dialog
        v-model="dialog2"
        width="40rem"
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
    <div v-else>
      <div class="text-left snackbar">
        <v-btn
          dark
          @click="snackbar = true"
        >
          Snackbar
        </v-btn>
        <v-snackbar
          v-model="snackbar"
          :top="true"
        >
          {{ message }}
          <v-btn
            color="white"
            text
            @click="snackbar = false"
          >
            Close
          </v-btn>
        </v-snackbar>
      </div>
    </div>

    <div class="error_handling_formfield">
      <FormField
        :field="field"
        :is-editing="true"
        :readonly="field.readonly"
        :callbacks="field.callbacks"
        @close="field.callbacks.stopEdit({field:field})"
      />
    </div>
  </div>
</template>
<script>
import FormField from '../FormField/FormField'

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
  components: {
    FormField
  },
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
    }
  },
  data () {
    return {
      dialog: false,
      dialog2: false,
      snackbar: false,
      field:
        {
          name: 'Error formfield',
          readonly: true,
          highlight: undefined,
          callbacks,
          el: {
            type: 'input'
          }
        }
    }
  }
}
</script>

<style lang="scss" >
    .errormodal{
      background-color: map-get($colors, white);
      border: 0.1rem solid map-get($colors, red );
    }
    .title_modal{
            display:inline-flex;
            border-bottom:0.1rem solid map-get($colors, grey-11 );
            width: 100%;
    }
    .alert_modal{
            display:inline-flex;
            border-bottom:0.1rem solid map-get($colors, grey-11 );
            width: 100%;
            color: map-get($colors, red );
            border-left: 0.5rem solid map-get($colors, red );
            .body_modal p{
              color: map-get($colors, red );
              letter-spacing: 0.05rem;
              line-height: 2.8rem;
              font-size: 1.9rem;
            }
    }
    .body_modal{
        display: flex;
        align-items: center;
        margin: 0 auto;
    }
    .error_handling_formfield{
      margin-top: 5rem;
      width: 20rem;
      .form-field-presentation .form-field-highlight.edit.input{
          border-color: map-get($colors, red) !important;

      .action-btns{
        border-color: map-get($colors, red) !important;
        .btns__edit{
          .btns__accept{
            background: map-get($colors, red) !important;
          }
          .btns__close {
            i{
              color: map-get($colors, red) !important;

            }
          }
      }}
      }
    }
    .footer_modal{
        display: flex;
        justify-content: flex-end;
        align-content: center;
        padding: 1.5rem;
        .primary_modal{
            background-color: map-get($colors, white ) !important;
            border: 0;
            border-radius: 0.3rem;
            color:map-get($colors, red );
            text-transform: uppercase;
            box-shadow:none
        }

    }
    .btn_ok{
            background-color: map-get($colors,red ) !important;
            border: 0.2rem solid map-get($colors, red );
            border-radius: 0.3rem;
            color:map-get($colors, white ) !important;
            text-transform: uppercase;
            margin: 0 1rem;
            padding: 0 3rem;
        }
    .snackbar{
      .v-snack__content{
        color: map-get($colors, white ) !important;
      }
    }

</style>
