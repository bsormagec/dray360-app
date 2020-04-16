<template>
  <div class="form-field-element-modal-form">
    <p>
      <a
        href="#"
      >{{ field.name }}</a>
    </p>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementModalForm',

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>
