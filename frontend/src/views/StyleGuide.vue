<template>
  <v-main>
    <v-container fluid>
      <v-row no-gutters>
        <v-col md="6">
          <v-card
            outlined
            tile
          >
            <v-card-title class="headline">
              Typography
            </v-card-title>
            <v-card-text>
              <div class="display-1">
                Hierarchy
              </div>
            </v-card-text>
            <v-card-text>
              <h1>H1 / Roboto Light</h1>
              <p class="body-1">
                96 / -1.5 letter-spacing / 144 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <h2>H2 / Roboto Light</h2>
              <p class="body-1">
                60 / -.5 letter-spacing / 90 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <h3>H3 / Roboto Regular</h3>
              <p class="body-1">
                48 / 0 letter-spacing / 72 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <h4>H4 / Roboto Regular</h4>
              <p class="body-1">
                34 / .25 letter-spacing / 51 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <h5>H5/ Roboto Regular</h5>
              <p class="body-1">
                24 / 0 letter-spacing / 36 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <h6>H6 / Roboto Medium</h6>
              <p class="body-1">
                20 / .25 letter-spacing/ 30 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <div class="subtitle-1">
                Subtitle 1 / Roboto Regular
              </div>
              <p class="body-1">
                16 / .15 letter-spacing / 24 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <div class="subtitle-2">
                Subtitle 2 / Roboto Medium
              </div>
              <p class="body-1">
                14 / .1 letter-spacing / 21 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <p class="body-1">
                Body 1 / Roboto Regular
              </p>
              <p class="body-1">
                18 / .5 letter-spacing / 24 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <p class="body-2">
                Body 2 / Roboto Regular
              </p>
              <p class="body-1">
                14 / .25 letter-spacing / 21 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <a
                href="#"
                class="button"
              >Button / Roboto Medium</a>
              <p class="body-1">
                14 / 1.25 letter-spacing / 21 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <p class="caption">
                Caption / Roboto Regular
              </p>
              <p class="body-1">
                12 / .4 letter-spacing / 18 line-height
              </p>
            </v-card-text>
            <v-divider class="mx-4" />
            <v-card-text>
              <p class="overline">
                Overline / Roboto Medium
              </p>
              <p class="body-1">
                12 / 2 letter-spacing / 18 Line-height / all caps
              </p>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col md="6">
          <v-card
            tile
            outlined
          >
            <v-card-text>Example Combinations</v-card-text>
            <v-card-text>
              <div class="h1">
                Hello, World.
              </div>
              <div class="h2">
                An Adaptable Foundation
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <div
      v-show="false"
      class="wrapper"
    >
      <div class="container">
        <h1 class="text-center">
          Ordermaster frontend components / Style guide
        </h1>
        <div class="row">
          <div class="col-md-6 left-side">
            <h2>Form Field / Edit mode</h2><br>
            <code>
              <p>This component receives an object with the following syntaxis:</p>
              object:{<br>
              name: 'name',<br>
              readonly: true,<br>
              callbacks,<br>
              el: {<br>
              type: 'input'<br>
              }<br><br>
              <p>You can use different types according your needs<br> examples below:</p>
            </code>
            <div
              v-for="item in fields"
              :key="item.key"
            >
              <code>type: {{ item.el.type }}</code>
              <FormField
                :field="item"
                :is-editing="true"
                :readonly="item.readonly"
                :callbacks="{}"
              />
            </div>
            <h2>SearchBar / Edit mode</h2><br>
            <code>Just need to import the component and use it</code>
            <p>
              <SearchBar />
            </p>
            <h2>Select / Edit mode</h2><br>
            <code>The required props are : Label (String) and items (Array)</code>
            <p>
              <Select
                label="Select"
                :items="items"
              />
            </p>
            <h2>ContentLoading / Edit mode</h2><br>
            <code>Only need a required props that receive a boolean value</code>
            <p>
              <ContentLoading :loaded="loaded" />
            </p>

            <h2>Error Handling / Modal - Error </h2><br>
            <code>Receives two props: label and type </code>
            <ErrorHandling
              label="error"
              type="{modal}"
              :message="errorMesage"
            />
            <h2>Error Handling / Alert - Error </h2><br>
            <code>Receives two props: label and type </code>
            <ErrorHandling
              label="alert"
              type="{modal}"
              :message="alertMesage"
            />
            <h2>Error Handling / Snackbar </h2><br>
            <code>Receives two props: label and type </code>
            <ErrorHandling
              label="snackbar"
              type="{modal}"
              :message="snackbarmessage"
            >
              <h2> Date Range Calendar</h2><br>
              <p>
                <DateRangeCalendar />
              </p>
              <!--
            **************************
            ADDRESS BOOK MODAL SECTION HERE
            **************************
            -->
              <h2>Address Book Modal</h2><br>
              <code>Receive as props: rawtext (String), companyId (Number) and tmsProviderId (Number)</code>
              <!-- <AddressBookModal -->
              <!-- rawtext="test" -->
              <!-- :company-id="1" -->
              <!-- :tms-provider-id="1" -->
              <!-- @change="change" -->
              <!-- /> -->
              <h2>No Match Modal / Address not found</h2><br>
              <code>Props: *modaltype* = AddressNotFound <br> currentstep = "the position in the stepper component" <br></code>
              <div>
                <OrderModal
                  currentstep="1"
                  modaltype="AddressNotFound"
                  :message="message"
                />
              </div>
              <h2>Close Match Modal /  Address Verification Needed</h2><br>
              <code>Props: *modaltype* = VerificationNeded <br> currentstep = "the position in the stepper component" <br> message: the message that indicates the user about the closest match</code>
              <div>
                <OrderModal
                  currentstep="2"
                  modaltype="VerificationNeded"
                  :message="message"
                />
              </div>
            </errorhandling>
          </div>
          <div class="col-md-6 right-side">
            <h2>
              Form Field / Presentational mode
            </h2>
            <br>
            <div class="forminput_2">
              <br>
              <code>
                <p>This is the same component but with events like hover and edit, <br>the following code should be added in order to have <br>the events working:</p>
                const callbacks = {<br>
                startEdit: (obj) => {<br>
                obj.field.highlight = 'edit'<br>
                },<br>
                stopEdit: (obj) => {<br>
                obj.field.highlight = undefined<br>
                },<br>
                startHover: (obj) => {<br>
                if (obj.field.highlight === 'edit') return<br>
                obj.field.highlight = 'hover'<br>
                },<br>
                stopHover: (obj) => {<br>
                if (obj.field.highlight === 'edit') return<br>
                obj.field.highlight = undefined<br>
                }<br>
                }<br>
                <p>Then you should add this lines to the component</p>
                :callbacks="object.callbacks"<br>
                @close="object.callbacks.stopEdit({field:object})
              </code>
              <FormField
                :field="fields_2"
                :is-editing="true"
                :readonly="fields_2.readonly"
                :callbacks="fields_2.callbacks"
                @close="fields_2.callbacks.stopEdit({field:fields_2})"
              />
            </div>
            <div>
              <h2>
                Table Component
              </h2>
              <br>
              <div class="">
                <br>
                <code>
                  <p>This component has 3 parts: headers, body and footer. The props received <br>are the following: <br>
                  </p>
                  tableTitle = String. Table's name on header <br>
                  customheaders = String. Table's headers<br>
                  customItems = Array with table items<br>
                  hasSearch = Boolean. shows/hide the search input<br>
                  hasColumnFilters = Boolean. shows/hide the column filter<br>
                  hasBulkActions = Boolean. shows/hide the bulk actions <br>
                  hasBulkActions = Array. Bulk Options <br>
                  <br>hasActionButton = Object = {showButton: Boolean, action: 'String'}<br>shows/hide the button on header <br>
                  <br>hasAddButton = Object = {showButton: Boolean, action: 'String'}<br>shows/hide the button on each row

                </code>
                <GeneralTable
                  table-title="Title"
                  :customheaders="headers"
                  :custom-items="data"
                  :has-search="true"
                  :has-column-filters="true"
                  :has-bulk-actions="true"
                  :has-action-button="{showButton: true, action: '/'}"
                  :has-add-button="{showButton: true, action: '/'}"
                  :bulk-actions="['Reset Selected Users Passwords', 'Deactivate Selected Users', 'Delete Selected Users']"
                />
                <br>
                <p> <br> <code> The table footer depends on metada that comnes from API. thats why isn't showing here</code></p>
              </div>
            </div>
            <div>
              <p>
                <code>
                  <p>This button group is part of the new UX enhancements it receives the following properties:</p>
                  <pre>
  mainAction: {
    type: Object,
    required: true
  },
  options: {
    type: Array,
    required: true
  },
  floated: {
    type: Boolean
  }
                  </pre>
                </code>
              </p>
              <p>Inline not floated Button Group:</p>
              <OutlinedButtonGroup
                :main-action="{
                  title: 'View',
                  path: '#',
                  hasPermission: true
                }"
                :options="[
                  { title: 'View Details', action: change, hasPermission: true },
                  { title: 'Download PDF', action: change, hasPermission: false },
                  { title: 'Vew Order History', action: change }
                ]"
              />
              <p />
              <p>Floated Button Group:</p>
              <div class="box">
                <OutlinedButtonGroup
                  :main-action="{
                    title: 'Longer Action Title',
                    path: '#',
                    hasPermission: true
                  }"
                  :options="[
                    { title: 'View Details', action: change, hasPermission: true },
                    { title: 'Download PDF', action: change, hasPermission: false },
                    { title: 'Vew Order History', action: change }
                  ]"
                  floated
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-main>
</template>
<script>
import FormField from '@/components/FormField/FormField'
import SearchBar from '@/components/SearchBar'
import Select from '@/components/Select'
import ContentLoading from '@/components/ContentLoading'
import ErrorHandling from '@/components/General/ErrorHandling'
import DateRangeCalendar from '@/components/Orders/DateRangeCalendar'
// import AddressBookModal from '@/components/Orders/AddressBookModal'
import OrderModal from '@/components/Orders/OrderModal'
import GeneralTable from '@/components/General/GeneralTable'
import OutlinedButtonGroup from '@/components/General/OutlinedButtonGroup'
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
  name: 'Login',
  components: {
    FormField,
    SearchBar,
    Select,
    ContentLoading,
    ErrorHandling,
    DateRangeCalendar,
    // AddressBookModal,
    OrderModal,
    GeneralTable,
    OutlinedButtonGroup
  },
  props: {
  },
  data () {
    return {
      isEditing: true,
      loaded: false,
      items: ['a', 'b', 'c'],
      headers: [{ text: 'First', value: 'first' },
        { text: 'Last', value: 'last' },
        { text: 'Email', value: 'email' },
        { text: 'Actions', value: 'actions', sortable: false }],
      data: [{
        id: 0,
        first: 'Bill',
        last: 'Brasky',
        email: 'bbrasky@email.com'
      }],
      errorMesage: 'A dialog is a type of modal window that appears in front of app content to provide critical information or ask for a decision. Dialogs disable all app functionality when they appear, and remain on screen until confirmed, dismissed, or a required action has been taken.:',
      alertMesage: 'The alert component is used to convey important information to the user through the use contextual types icons and color.',
      snackbarmessage: 'snackbar message',
      message: 'The recognition engine scanned the address below and did not find a matching address in your system. Please select the correct address:',
      components: {
        name: 'Components',
        el: {
          type: 'radio',
          options: ['a', 'b', 'c']
        }
      },
      fields_2:
      {
        name: 'name',
        readonly: true,
        highlight: undefined,
        callbacks,
        el: {
          type: 'input'
        }
      },
      fields: [
        {
          name: 'name',
          readonly: false,
          el: {
            type: 'input'
          }
        },
        {
          name: 'choose...',
          el: {
            type: 'select',
            options: ['a', 'b', 'c']
          }
        },
        {
          name: 'Input - Select',
          el: {
            type: 'input-select',
            options: ['a', 'b', 'c']
          }
        },
        {
          name: 'Switch',
          el: {
            type: 'switch'
          }
        },
        {
          name: 'Date',
          el: {
            type: 'date'
          }
        },
        {
          name: 'Time',
          el: {
            type: 'time'
          }
        },
        {
          name: 'Date - Time',
          el: {
            type: 'date-time'
          }
        },
        {
          name: 'Textarea',
          el: {
            type: 'text-area'
          }
        },
        {
          name: 'Radio',
          el: {
            type: 'radio',
            options: ['a', 'b', 'c']
          }
        },
        {
          name: 'Label',
          el: {
            type: 'label'
          }
        },
        {
          name: 'Info title',
          el: {
            type: 'info-title'
          }
        }
      ]
    }
  },

  methods: {
    change (e) {
      console.log(e)
    }
  }
}
</script>
<style lang="scss" scoped>
.wrapper {
  .left-side {
    padding: rem(30);
    border-right: rem(1) solid map-get($colors, grey);
  }
  .v-btn {
    padding: 0 !important;
  }
  .right-side {
    padding: rem(30);
    width: rem(300);
    display: flex;
    align-items: flex-start;
    height: 100%;
    flex-direction: column;
  }
  code {
    padding: rem(20);
    margin-bottom: rem(20);
  }
}
.box {
  box-sizing: border-box;
  position: relative;
  padding: rem(20);
  display: block;
  width: 100%;
  height: rem(420);
  border: rem(1px) solid #CCCCCC;
}
.box::v-deep .split-btn.split-btn--floated {
  position: absolute !important;
}
</style>
