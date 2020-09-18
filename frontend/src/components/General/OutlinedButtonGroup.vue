<template>
  <div>
    <!-- <v-btn-toggle v-model="toggle_none">
      <v-btn
        class="ma-2"
        outlined
        color="indigo"
      >
        Outlined Button
      </v-btn>
      <v-btn
        class="ma-2"
        outlined
        color="indigo"
      >
        i
      </v-btn>
    </v-btn-toggle> -->
    <v-row>
      <v-btn
        outlined
        color="indigo"
      >
        View
      </v-btn>
      <v-btn
        class='selector'
        outlined
        color="indigo"
      >
        ▼
      </v-btn>
    </v-row>
  </div>
</template>
<script>
export default {
  name: 'OutlinedButtonGroup'
}
</script>
<style scoped>
.selector{
    padding-left: -10px
}
</style>
