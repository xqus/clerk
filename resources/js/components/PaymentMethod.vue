<template>
    <div>
        <div v-show="paymentMethodsLoadStatus == 2
                && paymentMethods.length > 0">

            <div v-for="(method, key) in paymentMethods"
                 v-bind:key="'method-'+key">
                <h5><i :class="cardClass(method.brand)"></i> **** **** **** {{ method.last_four }} <small>{{ method.exp_month }} / {{ method.exp_year }}</small></h5>
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

        <h5 class="mt-2 mb-2 font-weight-light">{{ __('My subscription') }}</h5>
        <hr>
        <div v-for="(product, productid) in this.subscriptionPlans">
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
