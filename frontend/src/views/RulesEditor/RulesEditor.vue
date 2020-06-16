<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-2">
        <div class="card">
          <div class="card-header" />
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <v-row>
              <v-col
                cols="8"
                sm="8"
              >
                <v-menu offset-y>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      color="primary"
                      dark
                      v-on="on"
                    >
                      Select Account Variant Rule to Edit
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item
                      v-for="(rule, index) in account_variant_rules()"
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
                  @click="editRule(selected_rule_index)"
                >
                  Save
                </v-btn>
                <v-btn
                  @click="cancelRuleEdition(selected_rule_index)"
                >
                  Cancel
                </v-btn>
                <v-btn
                  v-if="account_variant_rules().length > 0"
                  @click="testSingleRule(selected_rule_index)"
                >
                  Test
                </v-btn>
              </v-col>
            </v-row>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card">
          <div class="card-header">
            <v-btn
              @click="saveRuleSequence()"
            >
              Save
            </v-btn>
            <v-btn
              @click="cancelSequenceEdition()"
            >
              Cancel
            </v-btn>
          </div>
        </div>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-2">
        <v-card
          class="mx-auto"
          max-width="300"
          tile
        >
          <v-list>
            <v-text-field
              label="Search..."
              outlined
            />
            <v-list-item-group
              color="primary"
            >
              <v-list-item
                v-for="(rule, i) in rules_library()"
                :key="i"
              >
                <v-list-item-content>
                  <v-list-item-title
                    v-text="rule.name"
                  />
                </v-list-item-content>
                <button @click="addToAccountVariant(rule.name, rule.code, i)">
                  ->
                </button>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card>
        <v-btn
          @click="addRuleToLibrary()"
        >
          Add Rule to Library
        </v-btn>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div
            v-if="account_variant_rules().length > 0"
            class="card-body"
          >
            <codemirror
              ref="cmEditor"
              v-model="account_variant_rules()[selected_rule_index].code"
              :options="cmOptions"
            />
          </div>
          <vue-json-pretty
            v-if="testing_output()"
            :path="'res'"
            :data="testing_output()"
          />
        </div>
      </div>
      <div class="col-md-2">
        <v-card
          class="mx-auto"
          max-width="300"
          tile
        >
          <v-list>
            <v-subheader>Cushing/Jetspeed Rules</v-subheader>
            <v-list-item-group
              color="primary"
            >
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
                  <button @click="removeFromAccountVariant(i)">
                    X
                  </button>
                </v-list-item>
              </draggable>
            </v-list-item-group>
          </v-list>
        </v-card>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState, mapActions } from '@/utils/vuex_mappings'
import { reqStatus } from '@/enums/req_status'
import draggable from 'vuedraggable'
import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/base16-light.css'
import VueJsonPretty from 'vue-json-pretty'
import rulesLibrary, { types } from '@/store/modules/rules_editor'
export default {
  name: 'RulesEditor',
  components: {
    draggable,
    codemirror,
    VueJsonPretty
  },
  data: () => ({
    ...mapState(rulesLibrary.moduleName, {
      rules_library: state => state.rules_library,
      account_variant_rules: state => state.account_variant_rules,
      testing_output: state => state.testing_output
    }),

    cmOptions: {
      tabSize: 4,
      mode: 'text/x-python',
      theme: 'base16-light',
      lineNumbers: true,
      line: true
    },
    draggable_rules: [],
    selected_rule_index: 0
  }),
  async mounted () {
    const vc = this
    await vc.fetchRules()
  },
  methods: {
    ...mapActions(rulesLibrary.moduleName, [types.getLibrary, types.getAccountVariantRules, types.setSequence, types.setRule, types.addRule, types.setRuleCode, types.getTestingOutput]),

    async fetchRulesLibrary () {
      const status = await this[types.getLibrary]()

      if (status === reqStatus.success) {
        console.log('success')
      } else {
        console.log('error')
      }
    },
    async fetchAccountVariantRules () {
      const vc = this
      const status = await this[types.getAccountVariantRules]()

      vc.draggable_rules = vc.account_variant_rules()

      if (status === reqStatus.success) {
        console.log('fetchAccountVariantRules success')
      } else {
        console.log('fetchAccountVariantRules error')
      }
    },
    async editRule (index) {
      const vc = this
      console.log('ruleId' + vc.account_variant_rules()[index].id)
      const ruleId = vc.account_variant_rules()[index].id
      const ruleName = vc.account_variant_rules()[index].name

      const ruleData = {
        code: vc.account_variant_rules()[index].code,
        description: 'sample rule ' + ruleName,
        id: ruleId,
        name: ruleName
      }

      const status = await this[types.setRule](ruleData)

      if (status === reqStatus.success) {
        console.log('editRules success')
      } else {
        console.log('editRules error')
      }
    },
    async saveRuleSequence () {
      const vc = this
      const idsToSave = []
      vc.draggable_rules.forEach(rule => idsToSave.push(rule.id))

      const sequenceData = {
        account_id: 8,
        variant_id: 8,
        rules: idsToSave
      }

      const status = await this[types.setSequence](sequenceData)

      if (status === reqStatus.success) {
        console.log('saveSequence success')
      } else {
        console.log('saveSequence error')
      }
    },
    // THIS IS USEFUL FOR DEBUGGING PURPOSES
    // onCmCodeChange (index) {
    //   const vc = this
    //   // vc.account_variant_rules[index].code = JSON.stringify(vc.account_variant_rules[index].code)
    //   console.log('oncmcodechange')
    //   console.log(vc.account_variant_rules[index].code)
    // },
    async addRuleToLibrary () {
      const vc = this

      const newName = prompt('Please type the name of the new rule')
      let newCode = null
      if (newName !== null) {
        newCode = prompt('Please paste the code for the rule')
      }

      const ruleData = {
        name: newName,
        description: 'sample rule ' + newName,
        code: newCode,
        id: (vc.rules_library().length + 1)
      }

      const status = await this[types.addRule](ruleData)

      if (status === reqStatus.success) {
        console.log('add rule success')
      } else {
        console.log('add rule error')
      }
    },
    addToAccountVariant (name, code, i) {
      const vc = this
      if (confirm('Add ' + name + ' to account variant')) {
        vc.draggable_rules.push(vc.rules_library()[i])
      }
    },
    removeFromAccountVariant (i) {
      const vc = this
      if (vc.draggable_rules.length > 1) {
        vc.draggable_rules.splice(i, 1)
      } else {
        alert('There must be at least 1 rule')
      }
    },
    updateSelectedIndex (i) {
      const vc = this
      vc.selected_rule_index = i
      console.log('selected index: ' + i)
    },
    fetchRules () {
      const vc = this
      vc.fetchRulesLibrary()
      vc.fetchAccountVariantRules()
    },
    async testSingleRule (index) {
      const vc = this

      const ruleToTest = vc.draggable_rules[index]
      const orderId = prompt('Please enter order ID')
      const dataObject = { orderId, ruleToTest }

      const status = await this[types.getTestingOutput](dataObject)

      if (status === reqStatus.success) {
        console.log('testSingleRule success')
      }
    },
    async cancelRuleEdition (index) {
      const vc = this
      vc.draggable_rules[index].code = await this[types.setRuleCode](index)
    },
    cancelSequenceEdition () {
      const vc = this
      vc.fetchAccountVariantRules()
    }
  }
}
</script>
<style scoped>
  .draggable-item {
    padding: 10px;
  }
</style>
