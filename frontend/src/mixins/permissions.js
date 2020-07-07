import Vue from 'vue';

Vue.mixin({
    methods: {
        $can(permission) {
            return this.$store.state.currentUser?.can(permission);
        },
        $cannot(permission) {
            return this.$store.state.currentUser?.cannot(permission);
        },
        $canAny(...permissions) {
            return this.$store.state.currentUser?.canAny(...permissions);
        },
        $canAll(...permissions) {
            return this.$store.state.currentUser?.canAll(...permissions);
        },
    },
});
