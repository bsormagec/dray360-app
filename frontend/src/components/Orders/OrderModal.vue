<template>
  <div>
    <v-dialog
      v-model="dialog"
      max-width="500px"
    >
      <v-stepper
        v-model="currentstep"
        :alt-labels="altLabels"
      >
        <template>
          <v-stepper-header>
            <template v-for="n in steps">
              <v-stepper-step
                :key="`${n}-step`"
                :step="n"
                :editable="editable"
              >
                Step {{ n }}
              </v-stepper-step>

              <v-divider
                v-if="n !== steps"
                :key="n"
              />
            </template>
          </v-stepper-header>

          <v-stepper-items>
            <v-stepper-content
              v-for="n in steps"
              :key="`${n}-content`"
              :step="n"
            >
              <div class="ordermodal">
                <div class="title_modal">
                  <h2 class="mx-3 my-3">
                    <v-icon
                      v-if="modaltype === 'AddressNotFound'"
                      color="red darken-2"
                    >
                      mdi-alert
                    </v-icon>
                    <v-icon
                      v-else
                      color="yellow darken-2"
                    >
                      mdi-alert
                    </v-icon>
                    <span
                      v-if="modaltype === 'AddressNotFound'"
                      class=""
                    >Address Not Found ({{ currentstep }} / {{ steps }})</span>
                    <span
                      v-else
                      class="verificationNeeded"
                    >Address Verification Needed ({{ currentstep }} / {{ steps }}) </span>
                  </h2>
                </div>
                <div
                  v-if="modaltype === 'VerificationNeded'"
                  class="col-12"
                >
                  <p>{{ message }}</p>
                </div>
                <div class="row body_modal">
                  <div
                    v-if="modaltype === 'VerificationNeded'"
                    class="col-6"
                  >
                    <h3 class="mb-1">
                      Address as Recognized
                    </h3>
                    <p>Walkins Mfg<br>1325 Hot Spring Way<br>Vista, CO, 92081</p>
                  </div>
                  <div
                    v-else
                    class="col-6 first-col"
                  >
                    <p>{{ message }}</p>
                  </div>
                  <div class="col-6">
                    <h3 class="mb-1">
                      Closest Match
                    </h3>
                    <p>Watkins Manufacturing Corp<br>1325 Hot Spring Way<br>Vista, CA, 92081</p>
                  </div>
                </div>
                <div class="footer_modal">
                  <div
                    v-if="modaltype === 'AddressNotFound' "
                    class="row"
                  >
                    <div class="col-12 d-block text-center">
                      <v-btn class="btn primary_modal">
                        Select Address
                      </v-btn>
                    </div>
                  </div>
                  <div
                    v-else
                    class="row"
                  >
                    <div class="col-6">
                      <v-btn class="btn primary_modal">
                        Select Different Address
                      </v-btn>
                    </div>
                    <div class="col-6">
                      <v-btn class="btn primary float-right">
                        This is correct
                      </v-btn>
                    </div>
                  </div>
                </div>
              </div>
            </v-stepper-content>
          </v-stepper-items>
        </template>
      </v-stepper>

      <template v-slot:activator="{ on }">
        <p>
          <a
            v-on="on"
          >
            <span
              v-if="modaltype === 'AddressNotFound'"
              class="addressnotfound"
            >Address Not Found
              <v-icon
                color="red darken-2"
                small
                right
              >mdi-alert</v-icon></span>
            <span
              v-else
              class="verificationNeeded"
            >Address Verification Needed<v-icon
              color="yellow darken-2"
              small
              right
            >mdi-alert</v-icon></span>
          </a>
        </p>
      </template>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: {
    modaltype: {
      type: String,
      required: true
    },
    currentstep: {
      type: Number,
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
      steps: 3,
      altLabels: false,
      editable: false
    }
  },
  methods: {
    onInput (val) {
      this.steps = parseInt(val)
    },
    nextStep (n) {
      if (n === this.steps) {
        this.step = 1
      } else {
        this.step = n + 1
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.wrapper{
    display: flex;
    align-content: center;
    justify-content: center;
    flex-direction: column;
    margin-top: rem(50);
    height: rem(270);
}
.v-stepper__content{
  padding: 0 !important;
}
.v-stepper__header{
  box-shadow: none !important;
}
    .title_modal{
            display:inline-flex;
            width: 100%;
            h2{
              display: flex;
            }
            span{
              font-size: rem(20) !important;
              margin-left: rem(5);
            }
            span.verificationNeeded{
              margin-left: rem(15);
            }
    }
    .body_modal{
        display: flex;
        align-items: center;
        padding: ren(15);
        width: rem(500);
        margin: 0 auto;
        border-bottom: rem(1) solid map-get($colors, grey-11 );
        .col-6:not(.first-col){
            border-left: rem(3) solid map-get($colors, grey-2 );
        }
    }
    .footer_modal{
        display: flex;
        justify-content: center;
        align-content: center;
        padding: rem(15);
        .primary_modal{
            background-color: map-get($colors, white ) !important;
            border: rem(2) solid var(--v-primary-base);
            border-radius: rem(3);
            color: var(--v-primary-base);
            text-transform: capitalize;
        }
    }
    span.addressnotfound{
      font-weight: 700;
      color: map-get($colors, red );
    }
    span.verificationNeeded{
      font-weight: 700;
      color: map-get($colors, yellow ) !important;
    }
</style>
