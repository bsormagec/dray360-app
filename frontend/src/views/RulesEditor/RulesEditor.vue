<template>
  <div>
    <draggable
      v-model="codeSnippets"
      group="people"
      @start="drag=true"
      @end="drag=false"
    >
      <div
        v-for="element in codeSnippets"
        :key="element.id"
        class="snippet-div"
      >
        <codemirror
          ref="cmEditor"
          :value="element"
          :options="cmOptions"
          @ready="onCmReady"
          @focus="onCmFocus"
          @input="onCmCodeChange"
        />
      </div>
    </draggable>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import { codemirror } from 'vue-codemirror'
// import base style
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
    codeSnippets: ["# This program adds two numbers \n num1 = 1.5 \n num2 = 6.3 \n # Add two numbers \n sum = num1 + num2 \n \n # Display the sum \n print('The sum of {0} and {1} is {2}'.format(num1, num2, sum)", 'if (year % 4) == 0: \n  if (year % 100) == 0:'],
    cmOptions: {
      tabSize: 4,
      mode: 'text/x-python',
      theme: 'base16-light',
      lineNumbers: true,
      line: true
    }
  })
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
