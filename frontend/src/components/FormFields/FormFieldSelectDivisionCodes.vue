<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="division_name"
      @accept="handleAccept"
    >
      <div class="divisionCodeSection">
        <span>Division Name</span>
        <v-select
          :items="divisionCodes"
          item-text="division_name"
          item-value="division_code"
          :value="value"
          clearable
          outlined
          dense
          class="divisionSelect"
          @change="handleChange"
        />
      </div>
    </FormFieldPresentation>
  </div>
</template>

<script>
import FormFieldPresentation from './FormFieldPresentation'
import { getDivisionCodes } from '@/store/api_calls/orders'
export default {
  name: 'FormFieldSelectDivisionCodes',

  components: {
    FormFieldPresentation
  },

  props: {
    tCompanyId: { required: true, type: Number },
    tTmsProviderId: { required: true, type: Number },
    references: { type: String, default: null },
    label: { required: true, type: String },
    value: { required: true, default: '' },
    editMode: { required: true, type: Boolean }
  },
  data: (vm) => ({
    currentValue: vm.value,
    divisionCodes: [],
    division_name: '---'
  }),

  async beforeMount () {
    const [error, response] = await getDivisionCodes(this.tCompanyId, this.tTmsProviderId)
    if (!error) {
      this.divisionCodes = response.data
      const found = this.divisionCodes.find(element => element.division_code === this.value)
      found !== undefined ? this.division_name = found.division_name : this.division_name = '--'
    }
    this.loading = false
  },
 
  methods: {
    handleChange (e) {
      this.currentValue = e
      if (this.editMode && this.references) {
        this.$emit('change', this.currentValue !== undefined ? this.currentValue : ' ')
        const found = this.divisionCodes.find(element => element.division_code === this.currentValue)
        found !== undefined ? this.division_name = found.division_name : this.division_name = ''
      }
    },
    handleAccept () {
      this.$emit('change', this.currentValue !== undefined ? this.currentValue : ' ')
      const found = this.divisionCodes.find(element => element.division_code === this.currentValue)
      found !== undefined ? this.division_name = found.division_name : this.division_name = ''
    }
  }
}
</script>

<style lang="scss" scoped>
.divisionCodeSection{
  display: flex;
  justify-content: space-evenly;
  align-items: baseline;
  width: 100%;
  .divisionSelect, span{
    width: 25rem;
  }
  .check_button{
    border-radius: 0px;
    height: 40px !important;
    width: 40px !important;
    border: 1px solid rgba(0, 0, 0, 0.38);
    margin: 0 2px;

  }
  .close_button{
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
    height: 40px !important;
    width: 40px !important;
    border: 1px solid rgba(0, 0, 0, 0.38);
    border-top-left-radius: 0px;
    border-bottom-left-radius: 0px;
    .v-icon.notranslate.mdi.mdi-close.theme--light {
      color: red;
    }
  }
}
</style>
