<template>
  <div class="wrapper">
    <div class="container">
      <h1 class="text-center">
        Ordermaster frontend components / Style guide
      </h1>
      <div class="row">
        <div class="col-md-6 left-side">
          <h2>Form Field / Edit mode</h2><br>
          <code><p>This component receives an object with the following syntaxis:</p>
            object:{<br>
            name: 'name',<br>
            readonly: true,<br>
            callbacks,<br>
            el: {<br>
            type: 'input'<br>
            }<br><br>
            <p>You can use different types according your needs<br> examples below:</p></code>
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
          <p><SearchBar /></p>
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
          <h2>Date Range Calendar</h2><br>

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
          <AddressBookModal
            rawtext="test"
            :company-id="1"
            :tms-provider-id="1"
            @change="change"
          />
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
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import FormField from '@/components/FormField/FormField'
import SearchBar from '@/components/SearchBar'
import Select from '@/components/Select'
import ContentLoading from '@/components/ContentLoading'
import DateRangeCalendar from '@/components/Orders/DateRangeCalendar'
import AddressBookModal from '@/components/Orders/AddressBookModal'
import OrderModal from '@/components/Orders/OrderModal'
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
    DateRangeCalendar,
    AddressBookModal,
    OrderModal
  },
  props: {
  },
  data () {
    return {
      isEditing: true,
      loaded: false,
      items: ['a', 'b', 'c'],
      message: 'The recognition engine scanned the address below and did not find a matching address in your system. Please select the correct address:',
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
.wrapper{

  .left-side{
    padding: 3rem;
    border-right: 0.1rem solid map-get($colors, grey);
    code{
        padding: 2rem;
        margin-bottom: 2rem;
    }

  }
  .v-btn{
    padding: 0 !important;
  }
  .v-btn{
    padding: 0 !important;
  }
  .v-btn{
    padding: 0 !important;
  }
  .right-side{
    padding: 3rem;
    width: 30rem;
    display: flex;
    align-items: flex-start;
    height: 100%;
    flex-direction: column;
    .forminput_2{

        code{
            padding: 2rem;
            margin-bottom: 2rem;
        }
    }
  }
}
</style>
