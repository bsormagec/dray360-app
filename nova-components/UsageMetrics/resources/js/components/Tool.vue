<template>
    <div>
        <heading class="mb-6">Usage Metrics</heading>

        <div class="flex mb-4">
            <span class="flex items-center text-lg  mr-2">Company</span>
            <select
                class="form-control form-select"
                v-model="companyId"
            >
                <option v-for="company in companies" :key="company.id" :value="company.id">{{company.name}}</option>
            </select>
            <span class="flex items-center text-lg ml-4 mr-2">Date Range</span>
            <date-range-picker
                class="mx-2"
                single-date-picker="range"
                :locale-data="{ firstDay: 0, format: 'mm/dd/yyyy' }"
                show-dropdowns
                :ranges="false"
                control-container-class="form-input form-input-bordered h-full flex items-center"
                v-model="dateRange"
                auto-apply
            />
            <button
                type="submit"
                class="btn btn-default btn-primary inline-flex items-center relative" dusk="update-button"
                @click="search"
                :disabled="loading"
            >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Go
            </button>
        </div>
        <div class="max-w-full flex flex-wrap flex-row space-between -mx-2">
            <Card label="Number of requests" :metric="report.requests"/>
            <Card label="Number of PDF requests" :metric="report.pdf_requests"/>
            <Card label="Number of datafile requests" :metric="report.datafile_requests" />
            <Card label="Number of rejected requests" :metric="report.rejected_requests" />
            <Card label="Number of orders" :metric="report.orders" />
            <Card label="Number of orders from pdf" :metric="report.orders_from_pdf" />
            <Card label="Number of orders from datafile" :metric="report.orders_from_datafile" />
            <Card label="Number of TMS shipments" :metric="report.tms_shipments" />
            <Card label="Number of pages" :metric="report.pages" />
        </div>


    </div>
</template>

<script>
import DateRangePicker from 'vue2-daterange-picker'
import Card from './Card'
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'


export default {
    components: { DateRangePicker, Card },
    data: () =>({
        companies: [],
        companyId: undefined,
        dateRange: {startDate: null, endDate: null},
        report: {},
        loading: false,
    }),
    async mounted() {
        this.loading = true
        const response =  await Nova.request().get('/api/companies')
        this.loading = false

        if (response.status !== 200) {
            alert('There was an issue retrieving the companies')
            return
        }

        this.companies = response.data.data
            .map(item => ({ id: item.id, name: item.name }))
            .sort((a,b) => {
                if (a.name === b.name) {
                    return 0
                }

                return a.name > b.name ? 1 : -1
            })
    },

    methods: {
        async search() {
            if (!this.companyId) {
                alert('Please select a company')
                return
            }
            if (!this.dateRange.startDate || !this.dateRange.endDate) {
                alert('Please selected a valid date range')
                return
            }

            const params = {
                start_date: this.dateRange.startDate.toISOString(),
                end_date: this.dateRange.endDate.toISOString()
            }

            this.loading = true
            await Nova.request().get(`/nova-vendor/usage-metrics/companies/${this.companyId}?${this.toParams(params)}`)
                .then(({data}) => {
                    this.loading = false
                    this.report = data.data
                })
                .catch(({response}) => {
                    this.loading = false
                    if (response.status === 422) {
                        alert('Please validate your date range selection')
                        return
                    }
                })
        },
        toParams(objParams) {
            const params = []

            Object.keys(objParams).forEach((key) => {
                if (!objParams[key]) return
                params.push(`${key}=${encodeURIComponent(objParams[key])}`)
            })

            return params.join('&')
        }
    }
}
</script>

<style lang="scss" scoped>
.vue-daterange-picker {
    min-width: 212px;
}
.report-card {
    width: calc(1/4 * 100%);
    display: flex;
    flex-direction: column;

    margin: 0.5rem 0.5rem;

    padding: 0 1rem;

}

.animate-spin {
    animation: spin 1s linear infinite!important;
}


</style>
