<template>
  <div class="form-field-element-radio">
    <v-radio-group v-model="radioValue">
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
    radioValue: 0
  })
}
</script>

<style lang="scss" scoped>
.radio__option {
  margin-bottom: 1rem;
}

.option__children {
  display: flex;
  justify-content: space-between;
  padding-left: 3.4rem;
}
</style>
