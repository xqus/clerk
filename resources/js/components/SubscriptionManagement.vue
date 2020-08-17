<template>
    <div>
        <h3>{{$t('managesubscription', 'Manage your subscription')}}</h3>

        <label>{{$t('cardholdername', 'Card holder name')}}</label>
        <input id="card-holder-name" type="text" v-model="name" class="form-control mb-2">

        <label>{{$t('card', 'Card')}}</label>
        <div id="card-element">

        </div>

        <button class="btn btn-primary mt-3" id="add-card-button" v-on:click="submitPaymentMethod()">
            {{$t('savepayment', 'Save payment method')}}
        </button>

        <div class="mt-3 mb-3">
            OR
        </div>

        <div v-show="paymentMethodsLoadStatus == 2
            && paymentMethods.length == 0"
             class="">
            {{$t('nopaymentmethod', 'No payment method on file, please add a payment method.')}}
        </div>

        <div v-show="paymentMethodsLoadStatus == 2
                && paymentMethods.length > 0">
            <div v-for="(method, key) in paymentMethods"
                 v-bind:key="'method-'+key"
                 v-on:click="paymentMethodSelected = method.id"
                 class="border rounded row p-1"
                 v-bind:class="{
                    'bg-success text-light': paymentMethodSelected == method.id
                }">
                <div class="col-2">
                    {{ method.brand.charAt(0).toUpperCase() }}{{ method.brand.slice(1) }}
                </div>
                <div class="col-7">
                    Ending In: {{ method.last_four }} Exp: {{ method.exp_month }} / {{ method.exp_year }}
                </div>
                <div class="col-3">
                    <span v-on:click.stop="removePaymentMethod( method.id )">Remove</span>
                </div>
            </div>
        </div>

        <h4 class="mt-3 mb-3">Select Subscription</h4>

        <div  v-for="(product, productid) in this.subscriptionPlans">
            <h5 class="mt-3 mb-3">{{product.title}}</h5>
            <div v-for="(plan, index) in product.plans" class="mt-3 row rounded border p-1"
                  v-bind:class="{'bg-success text-light': selectedPlan == index}"
                  v-on:click="selectedPlan = index">
                <div class="col-6">
                    {{ plan.interval }}
                </div>
                <div class="col-6">
                    {{ plan.price }}
                </div>
            </div>
            <button class="btn btn-primary mt-3" id="add-card-button" v-on:click="updateSubscription(productid)">
                Subscribe
            </button>
        </div>


    </div>
</template>

<script>
    export default {
        name: 'subscription-management',
        props: ['apitoken'],

        data(){
            return {
                stripe: '',
                elements: '',
                card: '',

                intentToken: '',

                name: '',
                addPaymentStatus: 0,
                addPaymentStatusError: '',

                paymentMethods: [],
                paymentMethodsLoadStatus: 0,
                paymentMethodSelected: {},

                subscriptionPlans: {},
                selectedPlan: '',
                product: ''
            }
        },



        mounted(){
            this.includeStripe('js.stripe.com/v3/', function(){
                this.configureStripe();
            }.bind(this) );

            this.loadIntent();

            this.loadPaymentMethods();

            this.loadSubscriptionPlans();
        },

        methods: {
            /*
                Includes Stripe.js dynamically
            */
            includeStripe( URL, callback ){
                var documentTag = document, tag = 'script',
                    object = documentTag.createElement(tag),
                    scriptTag = documentTag.getElementsByTagName(tag)[0];
                object.src = '//' + URL;
                if (callback) { object.addEventListener('load', function (e) { callback(null, e); }, false); }
                scriptTag.parentNode.insertBefore(object, scriptTag);
            },

            /*
                Configures Stripe by setting up the elements and
                creating the card element.
            */
            configureStripe(){
                this.stripe = Stripe( this.apitoken );

                this.elements = this.stripe.elements();
                this.card = this.elements.create('card');

                this.card.mount('#card-element');
            },

            /*
                Loads the payment intent key for the user to pay.
            */
            loadIntent() {
                axios.get('/api/v1/user/setup-intent')
                    .then( function( response ){
                        this.intentToken = response.data;
                    }.bind(this));
            },

            loadSubscriptionPlans() {
                axios.get('/api/v1/user/subscription-plans')
                    .then( function( response ){
                        this.subscriptionPlans = response.data;
                    }.bind(this));
            },

            /*
                Uses the intent to submit a payment method
                to Stripe. Upon success, we save the payment
                method to our system to be used.
            */
            submitPaymentMethod(){
                this.addPaymentStatus = 1;

                this.stripe.confirmCardSetup(
                    this.intentToken.client_secret, {
                        payment_method: {
                            card: this.card,
                            billing_details: {
                                name: this.name
                            }
                        }
                    }
                ).then(function(result) {
                    if (result.error) {
                        this.addPaymentStatus = 3;
                        this.addPaymentStatusError = result.error.message;
                    } else {
                        this.savePaymentMethod( result.setupIntent.payment_method );
                        this.addPaymentStatus = 2;
                        this.card.clear();
                        this.name = '';
                    }
                }.bind(this));
            },


            /*
                Saves the payment method for the user and
                re-loads the payment methods.
            */
            savePaymentMethod( method ){
                axios.post('/api/v1/user/payments', {
                    payment_method: method
                }).then( function(){
                    this.loadPaymentMethods();
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
                        // this.setDefaultPaymentMethod();
                    }.bind(this));
            },

            removePaymentMethod( paymentID ){
                axios.post('/api/v1/user/remove-payment', {
                    id: paymentID
                }).then( function( response ){
                    this.loadPaymentMethods();
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
        }
    }
</script>

<style scoped>

</style>
