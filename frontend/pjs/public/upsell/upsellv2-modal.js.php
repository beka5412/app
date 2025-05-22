(async function () {
  const btnUpsell = document.getElementById('btnUpsell');
  const endpoint = '<?php echo site_url(); ?>';
  const url = new URL(top.document.location.href)
  const id = btnUpsell.getAttribute('data-product-id');
  const data = url.searchParams.get('data');

  let btnDisabled = false;
  let textButton = '';

  const submit = async function () {
    if (btnDisabled) return;
    btnDisabled = true;
    btnUpsellSubmit.setAttribute('disabled', true);
    btnUpsellSubmit.innerHTML = '<div style="display: flex; justify-content: center"><div class="loader"></div></div>';
    elementErrorPayment.style.display = 'none';
    btnUpsellSubmit.style.display = 'block';

    const response = await fetch(`${endpoint}/snippets/upsell/pay?id=${id}&data=${data}`, {
      method: 'POST'
    });
    const {
      status,
      message,
      url,
      thanks_url
    } = await response.json();
    btnDisabled = false;
    btnUpsellSubmit.removeAttribute('disabled');
    btnUpsellSubmit.innerHTML = textButton;

    if (status === 'success') {
      btnUpsellSubmit.style.display = 'none';
      if (url) location.href = `${url}?data=${data}`;
      else if (thanks_url) location.href = thanks_url;
    } else {
      elementErrorPayment.style.display = 'block';
      elementErrorPayment.innerHTML = message;
    }
  }

  const response = await fetch(`${endpoint}/snippets/upsell/intent?id=${id}&data=${data}`);
  const { data: obj } = await response.json();

  if (obj?.paymentMethod) {
    const __ = function (text, vars = []) {
      let list = obj.__;
      let str = list[text] || text;
      for (let i in vars)
        str = str.replace(new RegExp(i, 'g'), vars[i]);
      return str;
    }

    const billing_details = obj.paymentMethod.billing_details;
    const card = obj.paymentMethod.card;
    const product = obj.product;
    const totalCurrency = obj.total_currency;

    const template = `
    <div class="lotuzpay-modal">
        <div class="lotuzpay-modal-content">
            <div class="lotuzpay-modal-body">
                <div class="lotuzpay-close-area">
                  <div class="lotuzpay-flex lotuzpay-justify-end">
                    <div id="LotuzPaybtnClose" class="lotuzpay-close">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="lotuzpay-modal-title">${billing_details.name}, ${__('you are buying:')}</div>
                
                <div>
                    <div class="lotuzpay-flex lotuzpay-gap-2 lotuzpay-mt-2">
                        <div>
                            <img src="${endpoint}${product.image}" width="60px" height="60px" />
                        </div>
                        <div class="lotuzpay-product-info-area">
                            <div>${product.name}</div>
                            <div>
                              <b class="lotuzpay-green">${totalCurrency}</b>
                              ${product.payment_type === 'recurring' ? `
                                <span class="lotuzpay-small-text">/${obj.cycle ? __(obj.cycle) : ''}</span>
                              ` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="lotuzpay-flex lotuzpay-justify-end lotuzpay-small-text">
                      <div>${billing_details.email}</div>
                    </div>
                    <div class="lotuzpay-flex lotuzpay-justify-between lotuzpay-items-center lotuzpay-card-area">
                      <div class="lotuzpay-flex lotuzpay-items-center lotuzpay-gap-2">
                        <div><img src="${endpoint}${`/images/upsell/${card.brand}.svg`}" height="20px" /></div>
                        <div>**** **** **** ${card.last4}</div>
                      </div>
                      <div>${card.exp_month}/${card.exp_year}</div>
                    </div>
                    <div class="lotuzpay-small-text lotuzpay-disclaimer">${__('By clicking the button below, you accept the payment gateway\'s terms and conditions and will be carrying out a transaction using this saved card.')}</div>
                </div>

                <button id="btnUpsellSubmit" class="lotuzpay-button">
                    ${__('Confirm payment')}
                </button>
            </div>
        </div>
    </div>
    `;
    // $product->price_promo ?: $product->price;
    const wrapper = document.querySelector('.upsell-wrapper');
    const modal = document.createElement('div');
    modal.id = 'LotuzPayModal';
    modal.innerHTML = template;
    wrapper.append(modal);
    LotuzPayModal.style.display = 'none';

    btnUpsellSubmit.onclick = submit;

    LotuzPaybtnClose.onclick = () => {
      LotuzPayModal.style.display = 'none';
    };
    
    btnUpsellSubmit.removeAttribute('disabled');
    textButton = btnUpsellSubmit.innerText;
  }

  btnUpsell.onclick = function () {
    LotuzPayModal.style.display = 'block';
  }

}());