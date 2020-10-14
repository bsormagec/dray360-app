<template>
  <div class="form-field-element-radio">
    <v-radio-group
      v-model="radioValue"
      @change="(v) => emitChange(nToRadioKey(v))"
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
              :is-editing="isEditing"
              @change="v => changeChildEl({ v, name: childKey, optionName: key })"
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
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    radioValue: 0,
    childrenData: {}
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

  mounted () {
    this.emitChange(Object.keys(this.field.el.options)[0])
  },

  methods: {
    nToRadioKey (n) {
      return Object.keys(this.field.el.options)[n]
    },

    keyToRadioN (key) {
      return Object.keys(this.field.el.options).findIndex(k => k === key)
    },

    emitChange (optionName) {
      const option = this.field.el.options[optionName]
      const hasChildren = option.el && option.el.children
      const dataToEmit = hasChildren ? { ...this.childrenData, optionName } : { optionName }
      this.radioValue = this.keyToRadioN(optionName)
      this.$emit('change', dataToEmit)
    },

    changeChildEl ({ v, name, optionName }) {
      this.childrenData[name] = v
      this.emitChange(optionName)
    },

    syncValue () {
      if (!this.field.optionName) return

      const hasChildren = this.field.el.options[this.field.optionName].el &&
       typeof this.field.value === 'object'

      if (hasChildren) {
        this.childrenData = this.field.value

        for (const key in this.childrenData) {
          this.changeChildEl({
            v: this.childrenData[key],
            name: key,
            optionName: this.field.optionName
          })
        }

        return
      }

      this.emitChange(this.field.optionName)
    }
  }
}
</script>

<style lang="scss" scoped>
.radio__option {
  margin-bottom: rem(10);
}

.option__children {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding-left: rem(34);
}
</style>
