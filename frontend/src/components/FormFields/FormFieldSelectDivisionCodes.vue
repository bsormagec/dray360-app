<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="division_name"
      @accept="handleAccept"
      @accept-all="() => handleAccept(true)"
    >
      <div class="form-field__group">
        <div class="form-field__label">
          {{ label }}
        </div>
        <v-select
          :items="divisionCodes"
          item-text="division_name"
          item-value="division_code"
          :value="value"
          clearable
          outlined
          dense
          flat
          hide-details="true"
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
        this.handleAccept()
      }
    },
    handleAccept (saveAll = false) {
      this.$emit('change', {
        value: this.currentValue !== undefined ? this.currentValue : ' ',
        saveAll
      })
      const found = this.divisionCodes.find(element => element.division_code === this.currentValue)
      found !== undefined ? this.division_name = found.division_name : this.division_name = ''
    }
  }
}
</script>

<style lang="scss" scoped>
.v-select::v-deep {
  .v-input__slot {
    .v-input__append-inner {
      margin-top: 0 !important;
      padding-left: 0;
      .v-icon {
        font-size: rem(20);
      }
    }
    .v-select__selections input {
      display: none;
    }
    .v-select__selection {
      color: map-get($colors, grey-4);
    }
  }
}
</style>
