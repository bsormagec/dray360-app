<template>
  <div class="navigation">
    <v-stepper
      v-model="current"
      vertical
      non-linear
    >
      <Fragment
        v-for="(step, i) in steps"
        :key="step.id"
      >
        <a
          class="navigation__link"
          :href="linkHref(step)"
        >
          <v-stepper-step
            :class="{
              navigation__step: true,
              active: isActive(step.id),
              hide: shouldHide(step.id),
              small: !isTitle(step.id)
            }"
            step=""
            :complete="isTitle(step.id)"
            complete-icon="mdi-account"
            @click="setStep(step.id)"
          >
            {{ step.isInventoryItem ? step.inventoryItemText : step.text }}
          </v-stepper-step>
        </a>

        <v-stepper-content
          :class="{ navigation__separator: true, hide: shouldHide(step.id) }"
          :step="i"
        />
      </Fragment>
    </v-stepper>
  </div>
</template>

<script>
import { Fragment } from 'vue-fragment'
import { detailsState } from '@/views/Details/inner_store'
import { navigationSteps } from '@/views/Details/inner_utils/navigation_steps'
import { cleanStrForId } from '@/views/Details/inner_utils/clean_str_for_id'
import { isInViewport } from '@/utils/is_in_viewport'

export default {
  name: 'DetailsSidebarNavigation',

  components: {
    Fragment
  },

  data: () => ({
    current: 1
  }),

  computed: {
    steps () {
      return navigationSteps()
    },

    isEditing () {
      return detailsState.isEditing
    },

    idSuffix () {
      return this.isEditing ? 'editing' : 'viewing'
    }
  },

  watch: {
    isEditing: function (val) {
      const currentHash = document.location.hash
        ? document.location.hash.split('-')[0] : false

      if (currentHash) {
        document.location.hash = `${currentHash}-${this.idSuffix}`
      }

      this.trackFormScroll()
    }
  },

  mounted () {
    this.trackFormScroll()
  },

  methods: {
    cleanStrForId,

    trackFormScroll () {
      const form = document.querySelector(
        this.isEditing ? '.form-editing .form' : '.form-viewing .form'
      )

      const handleScroll = () => {
        try {
          this.steps.forEach(step => {
            if (isInViewport(document.querySelector(this.linkHref(step)))) {
              this.setStep(step.id)
              throw new Error()
            }
          })
        } catch (e) {
          return e
        }
      }

      form.addEventListener('scroll', handleScroll)
    },

    linkHref (step) {
      return `#${cleanStrForId(step.text.toLowerCase())}-${this.idSuffix}`
    },

    setStep (n) {
      this.current = n
    },

    isActive (n) {
      if (this.current === n) return true
      if (String(this.current).split('.')[0] === String(n)) return true
    },

    isTitle (n) {
      return String(n).split('.').length === 1
    },

    shouldHide (n) {
      if (this.isTitle(n)) return false
      const titlePart = String(n).split('.')[0]
      const titlePartLeading = String(this.current).split('.')[0] === titlePart
      return !titlePartLeading
    }
  }
}
</script>

<style lang="scss" scoped>
.navigation {
  display: flex;
  margin-top: 3.1rem;
  margin-bottom: 3.1rem;
}

.navigation__link {
  text-decoration: none;
  text-transform: capitalize;
}
</style>
