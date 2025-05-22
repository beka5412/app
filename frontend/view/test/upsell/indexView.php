<content>
    <div style="display: table; margin: 50vh auto;">
    /* ------------------------------------------------------------------------------------------------------ */



    <style>
        .rocketpays-upsell-button {
            align-items: center;
            background-image: linear-gradient(144deg,#AF40FF, #5B42F3 50%,#00DDEB);
            border: 0;
            border-radius: 8px;
            box-shadow: rgba(151, 65, 252, 0.2) 0 15px 30px -5px;
            box-sizing: border-box;
            color: #FFFFFF;
            display: flex;
            font-family: Phantomsans, sans-serif;
            font-size: 20px;
            justify-content: center;
            line-height: 1em;
            max-width: 100%;
            min-width: 140px;
            padding: 19px 24px;
            text-decoration: none;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            white-space: nowrap;
            cursor: pointer;
        }

        .rocketpays-upsell-button:active,
        .rocketpays-upsell-button:hover {
            outline: 0;
        }

        .rocketpays-upsell-link,
        .rocketpays-upsell-link:hover {
            color: #7f7f7f;
            text-decoration: underline;
            font-size: 14px;
            font-family: Phantomsans, sans-serif;
        }

        @media (min-width: 768px) {
            .rocketpays-upsell-button {
                font-size: 24px;
                min-width: 196px;
            }
        }
    </style>

    <button class="rocketpays-upsell-button rocketpays_btn_open_modal rocketpays-w-100">SIM, EU QUERO</button>
    <a class="rocketpays-upsell-link rocketpays_btn_open_modal rocketpays-mt-3 rocketpays-display-flex" href="https://google.com/?v=refuse">
        Não, desejo abrir mão desta vantagem.
    </a>

    <script json-id="rocketpays" type="application/json">
    {"accept":"https://google.com/?v=accept"}
    </script>

    

    /* ------------------------------------------------------------------------------------------------------ */



    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap');
        
        .rocketpays-w-100 {
            width: 100%;
        }

        .rocketpays-display-flex {
            display: flex;
        }

        .rocketpays-align-items-end {
            align-items: end;
        }
        
        .rocketpays-justify-content-start {
            justify-content: start;
        }
        
        .rocketpays-justify-content-center {
            justify-content: center;
        }
        
        .rocketpays-justify-content-end {
            justify-content: end;
        }
        
        .rocketpays-justify-content-between {
            justify-content: space-between;
        }

        .rocketpays-modal {
            font-family: 'Poppins';
            display: none;
        }

        .rocketpays-modal.active {
            display: block;
        }

        .rocketpays-cursor-pointer {
            cursor: pointer;
        }

        .rocketpays-mt-1 { margin-top: 5px }
        .rocketpays-mt-2 { margin-top: 10px }
        .rocketpays-mt-3 { margin-top: 15px }
        .rocketpays-mt-4 { margin-top: 20px }
        .rocketpays-mt-5 { margin-top: 25px }

        .rocketpays-modal .rocketpays-bg {
            background: #00000044;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
        }

        .rocketpays-modal .rocketpays-block {
            padding: 30px;
            background: white;
            border-radius: 4px;
            max-width: 300px;
        }

        .rocketpays-modal .rocketpays-title {
            font-weight: bold;
        }

        .rocketpays-modal .rocketpays-description {
            font-size: 12px;
            line-height: 14px;
        }

        .rocketpays-modal select {
            width: 100%;
        }

        .rocketpays-select-installments {
            margin-top: 20px;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        /* --------- */
        
        .rocketpays-modal-button {
            appearance: none;
            background-color: #FAFBFC;
            border: 1px solid rgba(27, 31, 35, 0.15);
            border-radius: 6px;
            box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
            box-sizing: border-box;
            color: #24292E;
            cursor: pointer;
            font-family: -apple-system, system-ui, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
            font-size: 14px;
            font-weight: 500;
            line-height: 20px;
            list-style: none;
            padding: 6px 16px;
            position: relative;
            transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: middle;
            white-space: nowrap;
            word-wrap: break-word;
        }

        .rocketpays-modal-button:hover {
            background-color: #F3F4F6;
            text-decoration: none;
            transition-duration: 0.1s;
        }

        .rocketpays-modal-button:disabled {
            background-color: #FAFBFC;
            border-color: rgba(27, 31, 35, 0.15);
            color: #959DA5;
            cursor: default;
        }

        .rocketpays-modal-button:active {
            background-color: #EDEFF2;
            box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
            transition: none 0s;
        }

        .rocketpays-modal-button:focus {
            outline: 1px transparent;
        }

        .rocketpays-modal-button:before {
            display: none;
        }

        .rocketpays-modal-button:-webkit-details-marker {
            display: none;
        }

        .rocketpays-modal .rocketpays-pin input {
            width: 26px;
            margin-right: 7px;
            text-align: center;
            border: 7px solid white;
            border-radius: 5px;
            box-shadow: 0 0 0px 2px #dedede;
        }
    </style>

    <div class="rocketpays-modal rocketpays_upsell_modal">
        <div class="rocketpays-bg">
            <div class="rocketpays-block">
                <div>
                    <div class="rocketpays-display-flex rocketpays-align-items-end rocketpays-justify-content-between">
                        <div class="rocketpays-title">Pagamento adicional</div>
                        <span class="material-symbols-outlined rocketpays-cursor-pointer rocketpays_upsell_close_modal">close</span>
                    </div>
                    <div class="rocketpays-description">Você será cobrado em <b>R$ <span class="rocketpays_amount">0</span></b> no cartão de crédito.</div>
                </div>
                <div>
                    <input type="hidden" class="rocketpays_payment_method" value="credit_card" />
                    <select class="rocketpays-select-installments rocketpays_select_installments">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>

                    <div class="rocketpays-display-flex rocketpays-justify-content-end rocketpays-mt-1">
                        <button class="rocketpays-modal-button rocketpays_btn_buy">Comprar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rocketpays-modal rocketpays_upsell_modal-verify_email">
        <div class="rocketpays-bg">
            <div class="rocketpays-block">
                <div>
                    <div class="rocketpays-display-flex rocketpays-align-items-end rocketpays-justify-content-between">
                        <div class="rocketpays-title">Confirmar email</div>
                        <span class="material-symbols-outlined rocketpays-cursor-pointer rocketpays_upsell_close_modal">close</span>
                    </div>
                    <div class="rocketpays-description">Informe o código recebido no seu e-mail.</div>
                </div>
                <div class="rocketpays-mt-3">
                    <div class="rocketpays-display-flex rocketpays-justify-content-center rocketpays-pin rocketpays_pin">
                        <input type="text" value="" />
                        <input type="text" value="" />
                        <input type="text" value="" />
                        <input type="text" value="" />
                        <input type="text" value="" />
                    </div>

                    <div class="rocketpays-display-flex rocketpays-justify-content-end rocketpays-mt-4">
                        <button class="rocketpays-modal-button rocketpays_btn_verify_email">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rocketpays-modal rocketpays_upsell_modal-error">
        <div class="rocketpays-bg">
            <div class="rocketpays-block">
                <div>
                    <div class="rocketpays-display-flex rocketpays-align-items-end rocketpays-justify-content-between">
                        <div class="rocketpays-title rocketpays_error_title"></div>
                        <span class="material-symbols-outlined rocketpays-cursor-pointer rocketpays_upsell_close_modal">close</span>
                    </div>
                    <div class="rocketpays-description rocketpays_error_message"></div>
                </div>
                <div class="rocketpays-mt-4">
                    <div class="rocketpays-display-flex rocketpays-justify-content-end rocketpays-mt-4">
                        <button class="rocketpays-modal-button rocketpays_upsell_close_modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    
    /* ------------------------------------------------------------------------------------------------------ */
    </div>
</content>