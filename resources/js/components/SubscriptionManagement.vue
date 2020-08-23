<template>
    <div>

    </div>
</template>

<script>
    export default {
        name: 'subscription-management',
        data(){
            return {
                paymentMethods: [],
                paymentMethodsLoadStatus: 0,

                subscriptionPlans: {},
                selectedPlan: '',
                product: ''
            }
        },



        mounted (){
            this.loadPaymentMethods();
            this.loadSubscriptionPlans();
        },

        methods: {

            loadSubscriptionPlans() {
                axios.get('/api/v1/user/subscription-plans')
                    .then( function( response ){
                        this.subscriptionPlans = response.data;
                    }.bind(this));
            },

            /*
                Loads all of the payment methods for the
                user.
            */
            loadPaymentMethods(){
                this.paymentMethodsLoadStatus = 1;

                axios.get('/api/v1/user/payment-methods')
                    .then( function( response ){
                        this.paymentMethods = response.data;
                        this.paymentMethodsLoadStatus = 2;
                    }.bind(this));
            },

            updateSubscription(productid){
                axios.put('/api/v1/user/subscription', {
                    plan: this.selectedPlan,
                    product: productid,
                }).then( function( response ){
                    console.log( response );
                }.bind(this));
            },
            cardClass(brand) {
                return 'fab fa-cc-'+brand;
            }
        }
    }
</script>

<style scoped>

</style>
