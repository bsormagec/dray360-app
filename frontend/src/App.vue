<template>
  <v-app>
    <router-view />
  </v-app>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style lang="scss">
html {
  font-size: map-get($font, base-size) !important;
}

body {
  font-size: 1.6rem;
}

h1 {
  font-size: 2.6rem;
}

h2 {
  font-size: 1.6rem;
}

div,
span,
p,
label,
td {
  font-size: 1.2rem !important;
  color: map-get($colors, black);
}

.v-data-table {
  border-radius: unset;
  box-shadow: map-get($properties, table-shadow);

  * {
    border: unset !important;
  }

  td {
    border: 0.1rem solid #dadddd;
    border-right: unset;
    border-bottom: unset;
  }

  .v-data-footer {
    border: unset !important;
  }
}
</style>
