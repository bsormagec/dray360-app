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
                <v-overflow-btn
                  class="my-2"
                  :items="rules_array"
                  label="Select Rule"
                  target="#dropdown-example"
                />
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
                  @click="testRule(index)"
                >
                  Cancel
                </v-btn>
                <v-btn
                  @click="testRule(index)"
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
                  <v-list-item-title v-text="rule.name" />
                </v-list-item-content>
                ->
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
              v-model="rules_array[0].code"
              :options="cmOptions"
              @input="onCmCodeChange(0)"
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
                  <v-list-item-content style="padding: 10px;">
                    <v-list-item-title v-text="rule.name" />
                  </v-list-item-content>
                </v-list-item>
              </draggable>
            </v-list-item-group>
          </v-list>
        </v-card>
      </div>
    </div>
  </div>
  <!-- <div>
    <v-btn
      @click="addSnippet()"
    >
      Add Rule
    </v-btn>
    <v-btn
      @click="testSnippet(index)"
    >
      Test
    </v-btn>
    <draggable
      v-model="rules_array"
      group="snippets"
      @start="drag=true"
      @end="drag=false"
    >
      <div
        v-for="(element, index) in rules_array"
        :key="element.id"
        class="snippet-div"
      >
        <h2>
          {{ element.name }}
        </h2>
        <v-btn
          @click="editRuleName(index)"
        >
          Edit Rule Name
        </v-btn>
        <v-btn
          @click="removeSnippet(index)"
        >
          Remove Rule
        </v-btn>
        <codemirror
          ref="cmEditor"
          v-model="rules_array[index].code"
          :options="cmOptions"
          @input="onCmCodeChange(index)"
        />

  <div>
    {{ element.description }}
    <v-btn
      @click="editRuleDescription(index)"
    >
      Edit Rule Description
    </v-btn>
  </div>
  </div>
  </draggable>
  </div> -->
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
    // Mocked rules
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
  updated () {
    console.log('selected rule: ' + this.selected_rule)
  },
  methods: {
    saveRule (index) {
      alert(this.rules_array[index].name)
    },
    // onCmCodeChange (index) {
    //   const vc = this
    //   console.log('code snippet: ')
    //   console.log(JSON.stringify(vc.rules_array[index]))
    //   console.log('after key line isnt how i want')
    //   console.log(vc.rules_array[index])
    // },
    addRuleToLibrary () {
      const vc = this
      const newName = prompt('Please type the name of the new rule')
      vc.rules_array.push({
        name: newName,
        code: '# This is Python code'
      })
    }
    // removeSnippet (index) {
    //   const vc = this
    //   vc.rules_array.splice(index, 1)
    // },
    // editRuleName (index) {
    //   const vc = this
    //   const newName = prompt('Please type the new name for the rule')
    //   vc.rules_array[index].name = newName
    // },
    // editRuleDescription (index) {
    //   const vc = this
    //   const newDescription = prompt('Please type the new description for the rule')
    //   vc.rules_array[index].description = newDescription
    // }
  }
}
</script>
<style scoped>
  /* .code-snippet {
    margin: 0 auto;
  }
  .snippet-div {
    margin: 30px;
    padding: 50px;
    border-style: solid;
    border-width: 5px;
  } */
</style>
