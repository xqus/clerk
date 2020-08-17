Vue.component('subscription-management', require('./components/SubscriptionManagement.vue').default);

import Polyglot from "vue-polyglot";

Vue.use(Polyglot, {
    defaultLanguage: "en",
    languagesAvailable: ["nb"]
});



Vue.locales({
    nb: {
        managesubscription: "Ditt abonnement"
    }
});
