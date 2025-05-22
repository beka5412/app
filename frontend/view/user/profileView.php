<content>
<div class="nk-content-body">
    <card class="mt-2">
                    Profile <?php echo $title; ?>
<input type="text" class="form-control inp_price_promo" id="default-01"
keyup="$inputCurrency"
keydown="$onlyNumbers"
blur="$inputCurrency"
load="element.value = currency(element.value)"
placeholder="">
                    <button click="profileOnSubmit">Salvar</button>
    </card>
</div>
</content>
