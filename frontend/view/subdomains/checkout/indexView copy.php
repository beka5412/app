<?php
use Backend\Enums\Orderbump\EOrderbumpStatus;
use Backend\Enums\Product\EProductPaymentType;
?>
<content ready="ready"
  data-favicon="<?php echo $checkout?->favicon ?? 'https://painel.rocketleads.com.br/images/plataformas/rocketpays_dark.png'; ?>">
  
  <script type="application/json" json-id="env">
    <?php echo json_encode([
      "STRIPE_PUBKEY" => env('STRIPE_PUBKEY'),
    ]); ?>
  </script>
  <script type="application/json" json-id="customer">
  {
    "upsell_id": "<?php echo $upsell->id ?? ''; ?>",
    "price_var": "<?php echo $product_link->id ?? ''; ?>"
  }
  </script>
  <script type="application/json" json-id="checkout">
    <?php echo json_encode([
      "id" => $checkout?->id ?? '',
      "product_id" => $product?->id ?? '',
      "stripe_client_secret" => $payment_intent?->client_secret ?? '',
      "store_user_id" => $product?->user_id ?? 0,
      "total" => $total,
      "total_int" => intval($total * 100),
    ]); ?>
  </script>

  <div>
    <header class="bg-[#FF0000]">
      <div class="mx-auto max-w-xl p-7" id="conteudo">
        <p id="cronometro" class="text-sm font-semibold text-white text-center"></p>
      </div>
    </header>
  
    <main>
      <div class="mx-auto max-w-xl" style="margin-top: 5px;">
        <div class="mt-10 border border-gray-200 rounded bg-white py-8 px-8" style="margin: 20px;">
          <div class="flex justify-between">
            <div class="flex gap-2">
              <div class="bg-gray-200 rounded flex justify-center items-center  w-[30px] h-[30px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-cart-check-fill color-gray-900" viewBox="0 0 16 16" style="height: 24px">
                  <path
                    d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708" />
                </svg>
              </div>
              <span class="text-lg font-semibold">Tu compra</span>
            </div>
            <div class="flex gap-1 items-center">
              <!-- <span class="text-xs">Total:</span> -->
              <div class="flex font-semibold gap-1">
                <span>US$</span>
                <span><?php echo number_format($total, 2, '.', ',') ?></span>
              </div>
            </div>
          </div>
          <div class="flex gap-2 mt-5 mb-10">
            <div>
              <img src="<?php echo $product->image; ?>" alt="" class="rounded w-[45px] h-[45px]">
            </div>
            <div>
              <div>
                <span class="text-md font-semibold text-black-300"><?php echo $product->name ?></span>
              </div>
              <div class="flex font-semibold gap-1">
                <span>US$</span>
                <span><?php echo number_format($total, 2, '.', ',') ?></span>
              </div>
            </div>
          </div>
        </div>
  
        <form id="payment-form" class="mt-10 border border-gray-200 rounded bg-white py-4 px-4" style="margin: 20px;">
  
  
          <div class="mt-5">
            <label for="" class="label" id="labelName">Nombre</label>
            <input type="text" class="input" name="name" id="inputName">
          </div>
  
          <div class="mt-3">
            <label for="" class="label">Email</label>
            <input type="text" class="input" name="email" id="inputEmail">
          </div>
  
          <div id="payment-element" style="margin-top: 20px">
          </div>
  
          <div id="payment-errors" role="alert"></div>
          <div id="messages" role="alert" style="display: none; color: #df1b41; margin-top: 10px"></div>
  
          <button id="submit" class="button mt-5 transition ease-in-out duration-100 hover:scale-105 flex justify-center">
            <div id="btnLoader" class="loader" style="display: none"></div>
            <div id="btnText" class="py-1">Pagar</div>
          </button>
  
          <div class="mx-auto max-w-xl" style="margin-top: 2px;">
            <h2 style="font-size: 0.80rem;" class="text-center mt-10 font-medium text-gray-500">Si necesita
              3DSecurity, siga los pasos a continuación</h2>
            <div style="margin-bottom: 8px;"></div>
  
            <div class="mt-10" style="margin-top: -1px;">
              <img src="/images/3dsecure.gif" class="rounded" alt=""
                style="width: 91%; margin-left: auto; margin-right: auto;">
            </div>
          </div>
        </form>
  
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
        <div class="text-gray-400">2024 - Todos los derechos reservados.</div>
        <div class="flex items-center text-[#21c262]">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check2-circle"
              viewBox="0 0 16 16">
              <path
                d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
              <path
                d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
            </svg>
          </span>
          <span class="font-semibold ms-1">Compra 100% segura.</span>
        </div>
      </div>
    </footer>
  </div>

</content>