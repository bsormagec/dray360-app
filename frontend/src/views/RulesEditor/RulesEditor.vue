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
                      v-for="(rule, index) in account_variant_rules"
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
                  v-if="account_variant_rules.length > 0"
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
            v-if="account_variant_rules.length > 0"
            class="card-body"
          >
            <codemirror
              v-if="account_variant_rules.length > 0"
              ref="cmEditor"
              v-model="account_variant_rules[selected_rule_index].code"
              :options="cmOptions"
            />
          </div>
          <vue-json-pretty
            v-if="testing_output"
            :path="'res'"
            :data="{testing_output}"
            @click="handleClick"
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
                v-model="account_variant_rules"
                group="rules"
                @start="drag=true"
                @end="drag=false"
              >
                <v-list-item
                  v-for="(rule, i) in account_variant_rules"
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
const axios = require('axios')
export default {
  name: 'RulesEditor',
  components: {
    draggable,
    codemirror,
    VueJsonPretty
  },
  data: () => ({
    ...mapState(rulesLibrary.moduleName, {
      // list: state => listFormat(state.list, (id) => this.$router.push(`/order/${id}`)),
      rules_library: state => state.rules_library
    }),

    cmOptions: {
      tabSize: 4,
      mode: 'text/x-python',
      theme: 'base16-light',
      lineNumbers: true,
      line: true
    },
    // Account / Variant Rules
    account_variant_rules: [],
    // Selected rule
    selected_rule_index: 0,
    // Testing output
    testing_output: null
  }),
  async mounted () {
    const vc = this
    // await vc.fetchRules()
    await vc.fetchRulesLibrary()
  },
  methods: {
    ...mapActions(rulesLibrary.moduleName, [types.getLibrary]),

    async fetchRulesLibrary () {
      console.log('fetchrules library')
      const status = await this[types.getLibrary]()

      if (status === reqStatus.success) {
        console.log('success')
      } else {
        console.log('error')
      }
    },
    editRule (index) {
      const vc = this
      const baseURL = `${process.env.VUE_APP_APP_URL}`
      const ruleId = vc.account_variant_rules[index].id
      const ruleName = vc.account_variant_rules[index].name
      axios.put(baseURL + '/api/ocr/rules/' + ruleId, {
        code: vc.account_variant_rules[index].code,
        description: 'sample rule ' + ruleName,
        id: ruleId,
        name: ruleName
      })
        .then(function (response) {
          alert('Rule sequence saved')
        })
        .catch(function (error) {
          alert(error)
        })
    },
    saveRuleSequence () {
      const vc = this
      const baseURL = `${process.env.VUE_APP_APP_URL}`
      const idsToSave = []
      vc.account_variant_rules.forEach(rule => idsToSave.push(rule.id))

      // Cushing/JetSpeed ID is 1-1 in local and 8-8 in prod
      axios.post(baseURL + '/api/ocr/rules-assignment', {
        account_id: 8,
        variant_id: 8,
        rules: idsToSave
      })
        .then(function (response) {
          alert('Rule sequence saved')
        })
        .catch(function (error) {
          alert(error)
        })
    },
    // THIS IS USEFUL FOR DEBUGGING PURPOSES
    // onCmCodeChange (index) {
    //   const vc = this
    //   // vc.account_variant_rules[index].code = JSON.stringify(vc.account_variant_rules[index].code)
    //   console.log('oncmcodechange')
    //   console.log(vc.account_variant_rules[index].code)
    // },
    addRuleToLibrary () {
      const baseURL = `${process.env.VUE_APP_APP_URL}`
      const vc = this
      const newName = prompt('Please type the name of the new rule')
      let newCode = null
      if (newName !== null) {
        newCode = prompt('Please paste the code for the rule')
      }
      axios.post(baseURL + '/api/ocr/rules', {
        code: newCode,
        description: 'sample rule ' + newName,
        id: (vc.rules_array.length + 1),
        name: newName
      })
        .then(function (response) {
          vc.fetchRules()
          alert(newName + ' added successfully to the library!')
        })
        .catch(function (error) {
          console.log(error)
        })
    },
    addToAccountVariant (name, code, i) {
      const vc = this
      if (confirm('Add ' + name + ' to account variant')) {
        vc.account_variant_rules.push(vc.rules_array[i])
      }
    },
    removeFromAccountVariant (i) {
      const vc = this
      if (vc.account_variant_rules.length > 1) {
        vc.account_variant_rules.splice(i, 1)
      } else {
        alert('There must be at least 1 rule')
      }
    },
    updateSelectedIndex (i) {
      const vc = this
      vc.selected_rule_index = i
    },
    fetchRules () {
      const baseURL = `${process.env.VUE_APP_APP_URL}`
      const vc = this
      axios.get(baseURL + '/api/ocr/rules')
        .then(function (response) {
          vc.rules_array = response.data.data
        })
        .catch(function (error) {
          alert(error)
        })

      // Cushing/JetSpeed ID is 1-1 in local and 8-8 in prod
      axios.get(baseURL + '/api/ocr/rules-assignment?account_id=8&variant_id=8')
        .then(function (response) {
          vc.account_variant_rules = response.data.data
        })
        .catch(function (error) {
          alert(error)
        })
    },
    testSingleRule (index) {
      const vc = this
      const baseURL = `${process.env.VUE_APP_APP_URL}`
      let fetchedOcrData = []
      const orderId = prompt('Please enter order ID')
      axios.get(baseURL + '/api/orders/' + orderId)
        .then(function (response) {
          fetchedOcrData = response.data.ocr_data
          delete fetchedOcrData.fields_overwritten

          fetchedOcrData.rules = vc.account_variant_rules[index]

          const testedRuleName = fetchedOcrData.rules.name
          const testedRuleCode = fetchedOcrData.rules.code

          fetchedOcrData.rules = []
          fetchedOcrData.rules.push({
            [testedRuleName]: testedRuleCode
          })
          fetchedOcrData[testedRuleName] = testedRuleCode

          axios.post('https://i0mgwmnrb1.execute-api.us-east-2.amazonaws.com/default/ocr-rules-engine-dev',
            fetchedOcrData)
            .then(function (response) {
              vc.testing_output = response.data
            })
            .catch(function (error) {
              alert(error)
            })
        })
        .catch(function (error) {
          alert(error)
        })
    },
    cancelRuleEdition (index) {
      const vc = this
      const baseURL = `${process.env.VUE_APP_APP_URL}`
      axios.get(baseURL + '/api/ocr/rules-assignment?account_id=1&variant_id=1')
        .then(function (response) {
          vc.account_variant_rules[index].code = response.data.data[index].code
        })
        .catch(function (error) {
          alert(error)
        })
    },
    cancelSequenceEdition () {
      const vc = this
      const baseURL = `${process.env.VUE_APP_APP_URL}`
      axios.get(baseURL + '/api/ocr/rules-assignment?account_id=1&variant_id=1')
        .then(function (response) {
          vc.account_variant_rules = response.data.data
        })
        .catch(function (error) {
          alert(error)
        })
    }
  }
}
</script>
<style scoped>
  .draggable-item {
    padding: 10px;
  }
</style>
