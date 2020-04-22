<template>
  <div class="document">
    <img
      v-for="(page, i) in pages"
      :key="i"
      class="document__page"
      :src="page"
      alt="document page"
    >
  </div>
</template>

<script>
export default {
  name: 'DetailsDocument',

  data: () => ({
    pages: [
      'https://www.freightquote.com/en-US/-/media/Freightquote/Resources/bol_website_2016.jpg?h=716&w=750&la=en-US'
    ]
  })
}
</script>

<style lang="scss" scoped>
.document {
  height: 100vh;
  overflow-y: auto;
  display: flex;
  flex-grow: 1;
  justify-content: center;
  align-items: flex-start;
  padding: 2.6rem 0;
  background: map-get($colors , grey-8);
}

.document__page {
  width: 90%;
  object-fit: contain;
}
</style>
