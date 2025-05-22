<?php use Backend\Enums\Coupon\{ECouponStatus, ECouponType}; ?>
<title>Editar cupom <?php echo $coupon->name; ?></title>

<content>
    <div class="nk-content-body frm_edit_coupon">
        <div class="nk-block-head">
            <div class="nk-block-between g-3">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">
                        Cupom #<?php echo $coupon->id; ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-bordered card-preview">
                                    <div class="card-inner">
                                        <div class="preview-block">
                                            <div class="row gy-4">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Status</label>
                                                        <div class="form-control-wrap">
                                                            <div class="form-group">
                                                                <div class="form-control-wrap">
                                                                    <select class="form-select inp_status">
                                                                        <option <?php if ($coupon->status == ECouponStatus::PUBLISHED->value): ?> selected="" <?php endif; ?> 
                                                                            value="<?php echo ECouponStatus::PUBLISHED->value; ?>">Ativo</option>
                                                                        <option <?php if ($coupon->status == ECouponStatus::DISABLED->value): ?> selected="" <?php endif; ?> 
                                                                            value="<?php echo ECouponStatus::DISABLED->value; ?>">Inativo</option>
                                                                        <option <?php if ($coupon->status == ECouponStatus::DRAFT->value): ?> selected="" <?php endif; ?> 
                                                                            value="<?php echo ECouponStatus::DRAFT->value; ?>">Rascunho</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Código</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control inp_code"
                                                                value="<?php echo $coupon->code; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Descrição</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control inp_description"><?php echo $coupon->description; ?></input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-01">Tipo</label>
                                                        <div class="form-control-wrap">
                                                            <div class="form-group">
                                                                <div class="form-control-wrap">
                                                                    <select class="form-select inp_type">
                                                                        <option <?php if ($coupon->status == ECouponType::PERCENT->value): ?> selected="" <?php endif; ?> 
                                                                            value="<?php echo ECouponType::PERCENT->value; ?>">Porcentagem</option>
                                                                        <option <?php if ($coupon->status == ECouponType::PRICE->value): ?> selected="" <?php endif; ?> 
                                                                            value="<?php echo ECouponType::PRICE->value; ?>">Preço</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Desconto</label>
                                                        <div class="form-control-wrap">
                                                            <div class="input-group">
                                                                <input class="form-control inp_discount"
                                                                    value="<?php echo $coupon->type == 'percent' ? $coupon->discount : number_format($coupon->discount, 2, ',', '.'); ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Valor mínimo da compra</label>
                                                        <div class="form-control-wrap">
                                                            <div class="input-group">
                                                                <input class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Data de início</label>
                                                        <div class="form-control-wrap">
                                                        <div class="form-icon form-icon-left">
                                                            <em class="icon ni ni-calendar"></em>
                                                        </div>
                                                        <input type="text" class="form-control date-picker" data-date-format="dd-mm-yyyy">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Data de término</label>
                                                        <div class="form-control-wrap">
                                                        <div class="form-icon form-icon-left">
                                                            <em class="icon ni ni-calendar"></em>
                                                        </div>
                                                        <input type="text" class="form-control date-picker" data-date-format="dd-mm-yyyy">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Total de cupons disponíveis</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <hr class="preview-hr">
                                                <div class="c-right ps-1 mt-1 d-flex justify-content-end">
                                                    <button click="couponOnSubmit" class="btn btn-primary">Salvar</button>
                                                </div>
                                        </div>
                                    </div>
            </div>
        </div>                            
     </div>
</content>