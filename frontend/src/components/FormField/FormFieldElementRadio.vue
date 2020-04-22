<template>
  <div class="form-field-element-radio">
    <v-radio-group
      v-model="radioValue"
      @change="changeRadio"
    >
      <div
        v-for="(option, index) in field.el.options"
        :key="option.name"
        class="radio__option"
      >
        <v-radio
          :label="option.name"
          :value="index"
        />

        <div
          v-if="option.children"
          class="option__children"
        >
          <div
            v-for="el in option.children"
            :key="el.name"
            :style="{ width: el.el.width }"
            class="children__child"
          >
            <FormFieldElement
              :field="el"
              @change="e => changeChildEl({ e, name: el.name })"
            />
          </div>
        </div>
      </div>
    </v-radio-group>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementRadio',

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
    radioValue: 0,
    childrenData: {}
  }),

  methods: {
    changeRadio (e) {
      this.emitChange()
    },
    changeChildEl ({ e, name }) {
      this.childrenData[name] = e
      this.emitChange()
    },
    emitChange () {
      const childrenDataToSend = this.field.el.options[this.radioValue].children ? this.childrenData : {}

      this.$emit('change', {
        name: this.field.name,
        value: {
          name: this.field.el.options[this.radioValue].name,
          ...childrenDataToSend
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.radio__option {
  margin-bottom: 1rem;
}

.option__children {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding-left: 3.4rem;
}
</style>
