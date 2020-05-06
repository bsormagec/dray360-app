<template>
  <div>
    <span
      class="field__value"
      :style="getStyle()"
    >{{ field.value ? field.value : '--' }}</span>

    <FormFieldEditingSetByDocument
      :style="getStyle('field')"
      :field="field"
      @change="e => $emit('change', e)"
      @close="e => $emit('close')"
      @click.native="callbacks.startEdit({ fieldName: field.name })"
      @mouseover.native="callbacks.startHover({ fieldName: field.name })"
      @mouseleave.native="callbacks.stopHover({ fieldName: field.name })"
    />
  </div>
</template>

<script>
import FormFieldEditingSetByDocument from '@/components/FormField/FormFieldEditingSetByDocument'

export default {
  name: 'FormFieldPresentationValue',

  components: {
    FormFieldEditingSetByDocument
  },

  props: {
    field: {
      type: Object,
      required: true
    },
    callbacks: {
      type: Object,
      required: true
    }
  },

  methods: {
    getStyle (elName) {
      const positionStyle = {
        position: 'absolute',
        right: '0'
      }

      if (elName === 'field') {
        return {
          opacity: this.field.editing_set_by_document ? '1' : '0',
          ...positionStyle
        }
      } else {
        return {
          opacity: !this.field.editing_set_by_document ? '1' : '0',
          ...positionStyle
        }
      }
    }
  }
}
</script>
