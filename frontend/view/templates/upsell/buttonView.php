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

<button class="rocketpays-upsell-button rocketpays_btn_open_modal rocketpays-w-100"><?php echo $accept_text ?: 'SIM, EU QUERO'; ?></button>
<a class="rocketpays-upsell-link rocketpays_btn_open_modal rocketpays-mt-3 rocketpays-display-flex" href="<?php echo $refuse_page; ?>">
    <?php echo $refuse_text ?: 'Não, desejo abrir mão desta vantagem.'; ?>
</a>

<script json-id="rocketpays" type="application/json">
{"accept":"<?php echo $accept_page; ?>"}
</script>



/* ------------------------------------------------------------------------------------------------------ */

