<template>
  <div class="form-field-presentation">
    <div
      v-if="!editMode"
      :class="{
        'form-field-highlight': true,
        hover: isHovering,
        edit: isEditing
      }"
      @mouseover="isMobile || isEditing ? () => {} : startHover({ path: references })"
      @mouseleave="isMobile || isEditing ? () => {} : stopHover({ path: references })"
      @click="startFieldEdit({ path: references })"
    >
      <div
        v-show="!isEditing"
        class="field__group"
      >
        <span class="field__name">{{ label }}</span>
        <span
          class="field__value"
        >{{ value === null || value === '' ? '--' : value }}</span>
      </div>
      <div
        :class="{
          'highlight__edit': true,
          hover: isHovering,
          edit: isEditing
        }"
      >
        <slot v-if="isEditing" />
        <FormFieldHighlightBtns
          v-show="isEditing || highlight.hover"
          :edit-mode="isEditing"
          @accept="handleAccept"
          @cancel="handleCancel"
        />
      </div>
    </div>
    <slot
      v-if="editMode"
    />
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import { mapState, mapActions } from 'vuex'
import orderForm, { types } from '@/store/modules/order-form'
import get from 'lodash/get'

import FormFieldHighlightBtns from './FormFieldHighlightBtns'

export default {
  name: 'FormFieldPresentation',

  components: { FormFieldHighlightBtns },

  mixins: [isMobile],

  props: {
    editMode: { type: Boolean, required: true },
    references: { type: String, required: true },
    label: { type: String, required: true },
    value: { required: true, default: '' }
  },

  computed: {
    ...mapState(orderForm.moduleName, {
      allHighlights: state => state.highlights
    }),
    highlight () {
      if (this.references === null || this.references === undefined) {
        return null
      }

      return this.allHighlights[this.references]
    },
    isHovering () {
      return !this.isEditing && this.highlight.hover
    },
    isEditing () {
      return this.editMode || get(this.highlight, 'edit', false)
    }
  },

  methods: {
    ...mapActions(orderForm.moduleName, {
      startHover: types.startHover,
      stopHover: types.stopHover,
      startFieldEdit: types.startFieldEdit,
      stopFieldEdit: types.stopFieldEdit
    }),
    handleAccept () {
      this.stopFieldEdit({ path: this.references })
      this.$emit('accept')
    },
    handleCancel () {
      this.stopFieldEdit({ path: this.references })
      this.$emit('cancel')
    }
  }
}
</script>

<style lang="scss">
.form-field-presentation {
  .field__group {
    display: flex;
    width: 100%;
    height: 100%;
    justify-content: space-between;
    align-items: center;
  }

  .field__name,
  .field__children .field__name {
    font-size: 1.4rem !important;
    font-weight: bold;
    text-transform: capitalize;
  }

  .field__name {
    width: 60%;
  }

  .field__value {
    cursor: pointer;
    text-align: right;
    word-break: break-word;
    width: 40%;
    transition: opacity 200ms ease-in-out;
  }

  .field__value,
  .field__children .field__value {
    font-size: 1.44rem !important;
    text-transform: capitalize;
  }

  .field__children {
    display: flex;
    justify-content: space-between;
    padding-left: 1rem;
  }

  .form-field-highlight {
    position: relative;
    cursor: pointer;
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 3rem;
    margin-bottom: 1.1rem;
    border: 0.1rem solid;
    border-color: map-get($colors, white);
    border-radius: 0.2rem;
    transition: all 200ms ease-in-out;

    &.hover, &.edit {
      border-color: var(--v-primary-base);
    }

    &.hover {
      background: rgba(var(--v-primary-base-rgb), 0.15);
      padding-left: 1rem;
      padding-right: 3rem;
    }

    &.edit {
      // min-height: 10rem;
      // &.input, &.text-area {
        min-height: unset;
      // }
    }
  }

  .highlight__edit {
    &.edit {
      width: 100%;
      display: flex;
      align-items: center;
      // padding: 1rem 3rem 0rem 1rem;

      // &.input, &.text-area {
        padding: unset;
      // }
    }
  }
}
</style>
