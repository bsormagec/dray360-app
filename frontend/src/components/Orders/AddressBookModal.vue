<template>
  <div>
    <v-dialog
      v-model="dialog"
      max-width="70rem"
      scrollable
    >
      <v-card>
        <v-card-title>
          <h1>Addresses</h1>
          <v-spacer />
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            label="Search"
            outlined
            dense
            class="address__search"
          />
          <v-btn
            icon
            @click="dialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-data-table
          :headers="headers"
          :items="addressObject"
          :search="search"
          item-key:id
          :hide-default-header="true"
          :hide-default-footer="true"
          scrollable
        >
          <template
            slot="item"
            slot-scope="props"
          >
            <tr>
              <td class="fullAddress">
                <span class="city mb-3"><strong>{{ props.item.address.city }}</strong></span><br>
                <span class="managed">Managed by: <a
                  href=""
                  color="primary"
                >
                  {{ props.item.address.managedname }}</a></span><br>
                <span class="phone">
                  <v-icon
                    color="primary"
                    class="mr-3"
                    small
                  >
                    mdi-phone-outline
                  </v-icon>
                  {{ props.item.address.phone }}
                </span><br>
                <span class="email">
                  <v-icon
                    color="primary"
                    class="mr-3"
                    small
                  >mdi-email-outline</v-icon>
                  <a href="">{{ props.item.address.email }}</a></span>
              </td>
              <td class="col__icon">
                <v-icon
                  color="primary"
                >
                  mdi-map-marker-outline
                </v-icon>
              </td>
              <td>
                {{ props.item.fulladdress }}
              </td>
              <td>
                <v-btn
                  outlined
                  color="primary"
                  class="float-right"
                >
                  Select
                </v-btn>
              </td>
            </tr>
          </template>
        </v-data-table>
      </v-card>

      <template v-slot:activator="{ on }">
        <p>
          <a
            v-on="on"
          >
            Addres Book Modal
          </a>
        </p>
      </template>
    </v-dialog>
  </div>
</template>
<script>
export default {
  data () {
    return {
      dialog: false,
      search: '',
      headers: [
        { text: 'addressObject', value: 'city' },
        { text: 'addressObject', value: 'managedname' },
        { text: 'fulladdress', value: 'fulladdress' },
        { text: 'Actions', value: 'actions' }
      ],
      addressObject: [
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '3016 Loxley Lane Ladson, CA, 90210'

        },
        {
          address: { city: 'Atlanta', managedname: ' Mauricio', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '4220 Martin Luther King Drive Atlanta, GA 30336'

        },
        {
          address: { city: 'Baltimore', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'
        },
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'
        },
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'

        },
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'

        },
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'

        },
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'

        },
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'

        },
        {
          address: { city: 'Ladson', managedname: ' Seth Ling', phone: '(843) 697-8659', email: 'sling@abd-trucking.com' },
          fulladdress: '8208 Fischer Road Baltimore, MD 21222'

        }
      ]
    }
  }
}
</script>
<style lang="scss" scoped>
.address__search fieldset{
    height: 5.5rem;
}
.v-card__title{
    display: flex;
    align-items: baseline;
    border-bottom: 0.1rem solid map-get($colors, grey-11 );
    height: 8rem;
}
.v-data-table td {
    height: 10rem;
    width: 18rem;
}
.fullAddress{
    width: 25rem;
    margin: 3rem auto;
    span:last-child{
        width: 20rem !important;
        display: inline-block;

    }
}
.col__icon{
    width: 2rem !important;
    padding: 0rem !important;
}

</style>
