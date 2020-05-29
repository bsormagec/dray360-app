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
                  @click="saveRule(selected_rule_index)"
                >
                  Save
                </v-btn>
                <v-btn
                  @click="testRule(selected_rule_index)"
                >
                  Cancel
                </v-btn>
                <v-btn
                  @click="testRule(selected_rule_index)"
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
              @click="saveRule(index)"
            >
              Save
            </v-btn>
            <v-btn
              @click="testRule(index)"
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
                v-for="(rule, i) in rules_array"
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
          <div class="card-body">
            <codemirror
              ref="cmEditor"
              v-model="account_variant_rules[selected_rule_index].code"
              :options="cmOptions"
              @input="onCmCodeChange(selected_rule_index)"
            />
          </div>
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
import draggable from 'vuedraggable'
import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/base16-light.css'
export default {
  name: 'RulesEditor',
  components: {
    draggable,
    codemirror
  },
  data: () => ({
    cmOptions: {
      tabSize: 4,
      mode: 'text/x-python',
      theme: 'base16-light',
      lineNumbers: true,
      line: true
    },
    // Mocked Rule Library
    rules_array: [
      {
        name: 'bol_to_mbol',
        code: 'possible_output_fields = {"shipment_type":{"always":True}}\ndef runrule(input_fields, updated_fields):\n\t#return {"shipment_type":"export"}\n\tupdated_fields["shipment_type"] = "export"'
      },
      {
        name: 'rule_2',
        code: 'possible_output_fields = {"mbol":{"always":True}}\ndef runrule(input_fields, updated_fields):\n\t#return {"shipment_type":"export"}\n\tupdated_fields["bol"] = input_fields["mbol"]'
      }
    ],
    // Account / Variant Rules
    account_variant_rules: [
      {
        name: 'bol_to_mbol',
        code: 'possible_output_fields = {"shipment_type":{"always":True}}\ndef runrule(input_fields, updated_fields):\n\t#return {"shipment_type":"export"}\n\tupdated_fields["shipment_type"] = "export"'
      },
      {
        name: '2_bol_to_mbol',
        code: '# Second bol to mbol'
      }
    ],
    // Selected rule
    selected_rule_index: 0
  }),
  methods: {
    saveRule (index) {
      console.log('selected index: ' + this.selected_rule_index)
      alert(this.account_variant_rules[index].name)
    },
    onCmCodeChange (index) {
      // const vc = this
      console.log('code snippet: ' + index)
      // console.log(JSON.stringify(vc.account_variant_rules[index]))
      // console.log('after key line isnt how i want')
      // console.log(vc.rules_array[index])
    },
    addRuleToLibrary () {
      const vc = this
      const newName = prompt('Please type the name of the new rule')
      const newCode = prompt('Please paste the code for the rule')
      vc.rules_array.push({
        name: newName,
        code: newCode
      })
    },
    addToAccountVariant (name, code, i) {
      const vc = this
      if (confirm('Add ' + name + ' to account variant')) {
        vc.rules_array.splice(i, 1)
        vc.account_variant_rules.push({
          name: name,
          code: code
        })
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
    }
  }
}
</script>
<style scoped>
  .draggable-item {
    padding: 10px;
  }
</style>
