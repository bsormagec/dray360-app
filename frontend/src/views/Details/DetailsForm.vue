<template>
  <div class="form">
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
          @change="(value) => setFormFieldValue({
            value,
            location: `${sectionKey}/rootFields/${fieldKey}`
          })"
          @close="stopEdit({ fieldName: fieldKey })"
        />
      </div>

      <div
        v-for="(subVal, subKey, subIndex) in sectionVal.subSections"
        :key="subKey"
        class="section__sub"
      >
        <div
          :id="`${cleanStrForId(subKey)}-${idSuffix}`"
          class="sub__title"
        >
          <h2>{{ sectionKey === 'inventory' ? `Item ${subIndex + 1}` : subKey }}</h2>

          <v-btn
            v-if="isEditing && hasInventoryAction({ sectionKey, sectionVal })"
            icon
            @click="deleteFormInventoryItem({ key: subKey })"
          >
            <v-icon color="red">
              mdi-delete
            </v-icon>
          </v-btn>
        </div>

        <FormField
          v-for="(subFieldVal, subFieldKey) in subVal.fields"
          :key="subFieldKey"
          :field="{ ...subFieldVal, name: subFieldKey }"
          :readonly="readonly"
          @change="(value) => setFormFieldValue({
            value,
            location: `${sectionKey}/subSections/${subKey}/fields/${subFieldKey}`
          })"
          @close="stopEdit({ fieldName: subFieldKey })"
        />
      </div>

      <div v-if="hasInventoryAction({ sectionKey, sectionVal })">
        <DetailsFormAddInventoryItem />
      </div>
    </div>
  </div>
</template>

<script>
import FormField from '@/components/FormField/FormField'
import DetailsFormAddInventoryItem from '@/views/Details/DetailsFormAddInventoryItem'
import { formModule, documentModule } from '@/views/Details/inner_store/index'
import { cleanStrForId } from '@/views/Details/inner_utils/clean_str_for_id'

export default {
  name: 'DetailsForm',

  components: {
    FormField,
    DetailsFormAddInventoryItem
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
    isEditing () {
      return formModule.state.isEditing
    },

    form () {
      return formModule.state.form
    }
  },

  methods: {
    cleanStrForId,
    stopEdit: documentModule.methods.stopEdit,
    setFormFieldValue: formModule.methods.setFormFieldValue,
    deleteFormInventoryItem: formModule.methods.deleteFormInventoryItem,

    hasInventoryAction ({ sectionKey, sectionVal }) {
      return sectionKey === 'inventory' && sectionVal.actionSection
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
  display: flex;
  align-items: center;
  justify-content: space-between;

  h2 {
    width: 100%;
    font-size: 1.6rem;
    line-height: 3.6rem;
    color: map-get($colors, grey-4);
    border-bottom: 0.1rem solid map-get($colors, grey-3);
    margin-bottom: 1.4rem;
    text-transform: capitalize;
  }

  button {
    margin-left: auto;
  }
}
</style>
