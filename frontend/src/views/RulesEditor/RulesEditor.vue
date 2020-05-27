<template>
  <div>
    <v-btn
      @click="addSnippet()"
    >
      Add Rule
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
        <v-btn
          @click="testSnippet(index)"
        >
          Test
        </v-btn>
        <codemirror
          ref="cmEditor"
          v-model="rules_array[index].code"
          :options="cmOptions"
          @input="onCmCodeChange(index)"
        />
        <!-- Rule description -->
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
    // Mocked rules
    rules_array: [
      {
        name: 'Rule 1',
        description: 'Mocked description for Rule 1',
        code: 'possible_output_fields = {"shipment_type":{"always":True}}\ndef runrule(input_fields, updated_fields):\n\t#return {"shipment_type":"export"}\n\tupdated_fields["shipment_type"] = "export"'
      },
      {
        name: 'Rule 2',
        description: 'Mocked description for Rule 2',
        code: 'possible_output_fields = {"mbol":{"always":True}}\ndef runrule(input_fields, updated_fields):\n\t#return {"shipment_type":"export"}\n\tupdated_fields["bol"] = input_fields["mbol"]'
      }
    ]
  }),
  methods: {
    onCmCodeChange (index) {
      const vc = this
      console.log('code snippet: ')
      console.log(JSON.stringify(vc.rules_array[index]))
      console.log('after key line isnt how i want')
      console.log(vc.rules_array[index])
    },
    addSnippet () {
      const vc = this
      vc.rules_array.push({
        name: 'Default Name',
        description: 'Edit your description by clicking the button to the right',
        code: '# This is Python code'
      })
    },
    removeSnippet (index) {
      const vc = this
      vc.rules_array.splice(index, 1)
    },
    testSnippet (index) {
      const vc = this
      console.log(vc.rules_array[index])
    },
    editRuleName (index) {
      const vc = this
      const newName = prompt('Please type the new name for the rule')
      vc.rules_array[index].name = newName
    },
    editRuleDescription (index) {
      const vc = this
      const newDescription = prompt('Please type the new description for the rule')
      vc.rules_array[index].description = newDescription
    }
  }
}
</script>
<style scoped>
  .code-snippet {
    margin: 0 auto;
  }
  .snippet-div {
    margin: 30px;
    padding: 50px;
    border-style: solid;
    border-width: 5px;
  }
</style>
