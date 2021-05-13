import { getCompanies } from '@/store/api_calls/companies'

export default {
  data: () => ({
    companies: []
  }),

  methods: {
    async fetchCompanies () {
      const [error, data] = await getCompanies()

      if (error !== undefined) {
        return
      }

      this.companies = data.data
    }
  }
}
