<template>
  <v-row
    no-gutters
    class="pa-5"
  >
    <v-col
      cols="12"
      sm="12"
    >
      <v-row>
        <v-col
          cols="1"
          sm="10"
        >
          <v-row>
            <v-col
              cols="1"
              sm="3"
            >
              <v-autocomplete
                auto-select-first
                :items="companies"
                item-text="name"
                item-value="id"
                label="Company"
                clearable
                return-object
                @change="companyChanged"
              />
              <v-autocomplete
                auto-select-first
                :items="variants"
                item-text="abbyy_variant_name"
                item-value="id"
                label="Variant"
                clearable
                return-object
                @change="variantChanged"
              />
              <v-card class="ma-auto">
                <v-list>
                  <v-subheader>{{ company.name }} - {{ variant.abbyy_variant_name }}</v-subheader>
                  <v-list-item-group
                    color="primary"
                    :value="companyVariantRuleToEdit"
                    @change="item => ruleSelectedToEdit('company-variant', item)"
                  >
                    <draggable
                      :value="companyVariantRules"
                      group="rules"
                      @change="value => dragEnded('company-variant', value)"
                    >
                      <v-list-item
                        v-for="(rule, i) in companyVariantRules"
                        :key="i"
                      >
                        <v-list-item-content class="draggable-item">
                          <v-list-item-title v-text="rule.name" />
                        </v-list-item-content>
                        <v-btn
                          icon
                          :disabled="!hasPermission('rules-editor-assign')"
                          color="primary"
                          :ripple="false"
                          @click="removeFromCompanyVariant(rule)"
                        >
                          <v-icon>mdi-window-close</v-icon>
                        </v-btn>
                      </v-list-item>
                    </draggable>
                  </v-list-item-group>
                </v-list>
                <v-btn
                  color="success"
                  :disabled="!companyVariantSelected || !hasPermission('rules-editor-assign')"
                  class="ma-4"
                  @click="saveAssignment({ companyId: company.id, variantId: variant.id })"
                >
                  Save
                </v-btn>
                <v-btn
                  :disabled="!companyVariantSelected"
                  class="ma-4"
                  @click="getCompanyVariantRules()"
                >
                  Cancel
                </v-btn>
              </v-card>
              <v-card class="mt-8">
                <v-list>
                  <v-subheader>All Companies - {{ variant.abbyy_variant_name }}</v-subheader>
                  <v-list-item-group
                    color="primary"
                    :value="variantRuleToEdit"
                    @change="item => ruleSelectedToEdit('variant', item)"
                  >
                    <draggable
                      :value="variantRules"
                      group="rules"
                      @change="value => dragEnded('variant', value)"
                    >
                      <v-list-item
                        v-for="(rule, i) in variantRules"
                        :key="i"
                      >
                        <v-list-item-content class="draggable-item">
                          <v-list-item-title v-text="rule.name" />
                        </v-list-item-content>
                        <v-btn
                          icon
                          :disabled="!hasPermission('rules-editor-assign')"
                          color="primary"
                          :ripple="false"
                          @click="removeFromVariant(rule)"
                        >
                          <v-icon>mdi-window-close</v-icon>
                        </v-btn>
                      </v-list-item>
                    </draggable>
                  </v-list-item-group>
                </v-list>
                <v-btn
                  color="success"
                  :disabled="!variant.id || !hasPermission('rules-editor-assign')"
                  class="ma-4"
                  @click="saveAssignment({ companyId: null, variantId: variant.id })"
                >
                  Save
                </v-btn>
                <v-btn
                  :disabled="!companyVariantSelected"
                  class="ma-4"
                  @click="getVariantRules()"
                >
                  Cancel
                </v-btn>
              </v-card>
            </v-col>
            <v-col
              cols="1"
              sm="9"
            >
              <v-row>
                <v-col
                  cols="1"
                  sm="12"
                >
                  <v-card
                    elevation="4"
                    height="600"
                    color="black"
                  >
                    <div v-if="ruleToEdit">
                      <codemirror
                        ref="cmEditor"
                        v-model="ruleToEdit.code"
                        :options="cmOptions"
                      />
                    </div>
                  </v-card>
                </v-col>
              </v-row>
              <v-row>
                <v-col
                  cols="4"
                  sm="4"
                  class="d-flex"
                >
                  <v-text-field
                    v-if="ruleToEdit"
                    v-model="testOrderId"
                    placeholder="Order Id"
                    type="number"
                    outlined
                    dense
                    flat
                    hide-details
                  />
                  <v-btn
                    v-if="ruleToEdit"
                    :disabled="!testOrderId"
                    class="ml-4"
                    color="default"
                    @click="testSingleRule"
                  >
                    Test Rule
                  </v-btn>
                </v-col>
                <v-col
                  cols="4"
                  sm="4"
                >
                  <v-btn
                    v-if="ruleToEdit"
                    :disabled="!hasPermission('rules-editor-edit')"
                    color="success"
                    class="mr-4"
                    @click="updateRule"
                  >
                    Save Rule
                  </v-btn>
                  <v-btn
                    v-if="ruleToEdit"
                    @click="cancelRuleEdit"
                  >
                    Cancel
                  </v-btn>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
          <v-row>
            <v-col
              cols="1"
              sm="3"
            />
          </v-row>
          <v-row>
            <v-col
              cols="1"
              sm="12"
            >
              <v-row>
                <v-col
                  cols="1"
                  sm="6"
                >
                  <v-tabs
                    v-if="testOutput"
                    grow
                  >
                    <v-tab>
                      Original Fields
                    </v-tab>
                    <v-tab>
                      Previous Rules Output
                    </v-tab>

                    <v-tab-item
                      :transition="false"
                      :reverse-transition="false"
                    >
                      <v-btn
                        v-if="testOutput"
                        v-clipboard:copy="pasteAbleInput"
                        v-clipboard:success="onCopy"
                        v-clipboard:error="onError"
                        class="ma-2"
                        outlined
                        color="indigo"
                      >
                        Copy to Clipboard
                      </v-btn>
                      <vue-json-pretty
                        v-if="testOutput"
                        :depth="0"
                        :path="'res'"
                        :data="testOutput.input.original_fields"
                        class="font-weight-black"
                      />
                    </v-tab-item>

                    <v-tab-item
                      :transition="false"
                      :reverse-transition="false"
                    >
                      <v-btn
                        v-if="testOutput"
                        v-clipboard:copy="pasteAbleInput"
                        v-clipboard:success="onCopy"
                        v-clipboard:error="onError"
                        class="ma-2"
                        outlined
                        color="indigo"
                      >
                        Copy to Clipboard
                      </v-btn>
                      <vue-json-pretty
                        v-if="testOutput"
                        :depth="0"
                        :path="'res'"
                        :data="testOutput.input.original_output"
                        class="font-weight-black"
                      />
                    </v-tab-item>
                  </v-tabs>
                </v-col>
                <v-col
                  cols="1"
                  sm="6"
                >
                  <v-card
                    elevation="2"
                    class="px-2"
                  >
                    <v-row
                      v-if="testOutput"
                    >
                      <v-col
                        cols="1"
                        sm="3"
                      >
                        <h6>HTTP Status:</h6>
                      </v-col>
                      <v-col
                        cols="1"
                        sm="2"
                      >
                        <h6> {{ testOutput.status }} </h6>
                      </v-col>
                      <v-col
                        cols="1"
                        sm="7"
                      >
                        <h6> {{ testOutput.statusText }} </h6>
                      </v-col>
                    </v-row>
                  </v-card>

                  <v-card
                    v-if="testOutput"
                    elevation="2"
                    class="pa-2 my-4"
                  >
                    <h6>JSON Data:</h6>
                    <vue-json-pretty
                      v-if="testOutput"
                      class="font-weight-black"
                      :data="testOutput.output"
                      :show-length="false"
                      show-line
                      collapsed-on-click-brackets
                      @click="handleClick(...arguments, 'complexTree')"
                      @change="handleChange"
                    />
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-col>

        <v-col
          cols="1"
          sm="2"
        >
          <v-card
            class="mx-auto"
            tile
          >
            <v-card-title>
              Rules Library
              <v-btn
                class="mx-auto"
                color="yellow"
                :disabled="!hasPermission('rules-editor-create')"
                @click="addNewRule"
              >
                New Rule
              </v-btn>
            </v-card-title>
            <v-card
              class="rulesLibrary mx-auto"
              max-width="500"
            >
              <v-card-text>
                <v-treeview
                  :items="groupedRules"
                  activatable
                  :open-on-click="true"
                  transition
                >
                  <template v-slot:prepend="{ item }">
                    <v-icon
                      v-if="item.index"
                    >
                      mdi-folder-network
                    </v-icon>
                    <v-btn
                      v-if="!item.index && companyVariantSelected"
                      icon
                      small
                      :disabled="!hasPermission('rules-editor-assign')"
                      @click="assignTo('company-variant', item)"
                    >
                      <v-icon>mdi-chevron-left</v-icon>
                    </v-btn>
                    <v-btn
                      v-if="!item.index && variant.id"
                      small
                      icon
                      :disabled="!hasPermission('rules-editor-assign')"
                      @click="assignTo('variant', item)"
                    >
                      <v-icon>mdi-chevron-double-left</v-icon>
                    </v-btn>
                  </template>

                  <template v-slot:label="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <span
                          v-bind="attrs"
                          v-on="on"
                        >{{ item.name }}</span>
                      </template>
                      <span>{{ item.name }}</span>
                    </v-tooltip>
                  </template>
                </v-treeview>
              </v-card-text>
            </v-card>
          </v-card>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>
<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import draggable from 'vuedraggable'
import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/monokai.css'
import 'codemirror/mode/python/python.js'
import 'codemirror/addon/selection/active-line.js'
import 'codemirror/addon/edit/closebrackets.js'
import 'vue-json-pretty/lib/styles.css'
import VueJsonPretty from 'vue-json-pretty'
import rulesEditor, { actionTypes as rulesEditorActionsTypes } from '@/store/modules/rules_editor'
import cloneDeep from 'lodash/cloneDeep'
import get from 'lodash/get'
import utils, { actionTypes } from '@/store/modules/utils'
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'
import { uuid } from '@/utils/uuid_valid_id'

export default {
  name: 'RulesEditor',

  components: {
    draggable,
    codemirror,
    VueJsonPretty
  },

  mixins: [permissions, allCompanies],

  data: () => ({
    testOrderId: null,
    cmOptions: {
      autoCloseBrackets: true,
      tabSize: 4,
      mode: 'text/x-python',
      theme: 'monokai',
      lineNumbers: true,
      line: true,
      foldGutter: true,
      showCursorWhenSelecting: true,
      styleActiveLine: true,
    },
    draggable_rules: [],
    companyVariantRuleToEdit: undefined,
    variantRuleToEdit: undefined,
    ruleToEdit: undefined,
    company: { name: '', id: null },
    variant: { name: '', id: null },
  }),

  computed: {
    ...mapGetters(rulesEditor.moduleName, ['groupedRules']),
    ...mapState(rulesEditor.moduleName, {
      rules: state => state.rules,
      variants: state => state.variants,
      companyVariantRules: state => state.companyVariantRules,
      variantRules: state => state.variantRules,
      testOutput: state => state.testOutput,
    }),

    companyVariantSelected () {
      return !!(this.company.id && this.variant.id)
    },

    pasteAbleInput: function () {
      return this.testOutput ? JSON.stringify(get(this.testOutput, 'input.original_fields', {})).replace(/\n/g, '\\n') : ''
    },
  },

  async beforeMount () {
    await this.fetchRules()
    await this.fetchVariants()
    await this.fetchCompanies()
  },

  methods: {
    ...mapActions(rulesEditor.moduleName, [
      rulesEditorActionsTypes.fetchRules,
      rulesEditorActionsTypes.fetchVariants,
      rulesEditorActionsTypes.fetchCompanyVariantRules,
      rulesEditorActionsTypes.fetchVariantRules,
      rulesEditorActionsTypes.setCompanyVariantRules,
      rulesEditorActionsTypes.setVariantRules,
      rulesEditorActionsTypes.createRule,
      rulesEditorActionsTypes.editRule,
      rulesEditorActionsTypes.saveRulesAssigment,
      rulesEditorActionsTypes.testRule,
    ]),

    ...mapActions(utils.moduleName, [actionTypes.setSnackbar, actionTypes.setConfirmationDialog]),

    onCopy: function (e) {
      console.log('copied')
    },

    onError: function (e) {
      console.log('Failed to copy texts')
    },

    ruleSelectedToEdit (type, item) {
      if (item === undefined) {
        this.variantRuleToEdit = undefined
        this.companyVariantRuleToEdit = undefined
        this.ruleToEdit = undefined
        return
      }

      let ruleId = null

      if (type === 'company-variant') {
        this.companyVariantRuleToEdit = item
        this.variantRuleToEdit = undefined
        ruleId = this.companyVariantRules[this.companyVariantRuleToEdit].id
      } else {
        this.companyVariantRuleToEdit = undefined
        this.variantRuleToEdit = item
        ruleId = this.variantRules[this.variantRuleToEdit].id
      }
      this.ruleToEdit = cloneDeep(this.rules.find(rule => rule.id === ruleId))
    },

    async getCompanyVariantRules () {
      if (!this.companyVariantSelected) {
        return
      }

      const [error] = await this.fetchCompanyVariantRules({
        company_id: this.company.id,
        variant_id: this.variant.id,
      })

      if (error) {
        this.setSnackbar({ message: 'There was an error while fetching the company variant rules' })
      }
    },

    async getVariantRules () {
      if (!this.variant.id) {
        return
      }

      const [error] = await this.fetchVariantRules({ variant_id: this.variant.id })

      if (error) {
        this.setSnackbar({ message: 'There was an error while fetching the variant rules' })
      }
    },

    async updateRule () {
      if (!this.ruleToEdit) {
        return
      }

      const [error] = await this.editRule(this.ruleToEdit)

      if (error) {
        this.setSnackbar({ message: 'There was an error updating the rule' })
        return
      }
      this.setSnackbar({ message: 'Rule updated successfully' })
    },

    cancelRuleEdit () {
      this.ruleToEdit = cloneDeep(this.rules.find(rule => rule.id === this.ruleToEdit.id))
    },

    async addNewRule () {
      const name = prompt('Please type the name of the new rule')
      if (name === null) {
        return
      }
      const description = prompt('Rule description: events, direction, utility, etc.')
      const code = prompt('Please paste the code for the rule')

      const [error] = await this.createRule({
        name,
        description,
        code,
        id: uuid()
      })

      if (error) {
        this.setSnackbar({ message: 'There was an error creating the rule' })
        return
      }
      this.setSnackbar({ message: 'Rule created successfully' })
    },

    async saveAssignment (data) {
      const [error] = await this.saveRulesAssigment(data)

      if (error) {
        this.setSnackbar({ message: 'There was an error saving the rules assignment' })
        return
      }
      this.setSnackbar({ message: 'Rules assignment saved successfully' })
    },

    assignTo (type, rule) {
      this.setConfirmationDialog({
        open: true,
        title: `Add rule to ${type}`,
        text: `Do you want to add the rule '${rule.name}' to the selected ${type}?`,
        confirmText: 'Add',
        cancelText: 'Cancel',
        onConfirm: () => {
          const propertyName = type === 'company-variant' ? 'companyVariantRules' : 'variantRules'
          const methodName = type === 'company-variant' ? 'setCompanyVariantRules' : 'setVariantRules'
          const newAssignment = cloneDeep(this[propertyName])

          newAssignment.push(rule)
          this[methodName](newAssignment)
        },
        onCancel: () => {}
      })
    },

    dragEnded (type, { moved }) {
      const propertyName = type === 'company-variant' ? 'companyVariantRules' : 'variantRules'
      const methodName = type === 'company-variant' ? 'setCompanyVariantRules' : 'setVariantRules'
      const newAssignment = cloneDeep(this[propertyName])
      const temp = newAssignment[moved.newIndex]
      newAssignment[moved.newIndex] = newAssignment[moved.oldIndex]
      newAssignment[moved.oldIndex] = temp

      this[methodName](newAssignment)
    },

    removeFromCompanyVariant (rule) {
      if (this.companyVariantRules.length === 1) {
        this.setSnackbar({ message: 'There must be at least 1 rule' })
        return
      }

      const index = this.companyVariantRules.findIndex(item => item.id === rule.id)

      if (index === -1) return
      const newAssignment = cloneDeep(this.companyVariantRules)
      newAssignment.splice(index, 1)
      this.setCompanyVariantRules(newAssignment)
    },

    removeFromVariant (variant) {
      if (this.variantRules.length === 1) {
        this.setSnackbar({ message: 'There must be at least 1 rule' })
        return
      }

      const index = this.variantRules.findIndex(item => item.id === variant.id)

      if (index === -1) return
      const newAssignment = cloneDeep(this.variantRules)
      newAssignment.splice(index, 1)
      this.setVariantRules(newAssignment)
    },

    companyChanged (company) {
      if (!company) {
        this.company = { name: '', id: null }
        this.setCompanyVariantRules([])
        this.ruleSelectedToEdit('company-variant', undefined)
      } else {
        this.company = company
      }

      this.getCompanyVariantRules()
    },

    variantChanged (variant) {
      if (!variant) {
        this.variant = { abbyy_variant_name: '', id: null }
        this.ruleSelectedToEdit('variant', undefined)
        this.setCompanyVariantRules([])
        this.setVariantRules([])
      } else {
        this.variant = variant
        this.getCompanyVariantRules()
        this.getVariantRules()
      }
    },

    async testSingleRule () {
      const dataObject = { orderId: this.testOrderId, ruleToTest: this.ruleToEdit }
      await this.testRule(dataObject)
    },

    handleClick (path, data, treeName = '') {
      console.log('click: ', path, data, treeName)
      this.itemPath = path
      this.itemData = !data ? data + '' : data
    },

    handleChange (newVal, oldVal) {
      console.log('newVal: ', newVal, ' oldVal: ', oldVal)
    }
  }
}
</script>
<style lang="scss" scoped>
.draggable-item {
  padding: rem(10);
}
.rulesLibrary{
  max-height: 600px;
  max-width: 500px;
  height: auto;
  overflow-y: auto;
  overflow-x: hidden;
}
</style>
<style lang="scss">
.CodeMirror {
  border: 1px solid #eee;
  height: 600px !important;
  font-size: 18px;
}
.rulesLibrary{
  .v-treeview-node__level{
    display: none;
  }
}
.rules-editor__label {
  display: flex;
  align-items: center;
  margin-bottom: rem(8);
  .v-btn {
    min-width: unset;
    margin-right: rem(8);
  }
}
</style>
