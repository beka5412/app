<?php $locale = $product->language->code ?? ''; ?>
<title>Pagamento Realizado - Pix</title>
<content ready="ready">
    <script type="application/json" json-id="env">
        <?php echo json_encode([
            "STRIPE_PUBKEY" => env('STRIPE_PUBKEY'),
        ]); ?>
    </script>

    <script type="application/json" json-id="thanks_meta">
        <?php echo json_encode([
            "pixels" => isset($pixels) ? json_encode($pixels ?? '[]') : '[]',
            "total" => $order?->total ?: 0,
            "client_secret" => $intent_client_secret,
        ]); ?>
    </script>

    <link rel="stylesheet" href="<?php echo get_subdomain_serialized('checkout'); ?>/static/css/thanks.css" />


  <div>

    <main>
      <div class="mx-auto max-w-xl" style="margin-top: 5px;">
        <div class="mt-10 flex justify-center">
          <img src="/images/checkmark.svg" alt="">
        </div>
        <div class="mt-[20px] flex justify-center">
          <h1 class="text-[1.55rem] font-bold text-black text-satoshi"><?= lang($locale, 'Thank you for your purchase!') ?></h1>
        </div>
        <div class="mt-1 flex justify-center">
          <span class="text-lg text-satoshi"><?= lang($locale, 'You will receive an email confirming your order.') ?></span>
        </div>
        <div class="mt-3 flex justify-center">
          <a href="<?= get_subdomain_serialized('purchase') ?>/login" type="button" class="flex justify-between items-center bg-[#23CC67] text-white rounded px-4 py-[9px] font-semibold">
            <span class="text-[15px] font-bold me-3"><?= lang($locale, 'Access content') ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" style="zoom: .7" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>
        </div>
        
        <div class="mt-[56px] border border-gray-100 rounded bg-white py-8 px-8">
          <div class="flex justify-between">
            <div class="flex gap-2">
              <div class="bg-gray-200 rounded flex justify-center items-center  w-[30px] h-[30px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-cart-check-fill color-gray-900" viewBox="0 0 16 16" style="height: 24px">
                  <path
                    d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708" />
                </svg>
              </div>
              <span class="text-lg font-semibold"><?= lang($locale, 'Your purchase') ?></span>
            </div>
            <div class="flex gap-1 items-center">
              <!-- <span class="text-xs">Total:</span> -->
              <div class="flex font-semibold gap-1">
                <span><?= currency_code_to_symbol($order->currency_symbol)->value ?></span>
                <span><?= number_to_currency_by_symbol($order->currency_total, $order->currency_symbol); ?></span>
              </div>
            </div>
          </div>
          <div class="flex gap-2 mt-5">
            <div>
              <img src="<?php echo $product->image; ?>" alt="" class="rounded w-[45px] h-[45px]">
            </div>
            <div>
              <div>
                <span class="text-md font-satoshi font-[500] text-gray-400"><?php echo $product->name ?></span>
              </div>
              <div class="flex font-bold text-md gap-1">
                <span><?= currency_code_to_symbol($order->currency_symbol)->value ?></span>
                <span><?php echo number_to_currency_by_symbol($order->currency_total, $order->currency_symbol); ?></span>
              </div>
            </div>
          </div>
        </div>
        
        <?php /*
        if ($intent_client_secret): ?>
            <div class="mt-[56px] border border-gray-100 rounded bg-white py-8 px-8">
            <div id="payment-element"></div>
            </div>
        <?php endif; 
        */ ?>

        <!-- <div class="mt-10 border border-gray-200 rounded bg-white py-8 px-8" style="margin: 20px;">
          <span class="text-lg font-semibold">Deposiciones</span>
          <div style="margin-bottom: 40px;"></div>
          <div>
            <div class="flex gap-2">
              <div>
                <img src="/images/p1.webp" alt="" class="rounded-full">
              </div>
              <div>
                <div class="font-semibold text-md">Miguel Sánchez</div>
                <div class="text-sm">
                  <img src="/images/stars.png" alt="" ondragstart="return false" style="position: relative; left: -3px;">
                </div>
              </div>
            </div>
            <div class="mt-3 text-sm leading-5 text-gray-500">
              Estoy emocionado con los resultados que obtuve con el Aplicación canela. Mi diabetes está bajo control
              por primera vez en años, y debo todo a este producto increíble. Recomiendo a todos los que sufren esta
              condición.
            </div>
          </div>
          <div class="mt-10">
            <div class="flex gap-2">
              <div>
                <img src="/images/p2.webp" alt="" class="rounded-full">
              </div>
              <div>
                <div class="font-semibold text-md">Alejandro Pérez</div>
                <div class="text-sm">
                  <img src="/images/stars.png" alt="" ondragstart="return false" style="position: relative; left: -3px;">
                </div>
              </div>
            </div>
            <div class="mt-3 text-sm leading-5 text-gray-500">
              Cuando comencé a usar el Aplicación canela, no esperaba resultados tan rápidos y positivos. Mi salud
              mejoró drásticamente, y finalmente puedo vivir sin preocuparme constantemente por mi diabetes. Este
              producto es verdaderamente transformador.
            </div>
          </div>
          <div class="mt-10">
            <div class="flex gap-2">
              <div>
                <img src="/images/p3.webp" alt="" class="rounded-full">
              </div>
              <div>
                <div class="font-semibold text-md">Carmen Rodríguez</div>
                <div class="text-sm">
                  <img src="/images/stars.png" alt="" ondragstart="return false" style="position: relative; left: -3px;">
                </div>
              </div>
            </div>
            <div class="mt-3 text-sm leading-5 text-gray-500">
              No tengo palabras para expresar mi gratitud por el Truco de Canela. Después de usarlo consistentemente,
              mis exámenes revelaron una mejora significativa en mis niveles de azúcar en la sangre. Es como si mi
              diabetes estuviera desapareciendo poco a poco.
            </div>
          </div>
          <div class="mt-10">
            <div class="flex gap-2">
              <div>
                <img src="/images/p4.webp" alt="" class="rounded-full">
              </div>
              <div>
                <div class="font-semibold text-md">María González</div>
                <div class="text-sm">
                  <img src="/images/stars.png" alt="" ondragstart="return false" style="position: relative; left: -3px;">
                </div>
              </div>
            </div>
            <div class="mt-3 text-sm leading-5 text-gray-500">
              Desde que comencé a usar Aplicación canela, mi vida cambió por completo. Mis niveles de azúcar en la
              sangre están estables y me siento más energizada que nunca. Es como si la diabetes nunca hubiera sido
              parte de mi vida.
            </div>
          </div>
        </div> -->
      </div>

    </main>

    <footer class="mt-5 mb-20 mx-auto max-w-xl p-7">
      <div class="flex justify-between text-xs">
        <div class="font-satoshi">
          <a href="<?= site_url_base() ?>/terms" class="text-[#02a0fc] font-semibold"><?= lang($locale, 'Purchase Terms') ?></a>
          -
          <a href="<?= site_url_base() ?>/terms" class="text-[#02a0fc] font-semibold"><?= lang($locale, 'Privacy Policy') ?></a>.
        </div>
      </div>
      <div class="flex justify-between text-xs">
        <div class="text-gray-400 font-satoshi">
          <?= date('Y') ?> -
          <?= lang($locale, 'All rights reserved.') ?>
          <a href="<?= site_url_base() ?>/terms"><?= lang($locale, 'Read our terms.') ?></a>
        </div>
        <div class="flex items-center text-[#21c262]">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
              class="bi bi-check2-circle" viewBox="0 0 16 16">
              <path
                d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
              <path
                d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
            </svg>
          </span>
          <span class="font-semibold ms-1 font-satoshi">
            <?= lang($locale, '100% Secure purchase.') ?>
          </span>
        </div>
      </div>
    </footer>
  </div>

</content>