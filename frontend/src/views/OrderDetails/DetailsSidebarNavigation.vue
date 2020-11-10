<template>
  <div class="navigation">
    <v-stepper
      v-model="currentKey"
      vertical
      non-linear
    >
      <Fragment
        v-for="(section, key) in sections"
        :key="section.id"
      >
        <a :class="`navigation__link ${key}`">
          <v-stepper-step
            :class="{
              navigation__step: true,
              active: isActive(key),
              hide: shouldHide(key),
              small: section.subsection
            }"
            step=""
            :complete="!section.subsection"
            complete-icon="mdi-account"
            @click="jumpTo(key)"
          >
            {{ section.label }}
          </v-stepper-step>
        </a>

        <v-stepper-content
          :class="{ navigation__separator: true, hide: shouldHide(key) }"
          :step="key"
        />
      </Fragment>
    </v-stepper>
  </div>
</template>

<script>
import { Fragment } from 'vue-fragment'
import { isInViewport } from '@/utils/is_in_viewport'
import { scrollTo } from '@/utils/scroll_to'
import { mapState } from 'vuex'

import orderForm from '@/store/modules/order-form'

export default {
  name: 'DetailsSidebarNavigation',

  components: {
    Fragment
  },

  data: () => ({
    currentKey: 'shipment',
    isJumping: false
  }),

  computed: {
    ...mapState(orderForm.moduleName, {
      sections: state => state.sections,
      editMode: state => state.editMode
    }),
    currentSection () {
      return this.sections[this.currentKey]
    },

    idSuffix () {
      return this.editMode ? 'editing' : 'viewing'
    }
  },

  mounted () {
    this.trackFormScroll()
  },

  methods: {
    setSectionKey (key) {
      this.currentKey = key
    },

    isSubsection (key) {
      return this.sections[key].subsection
    },

    isActive (key) {
      const selectedSection = this.sections[key]

      return this.currentSection.id === selectedSection.id || this.currentSection.parent === key
    },

    shouldHide (key) {
      if (!this.isSubsection(key)) return false

      const selectedSection = this.sections[key]

      return selectedSection.parent !== this.currentKey &&
        this.currentSection.parent !== selectedSection.parent
    },

    jumpTo (key) {
      this.isJumping = true
      this.setSectionKey(key)
      scrollTo(this.currentSection.id)

      setTimeout(() => {
        this.isJumping = false
      }, 500)
    },

    trackFormScroll () {
      const form = document.querySelector('#order-form')

      const handleScroll = () => {
        if (this.isJumping) return
        try {
          for (const key in this.sections) {
            if (isInViewport(document.querySelector(`#${this.sections[key].id}`))) {
              this.setSectionKey(key)
              throw new Error()
            }
          }
        } catch (e) {
          return e
        }
      }

      try {
        form.addEventListener('scroll', handleScroll)
      } catch (e) {
        return e
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.navigation {
  display: flex;
  margin-top: rem(31);
  margin-bottom: rem(31);
}

.navigation__link {
  text-decoration: none;
  text-transform: capitalize;
}
</style>
