<template>
  <div class="form">
    <div
      v-for="section in form.sections"
      :key="section.title"
      class="form__section"
    >
      <h1
        :id="section.title"
        class="section__title"
        :style="{
          marginBottom: section.rootFields ? '2rem' : '1.6rem'
        }"
      >
        {{ section.title }}
      </h1>

      <div
        v-if="section.rootFields"
        class="section__rootfields"
      >
        <FormField
          v-for="field in section.rootFields"
          :key="field.name"
          :field="field"
        />
      </div>

      <div
        v-for="sub in section.subSections"
        :key="sub.name"
        class="section__sub"
      >
        <h2
          :id="sub.title"
          class="sub__title"
        >
          {{ sub.title }}
        </h2>

        <FormField
          v-for="field in sub.fields"
          :key="field.name"
          :field="field"
        />
      </div>
    </div>
  </div>
</template>

<script>
import FormField from '@/components/FormField/FormField'

export default {
  name: 'DetailsForm',

  components: {
    FormField
  },

  props: {
    form: {
      type: Object,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
.form {
  width: 45%;
  height: 100vh;
  overflow-y: auto;
  padding: 3.6rem 6.5rem;
  padding-top: 8.4rem;
}

.section__title {
  text-transform: uppercase;
  font-size: 1.7rem;
  line-height: 3rem;
  background: map-get($colors, grey-6);
  padding: 0 0.65rem;
  margin-bottom: 2rem;
  color: map-get($colors, grey-7);
}

.section__rootfields {
  margin-bottom: 3.6rem;
}

.section__field {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.1rem;
}

.field__name {
  font-size: 1.4rem !important;
  font-weight: bold;
  text-transform: capitalize;
}

.field__value {
  font-size: 1.44rem !important;
  text-transform: capitalize;
}

.section__sub {
  margin-bottom: 3.6rem;
}

.sub__title {
  font-size: 1.6rem;
  line-height: 3.6rem;
  color: map-get($colors, grey-4);
  border-bottom: 0.1rem solid map-get($colors, grey-3);
  margin-bottom: 1.4rem;
  text-transform: capitalize;
}
</style>
