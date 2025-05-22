App.Subdomains.Checkout.Thanks = class Thanks extends Page {
    context = 'public';
    title = 'Thanks';

    view(loaded) {
        return super.find(`subdomains/checkout/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    stripe() {
        // const { client_secret } = tagJSON('thanks_meta');
        // const { STRIPE_PUBKEY } = tagJSON('env');
    
        // const stripe = Stripe(
        //   STRIPE_PUBKEY, {
        //   apiVersion: '2023-10-16',
        // });

        // console.log('client_secret');
        // console.log(client_secret);

        // if (!client_secret) return;

        // const options = {
        //     clientSecret: client_secret,
        //     // Fully customizable with appearance API.
        //     appearance: {/*...*/},
        //   };
          
        //   // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in a previous step
        //   const elements = stripe.elements(options);
          
        //   // Create and mount the Payment Element
        //   const paymentElement = elements.create('payment');
        //   paymentElement.mount('#payment-element');
    }

    ready() {
        this.stripe();
    }
};