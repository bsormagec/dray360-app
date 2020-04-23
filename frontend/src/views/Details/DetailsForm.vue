<template>
  <div class="form>">
    <div
      v-for="(sectionVal, sectionKey) in form.sections"
      :key="sectionKey"
      class="form__section"
    >
      <h1
        :id="`${cleanStrForId(sectionKey)}-${idSuffix}`"
        class="section__title"
        :style="{
          marginBottom: sectionVal.rootFields ? '2rem' : '1.6rem'
        }"
      >
        {{ sectionKey }}
      </h1>

      <div
        v-if="sectionVal.rootFields"
        class="section__rootfields"
      >
        <FormField
          v-for="(fieldVal, fieldKey) in sectionVal.rootFields"
          :key="fieldKey"
          :field="{...fieldVal, name: fieldKey}"
          :readonly="readonly"
          @change="(value) => updateField({
            value,
            location: `${sectionKey}/rootFields/${fieldKey}`
          })"
        />
      </div>

      <div
        v-for="(subVal, subKey) in sectionVal.subSections"
        :key="subKey"
        class="section__sub"
      >
        <h2
          :id="`${cleanStrForId(subKey)}-${idSuffix}`"
          class="sub__title"
        >
          {{ subKey }}
        </h2>

        <FormField
          v-for="(subFieldVal, subFieldKey) in subVal.fields"
          :key="subFieldKey"
          :field="{ ...subFieldVal, name: subFieldKey }"
          :readonly="readonly"
          @change="(value) => updateField({
            value,
            location: `${sectionKey}/subSections/${subKey}/fields/${subFieldKey}`
          })"
        />
      </div>
    </div>
  </div>
</template>

<script>
import FormField from '@/components/FormField/FormField'
import { providerStateName, providerMethodsName } from '@/views/Details/inner_types'
import { cleanStrForId } from '@/views/Details/inner_utils/clean_str_for_id'

export default {
  name: 'DetailsForm',

  inject: [providerStateName, providerMethodsName],

  components: {
    FormField
  },

  props: {
    readonly: {
      type: Boolean,
      required: true
    },
    idSuffix: {
      type: String,
      required: true
    }
  },

  computed: {
    form () {
      return this[providerStateName].form()
    }
  },

  methods: {
    cleanStrForId,

    setFormToModify (updatedForm) {
      this[providerMethodsName].setFormToModify(updatedForm)
    },

    updateField ({ value, location }) {
      const formModified = this.form
      const parts = location.split('/')

      if (location.includes('rootFields')) {
        formModified.sections[parts[0]][parts[1]][parts[2]].value = value
      } else if (location.includes('subSections')) {
        formModified.sections[parts[0]][parts[1]][parts[2]][parts[3]][parts[4]].value = value
      }

      this.setFormToModify(formModified)
    }
  }
}
</script>

<style lang="scss" scoped>
.form {
  width: 100%;
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
