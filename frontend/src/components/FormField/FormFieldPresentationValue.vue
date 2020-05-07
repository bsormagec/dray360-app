<template>
  <div>
    <span
      class="field__value"
      :style="getStyle()"
    >{{ field.value ? field.value : '--' }}</span>

    <FormFieldHighlight
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
import FormFieldHighlight from '@/components/FormField/FormFieldHighlight'

export default {
  name: 'FormFieldPresentationValue',

  components: {
    FormFieldHighlight
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
        top: '0',
        right: '0'
      }

      if (elName === 'field') {
        return {
          opacity: this.field.highlight ? '1' : '0',
          ...positionStyle
        }
      } else {
        return {
          opacity: !this.field.highlight ? '1' : '0',
          ...positionStyle
        }
      }
    }
  }
}
</script>
