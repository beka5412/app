App.Subdomains.Checkout.Index = class Index extends Page {
  context = "form";
  title = "Checkout";
  couponDiscount = null;

  updateCheckoutValue() {
    const orderbumpTotal = this.orderbumps()
      .filter(({ checked }) => checked)
      .reduce((previousValue, currentValue) => {
        return previousValue + Number(currentValue.orderbump_price);
      }, 0);

    spanTotal.innerHTML = currency(Number(spanTotal.dataset.total) + orderbumpTotal - this.couponDiscount);
  }  

  view(loaded) {
    return super.find(
      `subdomains/checkout/${this?.constructor?.name || ""}${
        this?.queryString || ""
      }`,
      () => {
        this.load();
        return loaded;
      }
    );
  }

  stripe(paymentIntent) {
    const { client_secret } = paymentIntent;

    const {
      store_user_id,
      total,
      total_int,
      id: checkout_id,
      product_id,
      locale,
      _b,
      order_id,
    } = tagJSON("checkout");

    const { STRIPE_PUBKEY, STRIPE_CONNECT_ACCOUNT, STRIPE_CONNECT } =
      tagJSON("env");

    const inputName = document.getElementById("inputName");
    const inputEmail = document.getElementById("inputEmail");

    const addMessage = (message) => {
      const messageWithLinks = addDashboardLinks(message);
      Swal.fire("Erro!", messageWithLinks, "error");
    };

    const addDashboardLinks = (message) => {
      const piDashboardBase = "https://dashboard.stripe.com/test/payments";
      return message.replace(
        /(pi_(\S*)\b)/g,
        `<a href="${piDashboardBase}/$1" target="_blank">$1</a>`
      );
    };

    const stripeOptions = {
      apiVersion: "2023-10-16",
      // stripeAccount: 'acct_1PfZInEQpvewJOpe'
    };

    if (STRIPE_CONNECT && STRIPE_CONNECT_ACCOUNT)
      stripeOptions.stripeAccount = STRIPE_CONNECT_ACCOUNT;

    if (locale) {
      const lang = locale.split("_")[0];
      stripeOptions.locale = lang;
    }

    const stripe = Stripe(STRIPE_PUBKEY, stripeOptions);

    const elements = stripe.elements({
      clientSecret: client_secret,
    });

    const paymentElement = elements.create("payment", {
      fields: {
        billingDetails: {
          name: "never",
          email: "never",
        },
      },
    });
    paymentElement.mount("#payment-element");

    // const expressCheckoutElement = elements.create('expressCheckout')
    // expressCheckoutElement.mount('#express-checkout-element')
    // const linkAuthenticationElement = elements.create("linkAuthentication");
    // linkAuthenticationElement.mount("#link-authentication-element");
    // linkAuthenticationElement.addEventListener('change', function(r,o) {
    //   inputEmail.value = r?.value?.email || '';
    // });

    const paymentForm = document.querySelector("#payment-form");
    console.log("aqui");
    paymentForm
      .querySelector("#submit")
      .addEventListener("click", async (e) => {
        btnLoader.style.display = "block";
        btnText.style.display = "none";
        // e.preventDefault();
        paymentForm.querySelector("button").disabled = true;

        const params = {
          _b,
          store_user_id,
          total_int,
          checkout_id,
          product_id,
          name: inputName.value,
          email: inputEmail.value,
          country: navigator.language.split("-")[1],
        };

        const { error } = await stripe.confirmPayment({
          elements,
          confirmParams: {
            return_url: `${window.location.origin}/return_url?${$.param(
              params
            )}`,
            payment_method_data: {
              billing_details: {
                name: document.getElementById("inputName")?.value || "",
                email: document.getElementById("inputEmail")?.value || "",
              },
            },
          },
        });

        if (error) {
          btnLoader.style.display = "none";
          btnText.style.display = "block";

          this.updateOrder(order_id, error.message);
          addMessage(error.message);

          paymentForm.querySelector("button").disabled = false;
        }
      });
  }

  updateOrder(id, message) {
    let url_ = `${getSubdomainTranslated(
      "checkout"
    )}/ajax/actions/subdomains/checkout/updateOrder/${id}`;

    let data = { message };

    let options = {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Client-Name": "Action",
        Subdomain: subdomain(),
      },
      body: JSON.stringify(data),
    };

    fetch(url_, options);
  }

  fb() {
    !(function (f, b, e, v, n, t, s) {
      if (f.fbq) return;
      n = f.fbq = function () {
        n.callMethod
          ? n.callMethod.apply(n, arguments)
          : n.queue.push(arguments);
      };
      if (!f._fbq) f._fbq = n;
      n.push = n;
      n.loaded = !0;
      n.version = "2.0";
      n.queue = [];
      t = b.createElement(e);
      t.async = !0;
      t.src = v;
      s = b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t, s);
    })(
      window,
      document,
      "script",
      "https://connect.facebook.net/en_US/fbevents.js"
    );
  }

  backRedirect() {
    const { backredirect_enabled, backredirect_url } = tagJSON("checkout");

    if (backredirect_enabled) {
      history.pushState(null, null, location.href);
      window.onpopstate = function () {
        if (backredirect_url) {
          history.pushState(null, null, location.href);
          document.location = backredirect_url;
          history.go(1);
        } else {
          history.pushState(null, null, location.href);
          history.go(-history.length);
        }
      };
    }
  }

  orderbumps() {
    const elements = document.querySelectorAll(".checkout_orderbump_label");
    return [...elements].map((element) => {
      const { dataset } = element;
      const {
        orderbumpId,
        orderbumpPrice,
        orderbumpTitle,
        orderbumpDescription,
        orderbumpImage,
      } = dataset;
      const checkbox = element.querySelector("input[type=checkbox]");
      const checked = checkbox?.checked;
      return {
        checked,
        orderbump_id: orderbumpId,
        orderbump_price: orderbumpPrice,
        orderbump_title: orderbumpTitle,
        orderbump_description: orderbumpDescription,
        orderbump_image: orderbumpImage,
      };
    });
  }

  async checkoutNextStep() {
    const {
      id: checkout_id,
      _a,
      _b,
      _c,
      order_id,
      gateway_selected: gatewaySelected,
    } = tagJSON("checkout");
    const { STRIPE_PUBKEY } = tagJSON("env");

    const inputName = document.getElementById("inputName");
    const inputEmail = document.getElementById("inputEmail");

    btnLoader_NextButton.style.display = "block";
    btnNext.style.display = "none";
    progressBar.children[0].classList.remove("w-[50%]");
    progressBar.children[0].classList.add("w-[80%]");

    const orderbumps = this.orderbumps();

    const url = `${getSubdomainTranslated(
      "checkout"
    )}/ajax/actions/subdomains/checkout/paymentIntent/${checkout_id}`;

    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        _a,
        _b,
        _c,
        order_id,
        name: inputName.value || "",
        email: inputEmail.value || "",
        orderbumps,
      }),
    });

    const { data } = await response.json();

    btnLoader_NextButton.style.display = "none";
    mainForm.style.display = "none";
    if (gatewaySelected === "stripe") payForm.style.display = "block";

    if (gatewaySelected === "stripe") this.stripe(data);
    progressBar.children[0].classList.remove("w-[80%]");
    progressBar.children[0].classList.add("w-full");
  }

  orderbumpCheckboxOnClick(element) {}

  destroyCheckout() {
    const element = document.getElementById("payment-element");
    if (!element) return;
    element.innerHTML = "";
  }

  async renderCheckout(config = { orderbumps: [] }) {
    const {
      id: checkout_id,
      _a,
      _b,
      _c,
      order_id,
      gateway_selected: gatewaySelected,
    } = tagJSON("checkout");
    const { STRIPE_PUBKEY } = tagJSON("env");

    if (gatewaySelected !== "stripe") return;

    const inputName = document.getElementById("inputName");
    const inputEmail = document.getElementById("inputEmail");
    const url = `${getSubdomainTranslated(
      "checkout"
    )}/ajax/actions/subdomains/checkout/paymentIntent/${checkout_id}`;

    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        _a,
        _b,
        _c,
        order_id,
        name: inputName.value || "",
        email: inputEmail.value || "",
        ...config,
      }),
    });

    const { data } = await response.json();

    this.stripe(data);
  }

  orderbumpHandler() {
    const self = this;
    const { currency_symbol: currencySymbol } = tagJSON("checkout");

    [
      ...document.querySelectorAll(
        ".checkout_orderbump_label input[type=checkbox]"
      ),
    ].forEach((orderbumpCheckbox) => {
      orderbumpCheckbox.addEventListener('change', () => {
        const orderbumps = self
          .orderbumps()
          .filter((orderbump) => orderbump.checked);
        const totalOrderbump = orderbumps.reduce(
          (previousValue, currentValue) =>
            previousValue + Number(currentValue.orderbump_price),
          0
        );

        this.updateCheckoutValue();

        self.destroyCheckout();
        self.renderCheckout({ orderbumps });

        const checkoutOrderbumps = document.querySelector(
          "#checkout-orderbumps"
        );
        checkoutOrderbumps.classList.replace('hidden', 'flex');

        checkoutOrderbumps.innerHTML = "";
        orderbumps.forEach((orderbump) => {
          const newOrderbumpElement = document.createElement("div");
          newOrderbumpElement.innerHTML = `
            <div class="flex gap-2">
              <div>
                <img src="${orderbump.orderbump_image}" alt="${
            orderbump.orderbump_title
          }" class="rounded w-[45px] h-[45px]">
              </div>
              <div>
                <div>
                  <span class="text-md font-semibold text-black-300">
                    ${orderbump.orderbump_title}
                  </span>
                </div>
                <div class="flex font-semibold gap-1">
                  <span>
                    ${currencySymbol}
                  </span>
                  <span>
                    ${currency(orderbump.orderbump_price)}
                  </span>
                </div>
              </div>
            </div>
          `;
          checkoutOrderbumps.appendChild(newOrderbumpElement);
        });
      });
    });
  }

  paymentHandlers() {
    const {
      _b,
      store_user_id,
      product_id,
      id: checkout_id,
      gateway_selected: gatewaySelected,
    } = tagJSON("checkout");

    if (gatewaySelected !== "iugu") return;

    const { STRIPE_PUBKEY } = tagJSON("env");
    const btnSubmit = document.getElementById("submit");
    if (btnSubmit)
      btnSubmit.addEventListener("click", async function () {
        const btnLoader = document.getElementById("btnLoader");
        const btnText = document.getElementById("btnText");
        const gateway = document.getElementById("paymentMethod");

        btnLoader.style.display = "block";
        btnText.style.display = "none";

        let url_ =
          gateway.value === "credit-card"
            ? `${getSubdomainTranslated(
                "checkout"
              )}/ajax/actions/subdomains/checkout/pay`
            : `${getSubdomainTranslated(
                "checkout"
              )}/ajax/actions/subdomains/checkout/payPix`;

        let data = {
          name: document.getElementById("inputName")?.value || "",
          email: document.getElementById("inputEmail")?.value || "",
          cpf_cnpj: document.getElementById("inputCPFCNPJ")?.value || "",
          cc: document.getElementById("cardNumber")?.value || "",
          expiration: document.getElementById("cardExpiration")?.value || "",
          cvc: document.getElementById("cardCvc")?.value || "",
          months: parseInt(document.getElementById("months")?.value) || 1,
        };

        if (data.cpf_cnpj === "") {
          btnLoader.style.display = "none";
          btnText.style.display = "block";
          return Swal.fire("Erro!", "Preencha o CPF/CNPJ.", "error");
        }

        let someEmptyField = false;
        // for (const i in data) {
        //   if (!data[i]) {
        //     someEmptyField = true;
        //   }
        // }

        if (someEmptyField) {
          btnLoader.style.display = "none";
          btnText.style.display = "block";
          return Swal.fire("Erro!", "Preencha todos os campos.", "error");
        }

        data._b = _b;
        data.store_user_id = store_user_id;
        data.product_id = product_id;
        data.checkout_id = checkout_id;
        data.gateway = "iugu";

        let options = {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Client-Name": "Action",
            Subdomain: subdomain(),
          },
          body: JSON.stringify(data),
        };

        const response = await fetch(url_, options);
        const { status, message, data: info } = await response.json();

        btnLoader.style.display = "none";
        btnText.style.display = "block";

        if (status === "success") {
          location.href = info.url;
        } else {
          Swal.fire("Erro!", message, "error");
        }
      });
  }

  creditCardFieldMasks() {
    let options = {
      placeholder: "___.___.___-__",
      onKeyPress: function (value, e, field, options_) {
        let masks = ["000.000.000-009", "00.000.000/0000-00"],
          digits = value.replace(/\D/g, "").length,
          mask = digits <= 11 ? masks[0] : masks[1];

        element.mask(mask, options_);
      },
    };

    $("#cardNumber").mask("9999 9999 9999 9999");
    $("#cardExpiration").mask("99/99");
    $("#cardCvc").mask("999");
    $("#inputCPFCNPJ").mask("000.000.000-00", options);
  }

  checkoutTabs() {
    let paymentMethod = document.getElementById("paymentMethod");

    document.querySelectorAll(".tab-button-card").forEach((button) => {
      button.addEventListener("click", () => {
        document
          .querySelectorAll(".tab-button-card")
          .forEach((btn) => btn.classList.remove("active"));
        document
          .querySelectorAll(".tab-content")
          .forEach((content) => content.classList.remove("active"));

        paymentMethod.value = button.getAttribute("data-tab");
        button.classList.add("active");

        const tabId = button.getAttribute("data-tab");
        document.getElementById(tabId).classList.add("active");
      });
    });
  }

  ready(element, instance, methodName, ev) {
    this.applyCouponHandler();
    this.removeCouponHandler();
    this.orderbumpHandler();
    this.fb();
    this.backRedirect();
    this.renderCheckout();
    this.paymentHandlers();
    this.checkoutTabs();
    this.creditCardFieldMasks();
    faviconEl.href = element.getAttribute("data-favicon");
    if (document.getElementById("progressBar"))
      progressBar.children[0].classList.add("w-[50%]");

    const { pixels } = tagJSON("pixels");
    const fbPixels = pixels?.filter((pixel) => pixel.platform == "facebook");

    fbPixels?.forEach((pixel) => {
      fbq("init", pixel.content);
      console.log("pixel " + pixel.content);
      fbq("track", "PageView");
    });
  }

  checkoutInitiated = false;
  checkoutOnKeyupName() {
    if (!this.checkoutInitiated) {
      console.log("initiate");
      fbq("track", "InitiateCheckout");
    }
    this.checkoutInitiated = true;
  }

  removeCouponHandler() {
    const applyCouponButton = document.querySelector('#apply-coupon-button');
    const removeCouponButton = document.querySelector('#remove-coupon-button');
    const couponDiscountCheckoutTotal = document.querySelector('#coupon-discount-checkout-total');
    const couponAppliedLabel = document.querySelector('#coupon-applied-label');
    
    removeCouponButton.addEventListener('click', () => {
      const couponDiscountValueLabel = document.querySelector('#coupon-discount-value');

      couponDiscountCheckoutTotal.classList.replace('flex', 'hidden');
      applyCouponButton.classList.replace('hidden', 'flex');
      removeCouponButton.classList.replace('flex', 'hidden');
      couponAppliedLabel.classList.add('hidden');

      const discountValue = Number(couponDiscountValueLabel.getAttribute('value'))
      spanTotal.innerHTML = currency(Number(spanTotal.dataset.total) + discountValue);
    });
  }

  applyCouponHandler() {
    const { currency_symbol: currencySymbol } = tagJSON("checkout");
    const couponInput = document.querySelector('#coupon-input')
    const applyCouponButton = document.querySelector('#apply-coupon-button');
    const removeCouponButton = document.querySelector('#remove-coupon-button');
    const couponDiscountCheckoutTotal = document.querySelector('#coupon-discount-checkout-total');
    const couponAppliedLabel = document.querySelector('#coupon-applied-label');

    document.querySelector("#coupon-form").addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData(e.target);
      const baseUrl = getSubdomainSerialized("checkout");
      const url = `${baseUrl}/ajax/actions/subdomains/checkout/applyCoupon`;

      const response = await fetch(url, {
        headers: {
          'Content-Type': 'application/json',
          'Client-Name': 'Action'
        },
        method: 'POST',
        body: JSON.stringify({
          coupon: formData.get('coupon')
        })
      });
      
      const body = await response.json();

      if (body?.status !== 'success') return;
      couponInput.classList.add('input-coupon-applied');
      applyCouponButton.classList.replace('flex', 'hidden');
      removeCouponButton.classList.replace('hidden', 'flex');

      let couponDiscountTotalValue = body?.data?.discount;
      const checkoutTotal = Number(spanTotal.dataset.total);

      if (body?.data?.type === 'percent') {
        couponDiscountTotalValue = checkoutTotal * (body?.data?.discount / 100);
      }

      this.couponDiscount = body?.data;

      couponDiscountCheckoutTotal.classList.replace('hidden', 'flex');
      
      couponDiscountCheckoutTotal.innerHTML = `
        <span class="text-md flex font-semibold text-black-300">
          Cupom
        </span>

        <div class="flex font-semibold text-green-500 gap-1">
          <span>
            -
            ${currencySymbol}
          </span>
          <span id="coupon-discount-value" value="${couponDiscountTotalValue}">
            ${currency(couponDiscountTotalValue)}
          </span>
        </div>
      `

      this.updateCheckoutValue();

      couponAppliedLabel.innerHTML = `Cupom (${formData.get('coupon')}) aplicado`
      couponAppliedLabel.classList.remove('hidden');
    });
  }
};
