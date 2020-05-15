<template>
  <div class="form-viewing">
    <DetailsForm
      :readonly="true"
      id-suffix="viewing"
    />
  </div>
</template>

<script>
import DetailsForm from '@/views/Details/DetailsForm'

export default {
  name: 'DetailsFormViewing',

  components: {
    DetailsForm
  }
}
</script>

<style lang="scss" scoped>
.form-viewing {
  width: 100%;
}
</style>
