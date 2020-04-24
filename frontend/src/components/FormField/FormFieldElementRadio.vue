<template>
  <div class="form-field-element-radio">
    <v-radio-group
      v-model="radioValue"
      @change="changeRadio"
    >
      <div
        v-for="(option, key, index) in field.el.options"
        :key="key"
        class="radio__option"
      >
        <v-radio
          :label="String(key)"
          :value="index"
        />

        <div
          v-if="option.el && option.el.children"
          class="option__children"
        >
          <div
            v-for="(el, childKey) in option.el.children"
            :key="childKey"
            :style="{ width: el.el.width }"
            class="children__child"
          >
            <FormFieldElement
              :field="{...el, name: childKey}"
              @change="e => changeChildEl({ e, name: childKey })"
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
      const radioToOptionKey = Object.keys(this.field.el.options)
      const optionEl = this.field.el.options[radioToOptionKey[this.radioValue]].el
      const hasChildren = Boolean(optionEl && optionEl.children)
      const childrenDataToSend = hasChildren ? this.childrenData : radioToOptionKey[this.radioValue]

      this.$emit('change', childrenDataToSend)
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
