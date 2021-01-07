<template>
  <v-row no-gutters>
    <v-col
      cols="12"
      sm="12"
    >
      <div class="container">
        <div class="row justify-content-center">
          <v-col
            cols="1"
            sm="10"
          >
            <v-row>
              <v-col
                cols="1"
                sm="3"
              >
                <v-menu offset-y>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      color="primary"
                      dark
                      :ripple="false"
                      class="ma-4"
                      v-on="on"
                    >
                      Select Company
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item
                      v-for="(company, index) in company_list()"
                      :key="index"
                      @click="updateSelectedCompany(company)"
                    >
                      <v-list-item-title>{{ company.name }}</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
                <v-menu offset-y>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      color="primary"
                      dark
                      :ripple="false"
                      class="ma-4"
                      v-on="on"
                    >
                      Select Variant
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item
                      v-for="(variant, index) in variant_list()"
                      :key="index"
                      @click="updateSelectedVariant(variant)"
                    >
                      <v-list-item-title>{{ variant.abbyy_variant_name }}</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
                <v-card
                  class="ma-auto"
                  tile
                >
                  <v-list>
                    <v-subheader>{{ company_name }}</v-subheader>
                    <v-subheader>{{ variant_name }}</v-subheader>
                    <v-list-item-group color="primary">
                      <draggable
                        v-model="draggable_rules"
                        group="rules"
                        @start="drag=true"
                        @end="drag=false"
                      >
                        <v-list-item
                          v-for="(rule, i) in draggable_rules"
                          :key="i"
                        >
                          <v-list-item-content class="draggable-item">
                            <v-list-item-title v-text="rule.name" />
                          </v-list-item-content>
                          <v-btn
                            icon
                            color="primary"
                            :ripple="false"
                            @click="removeFromCompanyVariant(i)"
                          >
                            <v-icon>mdi-window-close</v-icon>
                          </v-btn>
                        </v-list-item>
                      </draggable>
                    </v-list-item-group>
                  </v-list>
                </v-card>
                <div class="card">
                  <div class="card-header">
                    <v-btn
                      color="success"
                      :disabled="!companyVariantSelected"
                      class="ma-4"
                      @click="saveRuleSequence()"
                    >
                      Save Variant
                    </v-btn>
                    <v-btn
                      :disabled="!companyVariantSelected"
                      class="ma-4"
                      @click="cancelSequenceEdition()"
                    >
                      Cancel
                    </v-btn>
                  </div>
                </div>
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
                      <div v-if="company_variant_rules().length">
                        <codemirror
                          ref="cmEditor"
                          v-model="company_variant_rules()[selected_rule_index].code"
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
                  >
                    <v-menu
                      v-if="company_variant_rules().length > 0"
                      offset-y
                    >
                      <template v-slot:activator="{ on }">
                        <v-btn
                          color="primary"
                          dark
                          class="ml-12 mx-auto"
                          v-on="on"
                        >
                          Select Rule to Edit
                        </v-btn>
                      </template>
                      <v-list>
                        <v-list-item
                          v-for="(rule, index) in company_variant_rules()"
                          :key="index"
                          @click="updateSelectedIndex(index)"
                        >
                          <v-list-item-title>{{ rule.name }}</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </v-col>
                  <v-col
                    cols="4"
                    sm="4"
                  >
                    <v-btn
                      v-if="company_variant_rules().length > 0"
                      class="mr-4"
                      color="default"
                      @click="testSingleRule(selected_rule_index)"
                    >
                      Test Rule
                    </v-btn>
                  </v-col>
                  <v-col
                    cols="4"
                    sm="4"
                  >
                    <v-btn
                      v-if="company_variant_rules().length > 0"
                      color="success"
                      class="mr-4"
                      @click="editRule(selected_rule_index)"
                    >
                      Save Rule
                    </v-btn>
                    <v-btn
                      v-if="company_variant_rules().length > 0"
                      disabled
                      @click="cancelRuleEdition(selected_rule_index)"
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
                sm="12"
              >
                <v-row>
                  <v-col
                    cols="1"
                    sm="6"
                  >
                    <v-tabs
                      v-if="testing_output"
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
                          v-if="testing_output"
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
                          v-if="testing_output"
                          :path="'res'"
                          :data="testing_output.input.original_fields"
                          class="font-weight-black"
                        />
                      </v-tab-item>

                      <v-tab-item
                        :transition="false"
                        :reverse-transition="false"
                      >
                        <v-btn
                          v-if="testing_output"
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
                          v-if="testing_output"
                          :path="'res'"
                          :data="testing_output.input.original_output"
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
                        v-if="testing_output"
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
                          <h6> {{ testing_output.status }} </h6>
                        </v-col>
                        <v-col
                          cols="1"
                          sm="7"
                        >
                          <h6> {{ testing_output.statusText }} </h6>
                        </v-col>
                      </v-row>
                    </v-card>

                    <v-card
                      v-if="testing_output"
                      elevation="2"
                      class="pa-2 my-4"
                    >
                      <h6>JSON Data:</h6>
                      <vue-json-pretty
                        v-if="testing_output"
                        :data="testing_output.output"
                        :show-length="showLength"
                        :show-line="showLine"
                        :collapsed-on-click-brackets="collapsedOnClickBrackets"
                        class="font-weight-black"
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
                  @click="addRuleToLibrary()"
                >
                  New Rule
                </v-btn>
              </v-card-title>

              <v-list>
                <v-list-item-group color="primary">
                  <v-list-item
                    v-for="rule in rules_library()"
                    :key="rule.id"
                  >
                    <v-btn
                      icon
                      color="primary"
                      :ripple="false"
                      @click="addToCompanyVariant(rule.id)"
                    >
                      <v-icon>mdi-chevron-left</v-icon>
                    </v-btn>
                    <v-list-item-content>
                      <v-list-item-title v-text="rule.name" />
                    </v-list-item-content>
                  </v-list-item>
                </v-list-item-group>
              </v-list>
            </v-card>
          </v-col>
        </div>
      </div>
    </v-col>
  </v-row>
</template>
<script>
import { mapState, mapActions } from 'vuex'
import { reqStatus } from '@/enums/req_status'
import draggable from 'vuedraggable'
import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/monokai.css'
import 'codemirror/mode/python/python.js'
import 'codemirror/addon/selection/active-line.js'
import 'codemirror/addon/edit/closebrackets.js'
import VueJsonPretty from 'vue-json-pretty'
import rulesLibrary, { types } from '@/store/modules/rules_editor'
import get from 'lodash/get'

export default {
  name: 'RulesEditor',
  components: {
    draggable,
    codemirror,
    VueJsonPretty
  },
  data: () => ({
    deep: 3,
    collapsedOnClickBrackets: true,
    selectableType: 'single',
    showSelectController: true,
    showLength: false,
    showLine: true,
    ...mapState(rulesLibrary.moduleName, {
      rules_library: state => state.rules_library,
      company_variant_rules: state => state.company_variant_rules,
      company_list: state => state.company_list,
      variant_list: state => state.variant_list
    }),

    cmOptions: {
      autoCloseBrackets: true,
      tabSize: 4,
      mode: 'text/x-python',
      theme: 'monokai',
      lineNumbers: true,
      line: true,
      foldGutter: true,
      showCursorWhenSelecting: true,
      styleActiveLine: true
    },
    draggable_rules: [],
    selected_rule_index: 0,
    company_id: null,
    variant_id: null,
    company_name: '',
    variant_name: ''
  }),
  computed: {
    ...mapState(rulesLibrary.moduleName, {
      testing_output: state => state.testing_output
    }),
    companyVariantSelected () {
      return this.company_id !== null && this.variant_id !== null
    },
    pasteAbleInput: function () {
      return this.testing_output ? JSON.stringify(get(this.testing_output, 'input.original_fields', {})).replace(/\n/g, '\\n') : ''
    },
    codemirror () {
      return this.$refs.cmEditor.codemirror
    }
  },

  async mounted () {
    await this.fetchRules()
  },
  methods: {
    ...mapActions(rulesLibrary.moduleName, [types.getLibrary, types.getCompanyVariantRules, types.setSequence, types.setRule, types.addRule, types.setRuleCode, types.getTestingOutput, types.getCompanyList, types.getVariantList]),
    onCopy: function (e) {
      console.log('copied')
    },

    onError: function (e) {
      console.log('Failed to copy texts')
    },

    async fetchRulesLibrary () {
      const status = await this[types.getLibrary]()

      if (status === reqStatus.success) {
        console.log('fetchRulesLibrary')
      } else {
        console.log('fetchRulesLibrary error')
      }
    },

    async fetchCompanyList () {
      const status = await this[types.getCompanyList]()

      if (status === reqStatus.success) {
        console.log('fetchCompanyList')
      } else {
        console.log('fetchCompanyList error')
      }
    },

    async fetchVariantList () {
      const status = await this[types.getVariantList]()
      if (status === reqStatus.success) {
        console.log('fetchVariantList')
      } else {
        console.log('fetchVariantList error')
      }
    },

    async fetchVariantName () {
      const status = await this[types.getVariantName](this.variant_id)

      if (status === reqStatus.success) {
        console.log('fetchVariantName')
      } else {
        console.log('fetchVariantName error')
      }
    },

    async fetchCompanyVariantRules () {
      this.updateSelectedIndex(0)
      if (
        this.company_id === null ||
        this.variant_id === null ||
        this.company_id === undefined ||
        this.variant_id === undefined
      ) {
        return
      }

      const pairIds = {
        company_id: this.company_id,
        variant_id: this.variant_id
      }

      const status = await this[types.getCompanyVariantRules](pairIds)

      this.draggable_rules = this.company_variant_rules()

      if (status === reqStatus.success) {
        console.log('fetchCompanyVariantRules')
      } else {
        console.log('fetchCompanyVariantRules error')
      }
    },

    async editRule (index) {
      console.log('ruleId' + this.company_variant_rules()[index].id)
      const ruleId = this.company_variant_rules()[index].id
      const ruleName = this.company_variant_rules()[index].name
      const ruleDescription = this.company_variant_rules()[index].description

      const ruleData = {
        code: this.company_variant_rules()[index].code,
        description: ruleDescription,
        id: ruleId,
        name: ruleName
      }

      const status = await this[types.setRule](ruleData)

      if (status === reqStatus.success) {
        console.log('editRule')
      } else {
        console.log('editRule error')
      }
    },

    async saveRuleSequence () {
      const idsToSave = []
      this.draggable_rules.forEach(rule => idsToSave.push(rule.id))

      const sequenceData = {
        company_id: this.company_id,
        variant_id: this.variant_id,
        rules: idsToSave
      }

      const status = await this[types.setSequence](sequenceData)

      if (status === reqStatus.success) {
        console.log('saveSequence')
      } else {
        console.log('saveSequence error')
      }
    },

    // THIS IS USEFUL FOR DEBUGGING PURPOSES
    // onCmCodeChange (index) {
    //   const vc = this
    //   // vc.company_variant_rules[index].code = JSON.stringify(vc.company_variant_rules[index].code)
    //   console.log('oncmcodechange')
    //   console.log(vc.company_variant_rules[index].code)
    // },

    async addRuleToLibrary () {
      const newName = prompt('Please type the name of the new rule')
      const newDescription = prompt('Rule description: events, direction, utility, etc.')

      let newCode = null
      if (newName !== null) {
        newCode = prompt('Please paste the code for the rule')
      }

      const ruleData = {
        name: newName,
        description: newDescription,
        code: newCode,
        id: (this.rules_library().length + 1)
      }

      const status = await this[types.addRule](ruleData)

      if (status === reqStatus.success) {
        console.log('addRuleToLibrary')
      } else {
        console.log('addRuleToLibrary error')
      }
    },

    addToCompanyVariant (ruleId) {
      if (this.company_id === null || this.variant_id === null) {
        alert('Please select a company/variant pair first.')
        return
      }
      const rule = this.rules_library().find(rule => rule.id === ruleId)

      if (confirm('Add ' + rule.name + ' to company variant')) {
        this.draggable_rules.push(rule)
      }
    },

    removeFromCompanyVariant (i) {
      if (this.draggable_rules.length >= 1) {
        this.draggable_rules.splice(i, 1)
        this.updateSelectedIndex(0)
      } else {
        alert('There must be at least 1 rule')
      }
    },

    updateSelectedIndex (i) {
      this.selected_rule_index = i
      console.log('selected index: ' + i)
    },

    updateSelectedCompany (company) {
      this.company_name = company.name
      this.company_id = company.id
      this.fetchRules()
    },

    updateSelectedVariant (variant) {
      this.variant_name = variant.abbyy_variant_name
      this.variant_id = variant.id
      this.fetchRules()
    },

    fetchRules () {
      this.fetchRulesLibrary()
      this.fetchCompanyVariantRules()
      this.fetchCompanyList()
      this.fetchVariantList()
    },

    async testSingleRule (index) {
      const ruleToTest = this.draggable_rules[index]
      const orderId = prompt('Please enter order ID')
      if (orderId == null) {
        return
      }
      const dataObject = { orderId, ruleToTest }
      const status = await this[types.getTestingOutput](dataObject)
      if (status === reqStatus.success) {
        console.log('testSingleRule success')
      }
    },

    async cancelRuleEdition (index) {
      this.draggable_rules[index].code = await this[types.setRuleCode](index)
    },

    cancelSequenceEdition () {
      this.fetchCompanyVariantRules()
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
.container {
  padding: 0px;
}
</style>
<style lang="scss">
.CodeMirror {
  border: 1px solid #eee;
  height: 600px !important;
  font-size: 18px;
}
</style>
