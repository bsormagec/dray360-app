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
    }
  },

  data: () => ({
    isActive: false,
    childrenData: {}
  }),

  computed: {
    parsedIsActive () {
      return this.isActive === true ? 'yes' : 'no'
    }
  },

  mounted () {
    this.emitChange()
  },

  methods: {
    changeChildEl ({ e, name }) {
      this.$set(this.childrenData, name, e)
      this.emitChange()
    },
    emitChange () {
      const value = this.field.el.children && this.isActive ? this.childrenData : this.parsedIsActive
      this.$emit('change', value)
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
