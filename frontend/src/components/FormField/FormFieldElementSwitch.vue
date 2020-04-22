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
      v-if="field.el.children && isActive"
      class="switch__children"
    >
      <div
        v-for="el in field.el.children"
        :key="el.name"
        class="children__child"
      >
        <FormFieldElement
          :field="el"
          @change="e => changeChildEl({ e, name: el.name })"
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
    }
  },

  data: () => ({
    isActive: false,
    childrenData: {}
  }),

  methods: {
    changeChildEl ({ e, name }) {
      this.childrenData[name] = e
      this.emitChange()
    },
    emitChange () {
      const value = this.field.el.children ? this.childrenData : this.isActive

      this.$emit('change', {
        name: this.field.name,
        value: Object.keys(value) ? { active: this.isActive, ...value } : value
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-switch.no-children {
  height: 6.6rem;
}

.switch__children {
  display: flex;
  flex-direction: column;
  padding-left: 3.4rem;
}
</style>
