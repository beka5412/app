<content ready="ready" data-favicon="<?php echo $checkout?->favicon ?? ''; ?>">
  <script type="application/json" json-id="env">
    <?php echo json_encode([
      'STRIPE_PUBKEY' => stripe_public($_b),
      'STRIPE_CONNECT_ACCOUNT' => env('STRIPE_CONNECT_ACCOUNT'),
      'STRIPE_CONNECT' => env('STRIPE_CONNECT') == 'true',
    ]); ?>
  </script>

  <script type="application/json" json-id="customer">
    {
      "upsell_id": "<?php echo $upsell->id ?? ''; ?>",
      "price_var": "<?php echo $product_link->id ?? ''; ?>"
    }
  </script>

  <script type="application/json" json-id="pixels">
    <?php echo json_encode([
      'pixels' => $pixels,
    ]); ?>
  </script>

  <script type="application/json" json-id="checkout">
    <?php echo json_encode([
      'currency_symbol' => $product->currency_symbol,
      'gateway_selected' => $gateway_selected, // stripe | iugu
      'id' => $checkout?->id ?? '',
      'product_id' => $product?->id ?? '',
      'stripe_client_secret' => /* $payment_intent?->client_secret ?? */ '',
      'store_user_id' => $product?->user_id ?? 0,
      'total' => $total,
      'total_int' => intval($total * 100),
      'locale' => $locale,
      '_a' => $_a,
      '_b' => $_b,
      '_c' => $_c,
      'order_id' => $current_order->id,
      'backredirect_enabled' => $checkout->backredirect_enabled ?? '',
      'backredirect_url' => $checkout->backredirect_url ?? '',
    ]); ?>
  </script>
    <style>
        .div-check {
            display: grid;
            grid-gap: 1rem;
            gap: 1rem;
            grid-template-columns: 1fr 350px;
        }
        .button-check {
            height: 4rem;
            background: #009d68;
            width: 100%;
            color: #fff;
            font-weight: 500;
            font-size: 1.2rem;
            border: 0;
            display: flex;
            align-items: center;
            gap: 1rem
            }
            .col1 {
              order: 1;
            }
            .col2 {
              order: 2;
            }
            @media only screen and (max-width: 768px) {
              .div-check {
                display: grid;
                grid-gap: 1rem;
                gap: 1rem;
                grid-template-columns: 1fr;
                padding: 10px;
            }
            .col1 {
              order: 2;
            }
            .col2 {
              order: 1;
            }
        }
        .tab-button-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 150px;
            height: 120px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-button-card.active {
            border-color: #00b37e;
            background-color: rgba(0, 179, 126, 0.1);
        }

        .tab-button-card span {
            color: #0D9F48;
            margin-top: 8px;
            font-size: 0.75rem;
        }

        .tab-button {
            padding: 10px 15px;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
        }

        .tab-button.active {
            border-bottom: 2px solid #0D9F48;
            color: #0D9F48;
        }

        .tab-button-card svg {
            fill: #0D9F48;
        }

        .tab-button-card svg {
            height: auto;
            max-width: 24px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
        
        .input-coupon-applied {
          outline: 2px solid #0D9F48;
          box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.03),
            0px 3px 6px rgba(0, 0, 0, 0.02),
            0 0 0 3px hsla(144, 85%, 34%, 25%),
            0 1px 1px 0 rgba(0, 0, 0, 0.08);
        }

        .input-coupon-applied:focus {
          border-color: hsla(144, 85%, 34%, 50%);
          box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.03),
            0px 3px 6px rgba(0, 0, 0, 0.02),
            0 0 0 3px hsla(144, 85%, 34%, 25%),
            0 1px 1px 0 rgba(0, 0, 0, 0.08);
        }
    </style>
  <div>
    <header class="bg-[<?= $checkout?->header_bg_color ?: '#0D9F48' ?>]">
      <div class="mx-auto max-w-3xl	 p-4">
        <div class="flex items-center justify-center gap-5">
          <div>
            <!-- @s logo -->
            <?php if ($preview->logo ?? false) : ?>
              <img src="<?php echo $preview->logo; ?>" class="h-[45px]" />
            <?php else : ?>

              <?php if ($checkout->logo ?? false) : ?>
                <img src="<?php echo $checkout->logo; ?>" class="h-[45px]" />
              <?php endif; ?>
            <?php endif; ?>
            <!-- @e logo -->
          </div>
          <div>
            <?php if ($checkout?->countdown_enabled) : ?>
              <div class="flex text-[<?= $checkout?->header_text_color ?: '#ffffff' ?>] font-semibold">
                <div>
                  <?php echo $checkout?->countdown_text ?: 'Essa oferta termina em'; ?>
                </div>
                <div style="margin-left:10px">
                  <countdown>
                    <?php echo $checkout?->countdown_time ?: '15:00'; ?>
                  </countdown>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </header>

    <main>
      <div class="mx-auto max-w-3xl mt-10" style="margin-top: 5px;max-width: 1000px;">

        <!-- @s top -->
        <?php if ($preview->top_banner ?? false) : ?>
          <div class="mt-4 px-8" style="border-radius: 10px;">
            <img src="<?php echo $preview->top_banner ?? ''; ?>" style="border-radius: 10px;" />
          </div>
        <?php else : ?>
          <?php if ($checkout->top_banner ?? false) : ?>
            <div class="mt-4 px-8" style="border-radius: 10px;">
              <img src="<?php echo $checkout->top_banner ?? ''; ?>" style="border-radius: 10px;" />
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <!-- @e top -->

        <!-- @s sidebar -->
        <?php if ($preview->sidebar_banner ?? false) : ?>
          <div class="mt-4 px-8">
            <img src="<?php echo $preview->sidebar_banner ?? ''; ?>" style="border-radius: 10px;" />
          </div>
        <?php else : ?>
          <?php if ($checkout->sidebar_banner ?? false) : ?>
            <div class="mt-4 px-8" style="border-radius: 10px;">
              <img src="<?php echo $checkout->sidebar_banner ?? ''; ?>" style="border-radius: 10px;" />
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <!-- @e sidebar -->

        <!-- @s top 2 -->
        <?php if ($preview->top_2_banner ?? false) : ?>
          <div class="mt-4 px-8" style="border-radius: 10px;">
            <img src="<?php echo $preview->top_2_banner ?? ''; ?>" style="border-radius: 10px;" />
          </div>
        <?php else : ?>
          <?php if ($checkout->top_2_banner ?? false) : ?>
            <div class="mt-4 px-8" style="border-radius: 10px;">
              <img src="<?php echo $checkout->top_2_banner ?? ''; ?>" style="border-radius: 10px;" />
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <!-- @e top 2 -->
        <div class="div-check mt-10">
          <div class="col-lg-8 col1">
            <form id="payment-form" class=" border border-gray-200 rounded bg-white py-8 px-8" >
              <div id="progressBar" class="hidden bg-gray-100 w-full h-3 mb-5 rounded overflow-hidden">
                <div class="bg-[#0D9F48] transition-all ease-in-out duration-1000 h-full w-[0px]"></div>
              </div>
              <div id="mainForm">
                <div class="flex gap-2">
                  <div class="bg-gray-200 rounded flex justify-center items-center  w-[30px] h-[30px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                      <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg>
                  </div>
                  <span class="text-lg font-semibold">
                    <?= lang($locale, 'Identification') ?>
                  </span>
                </div>
                <div class="mt-5">
                  <label for="" class="label" id="labelName">
                    <?= lang($locale, 'Name') ?>
                  </label>
                  <input type="text" class="input" name="name" id="inputName" keyup="checkoutOnKeyupName">
                </div>

                <div class="mt-3">
                  <label for="" class="label">
                    <?= lang($locale, 'Email') ?>
                  </label>
                  <input type="text" class="input" name="email" id="inputEmail">
                </div>
                <div class="mt-3">
                      <label for="" class="label" id="labelCPF">
                        <?= lang($locale, 'CPF') ?>
                      </label>
                      <input type="text" class="input" name="name" id="inputCPFCNPJ" placeholder="123.456.789-0">
                    </div>
                <?php if (count($product->orderbumps ?? [])): ?>
                  <div class="mt-3">
                    <?php foreach ($product->orderbumps as $orderbump): ?>
                    <?php if ($orderbump->status <> 'published') continue; ?>
                      <div class="order-bump-container mt-4">
                        <div class="order-bump-card">
                          <div class="card-body">
                            <div class="order-info-block">
                              <div class="img-block">
                                <img src="<?php echo $orderbump->product->image; ?>" alt="Product">
                              </div>
                              <div class="info-block">
                                <label class="d-block title bold" 
                                data-orderbump-id="<?= $orderbump->id ?>"><?= $orderbump->title ?></label>
                                <div class="text"><?= $orderbump->description ?></div>
                                <div>
                                  <span class="price-promo"><?= $product->currency_symbol ?> <?= number_to_currency_by_symbol($orderbump->price); ?></span>
                                </div>
                              </div>
                            </div>
                            <label
                              class="bump-title button sm-bump-title flex flex-1 p-4 px-2 pl-1 rounded items-center shadow-lg checkout_orderbump_label"
                              data-orderbump-id="<?= $orderbump->id ?>"
                              data-orderbump-price="<?= $orderbump->price ?>"
                              data-orderbump-title="<?= $orderbump->title ?>"
                              data-orderbump-description="<?= $orderbump->description ?>"
                              data-orderbump-image="<?= $orderbump->product->image ?>"
                            >
                                <input type="checkbox" class="form-checkbox border-gray-600 h-5 w-5 transition duration-150 ease-in-out mr-2" value="I2vZCau" style="color: rgb(36, 126, 243);">
                                <span class="font-bold">
                                  <?= $orderbump->text_button ?>
                                </span>
                            </label>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?> 

                <div id="payment-element" style="margin-top: 20px"></div>

                <?php if ($gateway_selected === 'iugu'): ?>
                    <div class="flex gap-2 mt-10">
                        <div class="bg-gray-200 rounded flex justify-center items-center  w-[30px] h-[30px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card-2-back-fill" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5H0zm11.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM0 11v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1z" />
                            </svg>
                        </div>
                        <span class="text-lg font-semibold">
                            <?= lang($locale, 'Payment') ?>
                        </span>
                    </div>
                    <div class="grid md:grid-cols-12 gap-5 mt-5">
                        <!-- Tabs -->
                        <div class="col-span-12 flex gap-4">
                            <span class="tab-button-card active border shadow" data-tab="credit-card">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M64 32C28.7 32 0 60.7 0 96l0 32 576 0 0-32c0-35.3-28.7-64-64-64L64 32zM576 224L0 224 0 416c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-192zM112 352l64 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-64 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zm112 16c0-8.8 7.2-16 16-16l128 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-128 0c-8.8 0-16-7.2-16-16z"/></svg>
                                <span class="text-green-500 font-semibold">Cartão de Crédito</span>
                            </span>

                            <!-- Tab PIX -->
                            <span class="tab-button-card border shadow" data-tab="pix">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 80C0 53.5 21.5 32 48 32l96 0c26.5 0 48 21.5 48 48l0 96c0 26.5-21.5 48-48 48l-96 0c-26.5 0-48-21.5-48-48L0 80zM64 96l0 64 64 0 0-64L64 96zM0 336c0-26.5 21.5-48 48-48l96 0c26.5 0 48 21.5 48 48l0 96c0 26.5-21.5 48-48 48l-96 0c-26.5 0-48-21.5-48-48l0-96zm64 16l0 64 64 0 0-64-64 0zM304 32l96 0c26.5 0 48 21.5 48 48l0 96c0 26.5-21.5 48-48 48l-96 0c-26.5 0-48-21.5-48-48l0-96c0-26.5 21.5-48 48-48zm80 64l-64 0 0 64 64 0 0-64zM256 304c0-8.8 7.2-16 16-16l64 0c8.8 0 16 7.2 16 16s7.2 16 16 16l32 0c8.8 0 16-7.2 16-16s7.2-16 16-16s16 7.2 16 16l0 96c0 8.8-7.2 16-16 16l-64 0c-8.8 0-16-7.2-16-16s-7.2-16-16-16s-16 7.2-16 16l0 64c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-160zM368 480a16 16 0 1 1 0-32 16 16 0 1 1 0 32zm64 0a16 16 0 1 1 0-32 16 16 0 1 1 0 32z"/></svg>
                                <span class="text-gray-500 font-semibold">PIX</span>
                            </span>
                        </div>

                        <!-- Conteúdo das Tabs -->
                        <div class="col-span-12 mt-2">
                            <!-- Pagamento com Cartão de Crédito -->
                            <div class="tab-content active" id="credit-card">
                                <div class="grid md:grid-cols-4 gap-5">                                    
                                    <div class="col-span-12">
                                        <div>
                                            <label for="" class="label" id="labelName">
                                                <?= lang($locale, 'Card number') ?>
                                            </label>
                                            <input type="text" class="input" id="cardNumber" placeholder="1234 1234 1234 1234">
                                        </div>
                                    </div>
                                    <div class="col-span-6">
                                        <div>
                                            <label for="" class="label">
                                                <?= lang($locale, 'Expiration date') ?>
                                            </label>
                                            <input type="text" class="input" id="cardExpiration" placeholder="MM/AA">
                                        </div>
                                    </div>
                                    <div class="col-span-6">
                                        <div>
                                            <label for="" class="label">
                                                <?= lang($locale, 'CVC') ?>
                                            </label>
                                            <input type="text" class="input" id="cardCvc" placeholder="CVC">
                                        </div>
                                    </div>
                                    <div class="col-span-12">
                                      <div>
                                        <label class="label" for="months">Parcelas</label>
                                        <select id="months" class="input inp_payment_type" checkout-input="months">
                                              <option value="" hidden="hidden" disabled="disabled">Parcelas - Até 12x</option>
                                              <option value="1" selected>1 </option>
                                              <option value="2">2</option>
                                              <option value="3">3</option>
                                              <option value="4">4</option>
                                              <option value="5">5</option>
                                              <option value="6">6</option>
                                              <option value="7">7</option>
                                              <option value="8">8</option>
                                              <option value="9">9</option>
                                              <option value="10">10</option>
                                              <option value="11">11</option>
                                              <option value="12">12</option>
                                          </select>
                                      </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagamento via PIX -->
                            <div class="tab-content" id="pix">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <input id="paymentMethod" hidden="hidden" type="text" name="paymentMethod" value="credit-card">
                <div id="payment-errors" role="alert"></div>
                <div id="messages" role="alert" style="display: none; color: #df1b41; margin-top: 10px"></div>

                <button type="button" id="(submit)" class="button mt-5 transition ease-in-out duration-100 hover:scale-105 flex justify-center">
                  <div id="btnLoader" class="loader" style="display: none"></div>
                  <div id="btnText" class="py-1"><?= lang($locale, 'Pay') ?></div>
                </button>
              </div>

              <div class="mx-auto max-w-3xl" style="margin-top: 2px;">
                <h2 style="font-size: 0.80rem;" class="text-center mt-10 font-medium text-gray-500" id="label3DS">
                  <?= lang($locale, 'If you need 3DSecurity, please follow the steps below.') ?>
                </h2>
                <div style="margin-bottom: 8px;"></div>

                <div class="mt-10" style="margin-top: -1px;">
                  <img src="/images/3dsecure.gif" class="rounded" alt="" style="width: 91%; margin-left: auto; margin-right: auto;">
                </div>
              </div>
            </form>
          </div>

          <div class="col-lg-4 col2">
            <div class="border border-gray-200 rounded bg-white">
              <div class="flex flex-col gap-5">
                <div class="flex gap-2 pt-8 px-8">
                  <div>
                    <img src="<?= site_url() . $product->image; ?>" alt="" class="rounded w-[45px] h-[45px]">
                  </div>
                  <div>
                    <div>
                      <span class="text-md font-semibold text-black-300">
                        <?php echo $product->name; ?>
                      </span>
                    </div>
                    <div class="flex font-semibold gap-1">
                      <span>
                        <?= $product->currency_symbol ?>
                      </span>
                      <span>
                        <?= number_to_currency_by_symbol($total, $product->currency_symbol); ?>
                      </span>
                    </div>
                  </div>
                </div>

                <div id="checkout-orderbumps" class="hidden px-8 gap-5 flex-col">
                </div>

                <div class="hidden ml-[54px] flex-col justify-between px-8" id="coupon-discount-checkout-total">
                </div>

                <div class="flex justify-between border-t px-8 py-5">
                  <div class="flex gap-2">
                    <div class="bg-gray-200 rounded flex justify-center items-center  w-[30px] h-[30px]">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check-fill color-gray-900" viewBox="0 0 16 16" style="height: 24px">
                        <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708" />
                      </svg>
                    </div>
                    <span class="text-lg font-semibold">
                      Total
                    </span>
                  </div>
                  <div class="flex gap-1 items-center">
                    <div class="flex font-semibold gap-1">
                      <span>
                        <?= $product->currency_symbol ?>
                      </span>
                      <span id="spanTotal" data-total="<?= $total ?>">
                        <?= number_to_currency_by_symbol($total, $product->currency_symbol); ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <form class="mt-5" id="coupon-form">
              <div>
                <label class="label mb-1">
                  Cupom de desconto
                </label>
                <input
                  type="text"
                  class="input mb-1"
                  name="coupon"
                  id="coupon-input"
                >
                <small id="coupon-applied-label" class="hidden"></small>
              </div>
              <button
                form="coupon-form"
                type="submit"
                id="apply-coupon-button"
                class="button mt-2 hover:bg-green-700 ease-linear transition flex justify-center"
              >
                <p class="py-1">Aplicar</p>
              </button>
              <button
                type="button"
                id="remove-coupon-button"
                class="button mt-1 bg-gray-400 hover:bg-gray-500 ease-linear transition hidden justify-center"
              >
                <p class="py-1">Remover</p>
              </button>
            </form>


            <?php if (count($checkout->testimonials)) : ?>
              <div class="mt-10 border border-gray-200 rounded bg-white py-8 px-8">
                <span class="text-lg font-semibold">
                  <?= lang($locale, 'Testimonials') ?>
                </span>
                <div style="margin-bottom: 40px;"></div>
                <?php foreach ($checkout->testimonials as $testimonial) : ?>
                  <div class="mb-8">
                    <div class="flex gap-2">
                      <div>
                        <img src="<?= get_subdomain_serialized('checkout') . '/' . ($testimonial->photo ? $testimonial->photo : '/images/default.png') ?>" alt="" class="rounded-full w-[50px] h-[50px]">
                      </div>
                      <div>
                        <div class="font-semibold text-md">
                          <?= $testimonial->name ?>
                        </div>
                        <div class="text-sm">
                          <img src="/images/stars.png" alt="" ondragstart="return false" style="position: relative; left: -3px;">
                        </div>
                      </div>
                    </div>
                    <div class="mt-3 text-sm leading-5 text-gray-500">
                      <?= $testimonial->text ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="mx-auto max-w-3xl" style="margin-top: 5px;">
        <!-- @s footer -->
        <?php if ($preview->footer_banner ?? false) : ?>
          <div class="mt-4 px-5" style="border-radius: 10px;">
            <img src="<?php echo $preview->footer_banner ?? ''; ?>" style="border-radius: 10px;" />
          </div>
        <?php else : ?>
          <?php if ($checkout->footer_banner ?? false) : ?>
            <div class="mt-4 px-5" style="border-radius: 10px;">
              <img src="<?php echo $checkout->footer_banner ?? ''; ?>" style="border-radius: 10px;" />
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <!-- @e footer -->
      </div>
    </main>

    <footer class="mt-5 mb-20 mx-auto max-w-xl p-7">
      <div class="flex justify-between text-xs">
        <div class="text-gray-400">
          <?= date('Y') ?> -
          <?= lang($locale, 'All rights reserved.') ?>
          <a href="<?= site_url_base() ?>/terms"><?= lang($locale, 'Read our terms.') ?></a>
        </div>
        <div class="flex items-center text-[#21c262]">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
              <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
              <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
            </svg>
          </span>
          <span class="font-semibold ms-1">
            <?= lang($locale, '100% Secure purchase.') ?>
          </span>
        </div>
      </div>
    </footer>
  </div>
</content>

