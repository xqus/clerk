<template>
    <div>
        <div v-show="paymentMethodsLoadStatus == 2
                && paymentMethods.length > 0">

            <div v-for="(method, key) in paymentMethods"
                 v-bind:key="'method-'+key">
                <h5 class="text-lg">
                    <svg class="fill-current text-teal-500 inline-block h-12 w-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M18 6V4H2v2h16zm0 4H2v6h16v-6zM0 4c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm4 8h4v2H4v-2z"/>
                    </svg>
                    **** **** **** {{ method.last_four }}
                    <small>{{ method.exp_month }} / {{ method.exp_year }}</small>
                </h5>
                <button v-on:click.stop="removePaymentMethod( method.id )" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{__('Delete my credit card')}}
                </button>
            </div>
        </div>
        <h5 class="mt-2 mb-2 font-weight-light">{{ __('Add / update credit card') }}</h5>
        <hr>
        <p><small>
            {{__("Want to update the credit card that we have on file? Provide the new details here. And don't worry; your card information will never directly touch our servers.")}}
        </small></p>

        <div id="card-element" class="bg-light mt-2 ml-4 mr-4"></div>

        <div class="form-group row pt-2">
            <label for="card-holder-name" class="col-md-4 col-form-label text-md-right">{{ __('Name on card') }}</label>
            <div class="col-md-6">
                <input v-model="name" id="card-holder-name" type="text">
            </div>
        </div>

        <button class="btn btn-success offset-md-4 mt-2" id="add-card-button" v-on:click="submitPaymentMethod()">
            {{ __('Update credit card') }}
        </button>
        <section class="text-gray-700 body-font overflow-hidden">
            <div v-for="(product, productid) in this.subscriptionPlans" class="container px-5 py-24 mx-auto">
                <h2 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">{{product.title}}</h2>
                <div class="flex flex-wrap -m-4">
                    <div v-for="(plan, index) in product.plans" class="p-4 xl:w-1/4 md:w-1/2 w-full"
                         v-on:click="selectedPlan = index"
                    >

                        <div class="h-full p-6 rounded-lg border-2 border-gray-300 flex flex-col relative overflow-hidden"
                             v-bind:class="{'border-indigo-500': selectedPlan == index}"
                        >
                            <h2 class="text-sm tracking-widest title-font mb-1 font-medium">START</h2>
                            <h1 class="text-5xl text-gray-900 pb-4 mb-4 border-b border-gray-200 leading-none">
                                {{plan.price}}
                            </h1>
                            <span class="text-lg ml-1 font-normal text-gray-500">{{plan.interval}}</span>
                            <p class="flex items-center text-gray-600 mb-2">
                                <span class="w-4 h-4 mr-2 inline-flex items-center justify-center bg-gray-500 text-white rounded-full flex-shrink-0">
                                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" class="w-3 h-3" viewBox="0 0 24 24">
                                        <path d="M20 6L9 17l-5-5"></path>
                                    </svg>
                                </span>
                                Vexillologist pitchfork
                            </p>
                            <button class="flex items-center mt-auto text-white bg-gray-500 border-0 py-2 px-4 w-full focus:outline-none hover:bg-gray-600 rounded">Button
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-auto" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <p class="text-xs text-gray-500 mt-3">Literally you probably haven't heard of them jean shorts.</p>
                        </div>
                    </div>
                </div>
                <div v-show="paymentMethodsLoadStatus == 2 && paymentMethods.length == 0">
                    No payment method on file, please add a payment method.
                </div>
                <button
                    v-show="paymentMethodsLoadStatus == 2 && paymentMethods.length > 0"
                    class="btn btn-primary mt-3"
                    id="add-card-button"
                    v-on:click="updateSubscription(productid)">
                    {{ __('Update subscription') }}
                </button>
            </div>
        </section>
    </div>
</template>

<script>
    import scaffold from "../app";

    export default {
        name: 'payment-method',
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
            this.loadSubscriptionPlans();
            this.loadPaymentMethods();
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

            loadSubscriptionPlans() {
                axios.get('/api/v1/user/subscription-plans')
                    .then( function( response ){
                        this.subscriptionPlans = response.data;
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
