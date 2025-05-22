(async function () {
  const btnUpsell = document.getElementById('btnUpsell');
  const endpoint = '<?= site_url(); ?>';
  const url = new URL(top.document.location.href)
  const id = btnUpsell.getAttribute('data-product-id');
  const data = url.searchParams.get('data');
  const loadingHtml = '<div style="display: flex; justify-content: center"><div class="loader"></div></div>';
  let btnUpsellSubmit;
  let textConfirmButton = '';
  const btnUpsellReject = document.getElementById('btnUpsellReject');

  let btnDisabled = false;
  let textButton = '';
  let paymentDisabled = false;
  const cookieKey = 'upsellPaymentId';
  const cookieListKey = 'upsellPaymentIds';
  const { data: intentData } = await (await fetch(`${endpoint}/snippets/upsell/intent/id?data=${data}&id=${id}`)).json();

  const submit = async function () {
    if (document.cookie) {
      const cookies = document.cookie.split(';');
      const cookieFound = cookies.find(cookie => {
        const key = cookie.split('=')[0].trim();
        // console.log(key +' === ' + intentData.id);
        return key === intentData.id;
      });
      if (cookieFound) paymentDisabled = true;
      // return;
      // if (cookieFound) {
      //   const upsellPaymentId = cookieFound.substring(cookieKey.length + 1) || '';
      //   const decoded = atob(upsellPaymentId);
      //   const cookieProductId = decoded.split(':')[0];
      //   const cookieIntentId = decoded.split(':')[1];
      //   if (id === cookieProductId && intentData.intent_id === cookieIntentId) paymentDisabled = true;
      // }
    }

    if (btnDisabled) return;

    if (paymentDisabled) {
      elementErrorPayment.style.display = 'block';
      elementErrorPayment.innerHTML = 'O pagamento foi recusado, não tente novamente com este cartão.';
      return console.error('Payment failed');
    }

    btnDisabled = true;
    textButton = btnUpsell.innerHTML;
    btnUpsell.setAttribute('disabled', true);
    btnUpsell.innerHTML = loadingHtml;
    btnUpsellSubmit.setAttribute('disabled', true);
    btnUpsellSubmit.innerHTML = loadingHtml;
    elementErrorPayment.style.display = 'none';
    btnUpsell.style.display = 'block';

    const response = await fetch(`${endpoint}/snippets/upsell/pay?id=${id}&data=${data}`, {
      method: 'POST'
    });

    const {
      status,
      message,
      url,
      thanks_url,
      paid,
      data: data2,
      first_id,
      current_id,
    } = await response.json();
    btnDisabled = false;
    btnUpsell.removeAttribute('disabled');
    btnUpsell.innerHTML = textButton;
    btnUpsellSubmit.removeAttribute('disabled');
    btnUpsellSubmit.innerHTML = textConfirmButton;
    document.cookie = first_id + '=1';

    if (paid) {
      if (url) location.href = `${url}?data=${data2}`;
      else if (thanks_url) location.href = thanks_url;
    } else {
      if (thanks_url) location.href = thanks_url;
      else {
        btnUpsell.style.display = 'none';
        btnUpsellReject.style.display = 'none';

        elementErrorPayment.style.display = 'block';
        elementErrorPayment.innerHTML = message;
        paymentDisabled = true;
      }
    }
  }

  btnUpsell.onclick = () => {
    LotuzPayModal.style.display = 'block';
  };

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

          <!-- div class="lotuzpay-modal-title">${billing_details.name}, ${__('you are buying:')} -->

          <div>
            <div class="lotuzpay-modal-title">${__('you are buying:')}</div>
          </div>

          <div class="lotuzpay-line"></div>
          
          <div>
            <div class="lotuzpay-flex lotuzpay-gap-2 lotuzpay-mt-2 lotuzpay-mx-1">
              <div>
                <img src="${endpoint}${product.image}" width="60px" height="60px" />
              </div>
              <div class="lotuzpay-product-info-area lotuzpay-text-color">
                <div>${product.name}</div>
                <div>
                  <b class="lotuzpay-green">${totalCurrency}</b>
                  ${product.payment_type === 'recurring' ? `
                  <span class="lotuzpay-small-text">/${obj.cycle ? __(obj.cycle) : ''}</span>
                  ` : ''}
                </div>
              </div>
            </div>

            <div class="lotuzpay-flex lotuzpay-justify-end lotuzpay-small-text lotuzpay-mx-1 lotuzpay-text-color">
              <div>${billing_details.email}</div>
            </div>

            <div class="lotuzpay-flex lotuzpay-justify-between lotuzpay-items-center lotuzpay-card-area lotuzpay-mx-1">
              <div class="lotuzpay-flex lotuzpay-items-center lotuzpay-gap-2">
              <div><img src="${endpoint}${`/images/upsell/${card.brand}.svg`}" height="20px" /></div>
                <div style="letter-spacing: 3px;">**** **** **** ${card.last4}</div>
              </div>
              <div style="letter-spacing: 2px;">${card.exp_month}/${card.exp_year}</div>
            </div>

            <div class="lotuzpay-small-text lotuzpay-disclaimer lotuzpay-mx-1 lotuzpay-my-2">${__('By clicking the button below, you accept the payment gateway\'s terms and conditions and will be carrying out a transaction using this saved card.')}</div>
          </div>

          <div class="lotuzpay-line"></div>
          
          <div class="lotuzpay-m-1">
            <button id="btnUpsellSubmit" class="lotuzpay-button-modal">
              ${__('Confirm payment')}
            </button>
          </div>
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
    btnUpsellSubmit = document.getElementById('btnUpsellSubmit');
    textConfirmButton = btnUpsellSubmit.innerText;

    btnUpsellSubmit.onclick = submit;

    LotuzPaybtnClose.onclick = () => {
      LotuzPayModal.style.display = 'none';
    };

    btnUpsellSubmit.removeAttribute('disabled');
    textButton = btnUpsellSubmit.innerText;    
  }

  if (btnUpsellReject) {
    const rejectUrlArray = btnUpsellReject.href.split('?');
    const rejectUrl = rejectUrlArray[0];
    btnUpsellReject.href = `${rejectUrl}?data=${data}`
  }
}());
