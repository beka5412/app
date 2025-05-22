App.Subdomains.Purchase.Dashboard.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Dashboard';
    className = 'App.Subdomains.Purchase.Dashboard.Index';
    templatePurchaseInfo = '';

    view(loaded) {
        return super.find(`subdomains/purchase/dashboard/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    toogleInfo() {
        demoMLToogler.click();
        // $('.toggle-overlay').remove();
        // $('.div_purchase_info').removeClass('content-active');
    }
    
    showPurchaseInfo(element, instance, methodName, ev) {
        let purchaseInfo = document.querySelector('.div_purchase_info');
        purchaseInfo.classList.add('xx');

        let purchase = element.hasAttribute('data-purchase') ? element.getAttribute('data-purchase') : '';
        purchase = JSON.parse(purchase);

        let html = purchaseInfo.innerHTML;

        if (!this.templatePurchaseInfo) {
            this.templatePurchaseInfo = html;
        }

        else {
            html = this.templatePurchaseInfo;
        }

        let root = 'purchase';

        let separator_start = '{%';
        let separator_end = '%}';
        let new_html = '';
        let aux = html.split(separator_start);

        aux.forEach(item => {
            let aux2 = item.split(separator_end);
            if (aux2.length > 1) {
                let js = aux2[0];
                
                let text = '';

                try { text = eval(js); } catch(e) {}

                if (!text) text = '';

                new_html += text + item.substr(js.length + separator_end.length, item.length);
            }

            else 
                new_html += item;
        });
        
        purchaseInfo.innerHTML = new_html;
    }

    refundPurchasePre(element, instance, methodName, ev) {
        let purchaseID = Number(element.getAttribute('data-id'));
        let btn_purchase_confirm_refund = document.querySelector('.btn_purchase_confirm_refund');
        this.toogleInfo();
        $('.modalPurchaseConfirmRefund').modal('show');
        btn_purchase_confirm_refund.setAttribute('data-id', purchaseID);
    }

    refundPurchase(element, instance, methodName, ev) {
        let purchaseID = Number(element.getAttribute('data-id'));
        let reason = document.querySelector('.inp_purchase_reason');
        
        
        this.toogleInfo();
        $('.modalPurchaseConfirmRefund').modal('hide');
        
        let data = {
            purchase_id: purchaseID,
            reason: reason?.value || ''
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/subdomains/purchase/${purchaseID}/refund`, options).then(response => response.json()).then(body => {
            toast(body.message); 
            let cancel = document.querySelector('.btn_cancel_refund_purchase'); if (cancel) cancel.style.display = 'block';
            let request = document.querySelector('.btn_refund_purchase'); if (request) request.style.display = 'none';
            reason.value = '';
        });
    }

    cancelRefundPurchase(element, instance, methodName, ev) {
        let purchaseID = Number(element.getAttribute('data-id'));
        
        let data = {
            purchase_id: purchaseID
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/subdomains/purchase/${purchaseID}/cancelRefund`, options).then(response => response.json()).then(body => {
            toast(body.message); 
            let cancel = document.querySelector('.btn_cancel_refund_purchase'); if (cancel) cancel.style.display = 'none';
            let request = document.querySelector('.btn_refund_purchase'); if (request) request.style.display = 'block';
        });
    }
};