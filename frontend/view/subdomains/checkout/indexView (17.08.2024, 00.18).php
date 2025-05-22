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

  <div>
    <header class="bg-[<?= $checkout?->header_bg_color ?: '#FF0000' ?>]">
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
      <div class="mx-auto max-w-3xl" style="margin-top: 5px;">

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

        <div class="mt-10 border border-gray-200 rounded bg-white py-8 px-8" style="margin: 20px;">
          <div class="flex justify-between">
            <div class="flex gap-2">
              <div class="bg-gray-200 rounded flex justify-center items-center  w-[30px] h-[30px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check-fill color-gray-900" viewBox="0 0 16 16" style="height: 24px">
                  <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708" />
                </svg>
              </div>
              <span class="text-lg font-semibold">
                <?= lang($locale, 'Your purchase') ?>
              </span>
            </div>
            <div class="flex gap-1 items-center">
              <!-- <span class="text-xs">Total:</span> -->
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
          <div class="flex gap-2 mt-5 mb-10">
            <div>
              <img src="<?php echo $product->image; ?>" alt="" class="rounded w-[45px] h-[45px]">
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
        </div>

        <form id="payment-form" class="mt-10 border border-gray-200 rounded bg-white py-8 px-8" style="margin: 20px;">
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
                        <label class="bump-title button sm-bump-title flex flex-1 p-4 px-2 pl-1 rounded items-center shadow-lg checkout_orderbump_label" 
                          data-orderbump-id="<?= $orderbump->id ?>"
                          data-orderbump-price="<?= $orderbump->price ?>">
                            <input type="checkbox" class="form-checkbox border-gray-600 h-5 w-5 transition duration-150 ease-in-out mr-2" value="I2vZCau" style="color: rgb(36, 126, 243);">
                            <span class="font-bold">
                         <?= $orderbump->text_button ?> </span>
                        </label>
                        
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?> 

            <div id="payment-element" style="margin-top: 20px"></div>

            <!-- <button type="button" class="button mt-5 transition ease-in-out duration-100 hover:scale-105 flex justify-center" click="checkoutNextStep">
              <div id="btnLoader_NextButton" class="loader" style="display: none"></div>
              <div id="btnNext" class="py-1"><?= lang($locale, 'Next') ?></div>
            </button> -->

            <?php if ($gateway_selected === 'iugu'): ?>
            <div class="mt-5">
              <label for="" class="label" id="labelCPF">
                <?= lang($locale, 'CPF') ?>
              </label>
              <input type="text" class="input" name="name" id="inputCPFCNPJ">
            </div>
            <div class="grid md:grid-cols-4 gap-5 mt-5">
              <div class="col-span-2">
                <div>
                  <label for="" class="label" id="labelName">
                    <?= lang($locale, 'Card number') ?>
                  </label>
                  <input type="text" class="input" id="cardNumber">
                </div>
              </div>
              <div>
                <div>
                  <label for="" class="label">
                    <?= lang($locale, 'Expiration date') ?>
                  </label>
                  <input type="text" class="input" id="cardExpiration">
                </div>
              </div>
              <div>
                <div>
                  <label for="" class="label">
                    <?= lang($locale, 'CVC') ?>
                  </label>
                  <input type="text" class="input" id="cardCvc">
                </div>
              </div>
              <div>
                  <label class="form-label" for="default-01">Parcelas</label>
                  <select class="form-control1" id="months" checkout-input="months">
                        <option value="" hidden="hidden" disabled="disabled"> Parcelas </option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                      </select>
                    </div>
              </div>
            </div>
            <?php endif; ?>

            <div id="payment-errors" role="alert"></div>
            <div id="messages" role="alert" style="display: none; color: #df1b41; margin-top: 10px"></div>

            <button type="button" id="submit" class="button mt-5 transition ease-in-out duration-100 hover:scale-105 flex justify-center">
              <div id="btnLoader" class="loader" style="display: none"></div>
              <div id="btnText" class="py-1"><?= lang($locale, 'Pay') ?></div>
            </button>
          </div>

          <div id="payForm" style="display: none">
            <div class="flex gap-2">
              <div class="bg-gray-200 rounded flex justify-center items-center  w-[30px] h-[30px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card-2-back-fill" viewBox="0 0 16 16">
                  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5H0zm11.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM0 11v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1z" />
                </svg>
              </div>
              <span class="text-lg font-semibold">
                <?= lang($locale, 'Purchase') ?>
              </span>
            </div>
            <div class="mt-3">
              <div id="link-authentication-element"></div>
              <!-- <label for="" class="label">
                <?= lang($locale, 'Email') ?>
              </label>
              <input type="text" class="input" name="email" id="inputEmail"> -->
              <input type="text" class="hidden" name="email" id="inputEmail">
            </div>
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

        <?php if (count($checkout->testimonials)) : ?>
          <div class="mt-10 border border-gray-200 rounded bg-white py-8 px-8" style="margin: 20px;">
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