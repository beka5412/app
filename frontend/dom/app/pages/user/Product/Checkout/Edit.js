App.User.Product.Checkout.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar Checkout';
    className = 'App.User.Product.Checkout.Edit';

    reactTestimonials() {
        // alert('carregou')
        window.reactTestimonial = true
    }

    view(loaded, link) {
        let product_id = params().product_id;
        let checkout_id = params().checkout_id;
        return super.find(
            `${link?.full?'full/':''}user/product/${product_id}/checkout/${checkout_id}/${this?.constructor?.name||''}${this?.queryString||''}`, 
            loaded,
            () => {
                this.reactTestimonials()
            }
        );
    }

    /**
     * Esse escopo nao considera quando a pagina eh carregada via redirecionamento de single page
     * apenas quando a pagina carregar pela primeira vez
     */
    ready() {
        // do sutff ...
    }

    end() {
        // do sutff ...
        this.reactTestimonials()
    }

    checkoutOnSubmit() {
        let form = document.querySelector('.frm_edit_checkout');
        let name = form.querySelector('.inp_checkout_name');
        let darkmode = form.querySelector('.inp_checkout_darkmode');
        let top_banner = form.querySelector('.inp_checkout_top_banner');
        let sidebar_banner = form.querySelector('.inp_checkout_sidebar_banner');
        let footer_banner = form.querySelector('.inp_checkout_footer_banner');
        let top_2_banner = form.querySelector('.inp_checkout_top_2_banner');
        let logo = form.querySelector('.inp_checkout_logo');
        let favicon = form.querySelector('.inp_checkout_favicon');
        let top_color = form.querySelector('.inp_top_color');
        let primary_color = form.querySelector('.inp_primary_color');
        let secondary_color = form.querySelector('.inp_secondary_color');
        let countdown_enabled = form.querySelector('.inp_checkout_countdown_enabled');
        let countdown_text = form.querySelector('.inp_checkout_countdown_text');
        let countdown_time = form.querySelector('.inp_checkout_countdown_time');
        let countdown_color = form.querySelector('.inp_checkout_countdown_color');
        let theme = [...document.querySelectorAll('.options_checkout_theme [name="checkout_theme_selected"]')].filter(item => item.checked);
        let pix_enabled = document.querySelector('#checkoutPmPix');
        let credit_card_enabled = document.querySelector('#checkoutPmCreditCard');
        let billet_enabled = document.querySelector('#checkoutPmBillet');
        let pix_discount_enabled = document.querySelector('#checkoutPixDiscountEnabled');
        let pix_discount_amount = document.querySelector('#checkoutPixDiscountAmount');
        let credit_card_discount_enabled = document.querySelector('#checkoutCreditCardDiscountEnabled');
        let credit_card_discount_amount = document.querySelector('#checkoutCreditCardDiscountAmount');
        let billet_discount_enabled = document.querySelector('#checkoutBilletDiscountEnabled');
        let billet_discount_amount = document.querySelector('#checkoutBilletDiscountAmount');
        let max_installments = document.querySelector('#checkoutInstallmentsQtySelect');
        let pix_thanks_page_enabled = document.querySelector('#checkoutPixThanksPageEnabled');
        let pix_thanks_page_url = document.querySelector('#pixThanksPageURL');
        let credit_card_thanks_page_enabled = document.querySelector('#checkoutCreditCardThanksPageEnabled');
        let credit_card_thanks_page_url = document.querySelector('#checkoutCreditCardThanksPageURL');
        let billet_thanks_page_enabled = document.querySelector('#checkoutBilletThanksPageEnabled');
        let billet_thanks_page_url = document.querySelector('#checkoutBilletThanksPageURL');
        let set_as_default = form.querySelector('.inp_checkout_set_default');
        let status = form.querySelector('.inp_checkout_status');
        let notification_interested24_number = form.querySelector('.inp_notification_interested24_number');
        let notification_interested_weekly_number = form.querySelector('.inp_notification_interested_weekly_number');
        let notification_order24_number = form.querySelector('.inp_notification_order24_number');
        let notification_order_weekly_number = form.querySelector('.inp_notification_order_weekly_number');
        let notification_interested24_enabled = form.querySelector('.swt_notification_interested24_enabled');
        let notification_interested_weekly_enabled = form.querySelector('.swt_notification_interested_weekly_enabled');
        let notification_order24_enabled = form.querySelector('.swt_notification_order24_enabled');
        let notification_order_weekly_enabled = form.querySelector('.swt_notification_order_weekly_enabled');
        let whatsapp_number = form.querySelector('.inp_whatsapp_number');
        
        let data = {
            name: name?.value || '',
            darkmode: darkmode?.checked ? 1 : 0,
            top_banner: top_banner?.value || '',
            sidebar_banner: sidebar_banner?.value || '',
            footer_banner: footer_banner?.value || '',
            top_2_banner: top_2_banner?.value || '',
            logo: logo?.value || '',
            favicon: favicon?.value || '',
            top_color: top_color?.value || '',
            primary_color: primary_color?.value || '',
            secondary_color: secondary_color?.value || '',
            countdown_enabled: countdown_enabled?.checked ? 1 : 0,
            countdown_text: countdown_text?.value || '',
            countdown_time: countdown_time?.value || '',
            header_bg_color: header_bg_color?.value || '',
            header_text_color: header_text_color?.value || '',
            theme: theme[0]?.checked ? theme[0]?.getAttribute('data-id') : null,            
            pix_enabled: pix_enabled?.checked ? 1 : 0,
            credit_card_enabled: credit_card_enabled?.checked ? 1 : 0,
            billet_enabled: billet_enabled?.checked ? 1 : 0,
            pix_discount_enabled: pix_discount_enabled?.checked ? 1 : 0,
            pix_discount_amount: currencyToNumber(pix_discount_amount?.value || ''),
            credit_card_discount_enabled: credit_card_discount_enabled?.checked ? 1 : 0,
            credit_card_discount_amount: currencyToNumber(credit_card_discount_amount?.value || ''),
            billet_discount_enabled: billet_discount_enabled?.checked ? 1 : 0,
            billet_discount_amount: currencyToNumber(billet_discount_amount?.value || ''),
            max_installments: max_installments?.value || '',
            pix_thanks_page_enabled: pix_thanks_page_enabled?.checked ? 1 : 0,
            pix_thanks_page_url: pix_thanks_page_url?.value || '',
            credit_card_thanks_page_enabled: credit_card_thanks_page_enabled?.checked ? 1 : 0,
            credit_card_thanks_page_url: credit_card_thanks_page_url?.value || '',
            billet_thanks_page_enabled: billet_thanks_page_enabled?.checked ? 1 : 0,
            billet_thanks_page_url: billet_thanks_page_url?.value || '',
            default: set_as_default?.checked ? 1 : 0,
            status: status?.value || '',
            notification_interested24_number: parseInt(notification_interested24_number?.value || 0),
            notification_interested_weekly_number: parseInt(notification_interested_weekly_number?.value || 0),
            notification_order24_number: parseInt(notification_order24_number?.value || 0),
            notification_order_weekly_number: parseInt(notification_order_weekly_number?.value || 0),
            notification_interested24_enabled: notification_interested24_enabled?.checked ? 1 : 0,
            notification_interested_weekly_enabled: notification_interested_weekly_enabled?.checked ? 1 : 0,
            notification_order24_enabled: notification_order24_enabled?.checked ? 1 : 0,
            notification_order_weekly_enabled: notification_order_weekly_enabled?.checked ? 1 : 0,
            whatsapp_number: whatsapp_number?.value || ''
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let product_id = params().product_id;
        let checkout_id = params().checkout_id;
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            // if (body?.status == 'success') {
            //     let link = new Link;
            //     link.to(`${siteUrl()}/product/${product_id}/checkouts`);          
            // }
            
            const params = {
                v: Math.random() * 100000000000000000
            };
            let src = $('#iframeCheckoutPreview').attr('src');
            src = src.includes('?') ? src + '&' : src + '?';

            const url = src + $.param(params);
            $('#iframeCheckoutPreview').attr('src', url);
        });
    }

    checkoutUploadTopBanner() {
        let product_id = params().product_id;
        let checkout_id = params().checkout_id;

        let formData = new FormData();
        formData.append('image', document.getElementById('checkoutTopBanner')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        topLoading.style.display = 'inline';
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                // let area = document.querySelector('.img_product_image');
                // area.innerHTML = `<img src="${body.data.image}" />`;
                let inp_image = document.querySelector('.inp_checkout_top_banner');
                inp_image.value = body.data.image;
                imgTopBanner.src = siteUrl() + body.data.image;
                
                
                const params = {
                    top_banner: body.data.image
                };
                let src = $('#iframeCheckoutPreview').attr('src');
                src = src.includes('?') ? src + '&' : src + '?';

                const url = src + $.param(params);
                $('#iframeCheckoutPreview').attr('src', url);
            }
            else toastError(body.message);
        }).finally(onfinally => {
            topLoading.style.display = 'none';
        });
    }

    checkoutUploadSidebarBanner() {
        let product_id = params().product_id;
        let checkout_id = params().checkout_id;

        let formData = new FormData();
        formData.append('image', document.getElementById('checkoutSidebarBanner')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        sidebarLoading.style.display = 'inline';
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let inp_image = document.querySelector('.inp_checkout_sidebar_banner');
                inp_image.value = body.data.image;
                imgSidebarBanner.src = siteUrl() + body.data.image;
                
                
                const params = {
                    sidebar_banner: body.data.image
                };
                let src = $('#iframeCheckoutPreview').attr('src');
                src = src.includes('?') ? src + '&' : src + '?';

                const url = src + $.param(params);
                $('#iframeCheckoutPreview').attr('src', url);
            }
            else toastError(body.message);
        }).finally(onfinally => {
            sidebarLoading.style.display = 'none';
        });
    }

    checkoutUploadFooterBanner() {
        let product_id = params().product_id;
        let checkout_id = params().checkout_id;

        let formData = new FormData();
        formData.append('image', document.getElementById('checkoutFooterBanner')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        footerLoading.style.display = 'inline';
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let inp_image = document.querySelector('.inp_checkout_footer_banner');
                inp_image.value = body.data.image;
                imgFooterBanner.src = siteUrl() + body.data.image;
                
                
                const params = {
                    footer_banner: body.data.image
                };
                let src = $('#iframeCheckoutPreview').attr('src');
                src = src.includes('?') ? src + '&' : src + '?';

                const url = src + $.param(params);
                $('#iframeCheckoutPreview').attr('src', url);
            }
            else toastError(body.message);
        }).finally(onfinally => {
            footerLoading.style.display = 'none';
        });
    }

    checkoutUploadTop2Banner() {
        let product_id = params().product_id;
        let checkout_id = params().checkout_id;

        let formData = new FormData();
        formData.append('image', document.getElementById('checkoutTop2Banner')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        banner2Loading.style.display = 'inline';
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let inp_image = document.querySelector('.inp_checkout_top_2_banner');
                inp_image.value = body.data.image;
                imgTop2Banner.src = siteUrl() + body.data.image;
                
                
                const params = {
                    top_2_banner: body.data.image
                };
                let src = $('#iframeCheckoutPreview').attr('src');
                src = src.includes('?') ? src + '&' : src + '?';

                const url = src + $.param(params);
                $('#iframeCheckoutPreview').attr('src', url);
            }
            else toastError(body.message);
        }).finally(onfinally => {
            banner2Loading.style.display = 'none';
        });
    }

    checkoutUploadLogoBanner() {
        let product_id = params().product_id;
        let checkout_id = params().checkout_id;

        let formData = new FormData();
        formData.append('image', document.getElementById('checkoutLogoBanner')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        logoLoading.style.display = 'inline';
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let inp_image = document.querySelector('.inp_checkout_logo');
                inp_image.value = body.data.image;
                imgLogoBanner.src = siteUrl() + body.data.image;
                
                
                const params = {
                    logo: body.data.image
                };
                let src = $('#iframeCheckoutPreview').attr('src');
                src = src.includes('?') ? src + '&' : src + '?';

                const url = src + $.param(params);
                $('#iframeCheckoutPreview').attr('src', url);
            }
            else toastError(body.message);
        }).finally(onfinally => {
            logoLoading.style.display = 'none';
        });
    }

    checkoutUploadFaviconBanner() {
        let product_id = params().product_id;
        let checkout_id = params().checkout_id;

        let formData = new FormData();
        formData.append('image', document.getElementById('checkoutFaviconBanner')?.files[0] || '');

        let options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: formData
        };

        faviconLoading.style.display = 'inline';
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let inp_image = document.querySelector('.inp_checkout_favicon');
                inp_image.value = body.data.image;
                imgFaviconBanner.src = siteUrl() + body.data.image;
                
                
                const params = {
                    favicon: body.data.image
                };
                let src = $('#iframeCheckoutPreview').attr('src');
                src = src.includes('?') ? src + '&' : src + '?';

                const url = src + $.param(params);
                $('#iframeCheckoutPreview').attr('src', url);
            }
            else toastError(body.message);
        })
        .finally(onfinally => {
            faviconLoading.style.display = 'none';
        });
    }

    checkoutRemoveTopBanner() {
        document.querySelector('.inp_checkout_top_banner').value = '';
        checkoutTopBanner.value = '';
        imgTopBanner.src = imgTopBanner.getAttribute('default-img');
    }

    checkoutRemoveTop2Banner() {
        document.querySelector('.inp_checkout_top_2_banner').value = '';
        checkoutTop2Banner.value = '';
        imgTop2Banner.src = imgTop2Banner.getAttribute('default-img');
    }

    checkoutRemoveSidebarBanner() {
        document.querySelector('.inp_checkout_sidebar_banner').value = '';
        checkoutSidebarBanner.value = '';
        imgSidebarBanner.src = imgSidebarBanner.getAttribute('default-img');
    }

    checkoutRemoveFooterBanner() {
        document.querySelector('.inp_checkout_footer_banner').value = '';
        checkoutFooterBanner.value = '';
        imgFooterBanner.src = imgFooterBanner.getAttribute('default-img');
    }

    checkoutRemoveLogoBanner() {
        document.querySelector('.inp_checkout_logo').value = '';
        checkoutLogoBanner.value = '';
        imgLogoBanner.src = imgLogoBanner.getAttribute('default-img');
    }

    checkoutRemoveFaviconBanner() {
        document.querySelector('.inp_checkout_favicon').value = '';
        checkoutFaviconBanner.value = '';
        imgFaviconBanner.src = imgFaviconBanner.getAttribute('default-img');
    }

    checkoutBackRedirectOnSubmit() {
        const product_id = params().product_id;
        const checkout_id = params().checkout_id;

        const data = {
            backredirect_enabled: document.querySelector('.inp_backredirect_enabled').checked ? 1 : 0,
            backredirect_url: document.querySelector('.inp_backredirect_url').value || ''
        };

        const options = {
            headers: {'Client-Name': 'Action'},
            method: 'POST',
            body: JSON.stringify(data)
        };
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/backredirect/update`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                toastSuccess(body.message)
            }
            else toastError(body.message);
        });
    }
};
