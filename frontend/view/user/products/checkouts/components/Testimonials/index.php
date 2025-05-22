<div id="rootProductCheckoutTestimonials" data-testimonials='<?= json_encode($testimonials) ?>'></div>

<script type="text/babel">
    window.addEventListener('load', function() {
        <?= import(base_path('frontend/view/user/products/checkouts/components/Testimonials/EditTestimonial.js.php')) ?>
        <?= import(base_path('frontend/view/user/products/checkouts/components/Testimonials/Testimonials.js.php')) ?>
        <?= import(base_path('frontend/view/user/products/checkouts/components/Testimonials/Index.js.php')) ?>
        const div = document.getElementById("rootProductCheckoutTestimonials");
        if (!div) throw new Error('Element #rootProductCheckoutTestimonials not found.');
        const testimonials = JSON.parse(div.dataset.testimonials);
        const {product_id, checkout_id} = SiteScope.params();
        const productID = product_id;
        const checkoutID = checkout_id;

        // ReactDOM.render(
        //     <Index testimonials={testimonials} productID={productID} checkoutID={checkoutID} />,
        //     div
        // );
        
        let root = ReactDOM.createRoot(div)
        root.render(
            <Index testimonials={testimonials} productID={productID} checkoutID={checkoutID} />
        );
    });
</script>
