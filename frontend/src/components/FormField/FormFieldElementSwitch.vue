<template>
  <div
    :class="{
      'form-field-element-switch': true,
      'no-children': !field.el.children
    }"
  >
    <v-switch
      v-model="isActive"
      :label="field.name"
      @change="emitChange"
    />

    <div
      v-show="field.el.children && isActive"
      class="switch__children"
    >
      <div
        v-for="(el, key) in field.el.children"
        :key="key"
        class="children__child"
      >
        <FormFieldElement
          :field="{ ...el, name: key }"
          :is-editing="isEditing"
          @change="e => changeChildEl({ e, name: key })"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementSwitch',

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
    isActive: false,
    childrenData: {}
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

  mounted () {
    this.initialize()
    this.emitChange()
  },

  methods: {
    initialize () {
      if (this.field.value === '--') {
        this.isActive = false
      } else if (typeof this.field.value === 'string') {
        this.isActive = this.field.value === 'yes'
      } else if (typeof this.field.value === 'boolean') {
        this.isActive = this.field.value
      }
    },

    changeChildEl ({ e, name }) {
      this.$set(this.childrenData, name, e)
      this.emitChange()
    },

    emitChange () {
      const value = this.field.el.children && this.isActive ? this.childrenData : this.isActive
      this.$emit('change', value)
    },

    syncValue (val) {
      if (typeof this.field.value === 'object') {
        this.childrenData = this.field.value
        this.isActive = true
        for (const key in this.childrenData) {
          this.changeChildEl({ e: this.childrenData[key], name: key })
        }
        return
      }

      this.isActive = this.field.value
      this.emitChange()
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-switch.no-children {
  height: rem(66);
}

.switch__children {
  display: flex;
  flex-direction: column;
  padding-left: rem(34);
}
</style>
