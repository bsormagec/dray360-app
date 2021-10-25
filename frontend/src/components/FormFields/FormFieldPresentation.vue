<template>
  <div
    :id="`${cleanStrForId(references)}-formfield`"
    :class="{'form-field-presentation': true, 'has-notes': !!adminNotes}"
  >
    <div
      v-if="!editMode && !managedByTemplate"
      :class="{
        'form-field-highlight': true,
        hover: isHovering && !onlyHover,
        'hover-paddingless': isHovering && onlyHover,
        'field-updated': !isEditing && hasPrecedingOrder,
        edit: isEditing,
      }"
      tabindex="0"
      @mouseover="isMobile || isEditing ? () => {} : startHover({ path: references })"
      @mouseleave="isMobile || isEditing ? () => {} : stopHover({ path: references })"
      @click="handleStartEdit"
      @keypress.enter.prevent="handleStartEdit"
    >
      <div
        v-show="!isEditing && !onlyHover"
        class="form-field__group"
      >
        <span class="form-field__label">
          {{ label }}

          <TooltipIcon
            v-if="hasPrecedingOrder"
            icon="mdi-history"
            :text="`Order #${ order.preceded_by_order_id }`"
            :custom-icon-attrs="{ small: false, color: 'orange-changes', class: 'ml-1' }"
          />
        </span>
        <span
          v-if="isLoading"
          class="field__value"
        >
          <v-progress-circular
            :size="20"
            indeterminate
            color="primary"
          />
        </span>
        <span
          v-else
          class="field__value"
        >
          {{ value === null || value === '' ? '--' : value }}
        </span>
      </div>
      <div
        :class="{
          'highlight__edit': true,
          'only-hover': onlyHover,
          hover: isHovering,
          edit: isEditing,
        }"
      >
        <slot v-if="isEditing || onlyHover" />
        <FormFieldHighlightBtns
          v-show="(isEditing || highlight.hover) && !onlyHover"
          :edit-mode="isEditing"
          :save-for-all="saveForAll"
          :locked="isLocked"
          :readonly="readonly"
          @accept="handleAccept"
          @accept-all="() => handleAccept(true)"
          @cancel="handleCancel"
        />
      </div>
      <TooltipIcon
        v-if="!!adminNotes"
        :text="adminNotes"
        :custom-icon-attrs="{ small: false, color: 'grey-darken4', class: 'mr-1' }"
      />
    </div>
    <FormFieldManaged
      v-else-if="managedByTemplate"
      :references="references"
      :label="label"
    />
    <div
      v-else-if="editMode"
      class="slot-container d-flex align-center"
    >
      <slot />
      <TooltipIcon
        v-if="!!adminNotes"
        :text="adminNotes"
        :custom-icon-attrs="{ small: false, color: 'grey-darken4', class: 'mr-1' }"
      />
    </div>
    <v-alert
      :value="errors.length > 0"
      dense
      text
      dismissible
      type="error"
      @input="dismissErrors"
    >
      <span
        v-for="(error, index) in errors"
        :key="index"
      >
        {{ error }}
      </span>
    </v-alert>
  </div>
</template>

<script>
import FormFieldHighlightBtns from './FormFieldHighlightBtns'
import FormFieldManaged from './FormFieldManaged'
import TooltipIcon from '@/components/General/TooltipIcon'

import isMobile from '@/mixins/is_mobile'
import { mapState, mapActions, mapGetters } from 'vuex'
import permissions from '@/mixins/permissions'
import orderForm, { actionTypes } from '@/store/modules/order-form'
import get from 'lodash/get'
import { cleanStrForId } from '@/utils/clean_str_for_id.js'

export default {
  name: 'FormFieldPresentation',

  components: {
    FormFieldHighlightBtns,
    FormFieldManaged,
    TooltipIcon,
  },

  mixins: [isMobile, permissions],

  props: {
    editMode: { type: Boolean, required: true },
    references: { type: String, required: true },
    label: { type: String, required: true },
    value: { required: true, default: '' },
    onlyHover: { type: Boolean, required: false, default: false },
    readonly: { type: Boolean, required: false, default: false },
    managedByTemplate: { type: Boolean, required: false, default: false },
    adminNotes: { type: String, required: false, default: '' },
  },

  computed: {
    ...mapGetters(orderForm.moduleName, ['isMultiOrderRequest', 'isLocked']),

    ...mapState(orderForm.moduleName, {
      allHighlights: state => state.highlights,
      order: state => state.order,
    }),

    saveForAll () {
      const blackListedParams = ['unit_number', 'seal_number']

      return this.isMultiOrderRequest &&
        this.hasPermission('all-orders-edit') &&
        !blackListedParams.includes(this.references)
    },

    highlight () {
      if (this.references === null || this.references === undefined) {
        return null
      }

      return this.allHighlights[this.references]
    },

    errors () {
      if (!this.highlight) {
        return []
      }

      return this.highlight.errors || []
    },

    isHovering () {
      return !this.isEditing && this.highlight.hover
    },

    isEditing () {
      return this.editMode || get(this.highlight, 'edit', false)
    },

    isLoading () {
      return this.allHighlights[this.references]?.loading || false
    },

    hasPrecedingOrder () {
      return this.order.preceded_by_order_id && get(this.order.preceding_order_changes, this.references, undefined) !== undefined
    },
  },

  methods: {
    ...mapActions(orderForm.moduleName, [
      actionTypes.startHover,
      actionTypes.stopHover,
      actionTypes.startFieldEdit,
      actionTypes.stopFieldEdit,
      actionTypes.clearErrors,
    ]),

    cleanStrForId,

    dismissErrors () {
      this.clearErrors({ path: this.references })
    },

    handleStartEdit () {
      if (this.isLocked || this.onlyHover || this.readonly) {
        return
      }

      this.startFieldEdit({ path: this.references })
    },

    verify () {
      this.$emit('accept')
    },

    handleAccept (saveAll = false) {
      this.stopFieldEdit({ path: this.references })
      this.$emit(saveAll ? 'accept-all' : 'accept')
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
  &:not(.has-notes) {
    .form-field-highlight.hover:hover {
      padding-right: rem(12);
    }
  }

  .form-field-highlight {
    position: relative;
    display: flex;
    align-items: center;
    transition: all 200ms ease-in-out;
    cursor: pointer;

    &.field-updated {
      background-color: rgba(var(--v-orange-changes-base-rgb), 0.1);

      &::after {
        content: " ";
        position: absolute;
        height: 100%;
        width: rem(4);
        background-color: var(--v-orange-changes-base);
      }
    }

    &.hover {
      background-color: rgba($blue--lt, 0.4);
    }

    &:focus {
      outline: var(--v-primary-base) auto 1px;
    }

    &.hover-paddingless {
      background-color: rgba($blue--lt, 0.4);
    }

    &.edit {
      transition: none;
      background-color: $blue--lt;
    }
  }

  &.has-notes {
    background-color: rgba(var(--v-success-base-rgb), 0.2) !important;
  }

  .only-hover {
    width: 100%;
  }

  .highlight__edit.edit {
    width: 100%;
    display: flex;
    padding: rem(7) rem(5.5) rem(7) rem(10);
    background-color: $blue--lt;
    color: white;

    fieldset {
      border: none;
    }

    .v-input__slot {
      border-radius: rem(4) 0 rem(0) rem(4);
    }

    .v-icon {
      margin-left: auto;
      margin-right: rem(8);
    }
  }
}
</style>
