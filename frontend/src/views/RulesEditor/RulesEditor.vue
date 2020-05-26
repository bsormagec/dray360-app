<template>
  <div>
    <v-btn
      @click="addSnippet()"
    >
      Add Rule
    </v-btn>
    <draggable
      v-model="codeSnippets"
      group="snippets"
      @start="drag=true"
      @end="drag=false"
    >
      <div
        v-for="(element, index) in codeSnippets"
        :key="element.id"
        class="snippet-div"
      >
        <v-btn
          @click="removeSnippet(index)"
        >
          Remove Rule
        </v-btn>
        <codemirror
          ref="cmEditor"
          v-model="codeSnippets[index]"
          :options="cmOptions"
          @input="onCmCodeChange(index)"
        />
        <!-- Rule description -->
        <div>
          Sums up two numbers. Prints the result
        </div>
      </div>
    </draggable>
  </div>
</template>
<script>
/* eslint-disable vue/valid-v-model */
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
    code: 'Hello world',
    codeSnippets: ["# This program adds two numbers\nnum1 = 1.5\nnum2 = 6.3\n# Add two numbers\nsum = num1 + num2\n\n# Display the sum\nprint('The sum of {0} and {1} is {2}'.format(num1, num2, sum)", 'if (year % 4) == 0:\n\tif (year % 100) == 0:'],
    cmOptions: {
      tabSize: 4,
      mode: 'text/x-python',
      theme: 'base16-light',
      lineNumbers: true,
      line: true
    }
  }),
  methods: {
    onCmCodeChange (index) {
      const vc = this
      console.log('code snippet: ')
      console.log(JSON.stringify(vc.codeSnippets[index]))
      console.log('after key line isnt how i want')
      console.log(vc.codeSnippets[index])
    },
    addSnippet () {
      const vc = this
      vc.codeSnippets.push('# Hello Python')
    },
    removeSnippet (index) {
      const vc = this
      vc.codeSnippets.splice(index, 1)
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
