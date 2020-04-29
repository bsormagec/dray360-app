<template>
  <div class="form-editing">
    <DetailsForm
      :readonly="false"
      id-suffix="editing"
    />
  </div>
</template>

<script>
import DetailsForm from '@/views/Details/DetailsForm'

export default {
  name: 'DetailsFormEditing',

  components: {
    DetailsForm
  }
}
</script>

<style lang="scss" scoped>
.form-editing {
  width: 45%;
  min-width: 45%;
}
</style>
