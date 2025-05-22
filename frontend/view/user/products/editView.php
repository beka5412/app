<?php use Backend\Enums\Product\{EProductWarrantyTime, EProductType, EProductPaymentType, EProductDelivery, EProductRequestStatus}; 

if ($product->attachment_file)
{
  $product_attachment_icon_ext = pathinfo($product->attachment_file, PATHINFO_EXTENSION);
  $product_attachment_icon_url = "/images/extensions/$product_attachment_icon_ext.png";
  $product_attachment_icon_url = file_exists(base_path("frontend/public$product_attachment_icon_url")) ? $product_attachment_icon_url : '';
}
?>

<title>Editar produto
  <?php echo $product->name; ?>
</title>

<content>

  <style>
    #productUploading
    {
      display: none;
      position: relative;
      top: -2px;
    }

    .product-upload-icon
    {
      width: 110px;
      margin-right: 30px;
    }
  </style>

  <div class="nk-content-body">
    <!-- <?php if (($product->last_request()->status ?? '') == EProductRequestStatus::PENDING->value): ?>
      <div class="alert alert-warning">No momento você ainda não pode vender devido ao produto estar sob análise.</div>
    <?php elseif (($product->last_request()->status ?? '') == EProductRequestStatus::REJECTED->value): ?>
      <div class="alert alert-danger">Este produto não foi aprovado. Faça os ajustes solicitados no e-mail e tente salvar o produto novamente para enviar à análise.</div>
    <?php endif ?> -->

    <ProductMenu />

    <div class="tab-content">
      <div class="tab-pane active frm_edit_product" id="tabGeral">
        <div class="nk-block">
          <div class="row g-gs">
            <div class="col-12 d-flex">
              <div class="col-md-12">
                <div class="card card-bordered card-preview">
                  <div class="card-inner">
                    <div class="preview-block">
                      <div class="row gy-4">
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="form-label"><?= __('Name') ?></label>
                            <div class="form-control-wrap">
                              <input class="form-control inp_name" value="<?php echo $product->name; ?>" />
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Product category') ?></label>
                            <div class="form-control-wrap">
                              <div class="form-group">
                                <div class="form-control-wrap">
                                  <select class="form-select inp_category">
                                    <?php foreach ($categories as $category): ?>
                                    <option <?php if ($category->id == ($product->category->id ?? 0)): ?> selected="" <?php endif; ?> value="<?php echo $category->id; ?>">
                                      <?php echo $category->name; ?>
                                    </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Language') ?></label>
                            <div class="form-control-wrap">
                              <div class="form-group">
                                <div class="form-control-wrap">
                                  <select class="form-select inp_lang">
                                    <?php foreach (languages(true) as $language): ?>
                                        <option <?= $product->language_id === $language->id ? 'selected=""' : '' ?> value="<?php echo $language->id; ?>"><?= __($language->name) ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group"><label class="form-label" for="default-textarea">
                            <?= __('Product description') ?></label>
                            <div class="form-control-wrap">
                                <textarea rows="13" class="form-control no-resize inp_description" id="default-textarea" required><?php echo $product->description; ?></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="productImage"><?= __('Product image') ?>
                            (<?= __('Recommended size') ?>: 300x250 pixels)</label>
                           
                          <input type="hidden" class="inp_image" value="<?php echo $product->image; ?>">
                          <input type="file" id="productImage" class="d-none" change="productUploadImage">
                          <div class="ez-dropzone">
                            <div class="dz-message" data-dz-message="">
                              <span class="dz-message-text img_product_image">
                                <?php if ($product->image): ?>
                                <img src="<?php echo $product->image ?? 'images/demo-product.webp'; ?>" />
                                <?php else: ?>
                                <?= __('Upload image') ?>
                                <?php endif; ?>
                              </span>
                              <span class="dz-message-or">&nbsp;</span>
                              <button type="button" class="btn btn-primary"
                                onclick="productImage.click()"><?= __('Upload image') ?></button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr class="preview-hr">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
                <div class="card card-bordered card-preview">
                  <div class="card-inner">
                    <div class="preview-block">
                      <div class="row gy-4">

                        <div class="col-sm-3">
                          <div class="form-group">
                            <label class="form-label"><?= __('Type of payment') ?></label>
                            <div class="form-control-wrap">
                              <select class="form-select inp_payment_type" toggle-change="payment-type"
                              load="toggleStatement(element, true)"
                              >
                              <!-- [no-dispatch-change-onload] -->
                                <?php $v = EProductPaymentType::UNIQUE->value; ?>
                                <option <?php if ($v==$product->payment_type): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?= __('Single payment') ?></option>
                                <?php $v = EProductPaymentType::RECURRING->value; ?>
                               <option <?php if ($v==$product->payment_type): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?= __('Recurrent') ?></option> 
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3" toggle-change-item="payment-type-recurring">
                          <div class="form-group">
                            <label class="form-label"><?= __('Recurrence period') ?></label>
                            <div class="form-control-wrap">
                              <select class="form-select inp_recurrence_period">
                                <option value="daily" <?php if ($product->recurrence_interval == 'day' && $product->recurrence_interval_count == 1): ?> selected="" <?php endif; ?>><?= __('Daily') ?></option>
                                <option value="weekly" <?php if ($product->recurrence_interval == 'week' && $product->recurrence_interval_count == 1): ?> selected="" <?php endif; ?>><?= __('Weekly') ?></option>
                                <option value="fortnightly" <?php if ($product->recurrence_interval == 'week' && $product->recurrence_interval_count == 2): ?> selected="" <?php endif; ?>><?= __('Fortnightly') ?></option>
                                <option value="monthly" <?php if ($product->recurrence_interval == 'month' && $product->recurrence_interval_count == 1): ?> selected="" <?php endif; ?>><?= __('Monthly') ?></option>
                                <option value="twomonthly" <?php if ($product->recurrence_interval == 'month' && $product->recurrence_interval_count == 2): ?> selected="" <?php endif; ?>><?= __('Two monthly') ?></option>
                                <option value="quarterly" <?php if ($product->recurrence_interval == 'month' && $product->recurrence_interval_count == 3): ?> selected="" <?php endif; ?>><?= __('Quarterly') ?></option>
                                <option value="semiannual" <?php if ($product->recurrence_interval == 'month' && $product->recurrence_interval_count == 6): ?> selected="" <?php endif; ?>><?= __('Semiannual') ?></option>
                                <option value="yearly" <?php if ($product->recurrence_interval == 'year' && $product->recurrence_interval_count == 1): ?> selected="" <?php endif; ?>><?= __('Yearly') ?></option>
                              </select>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label class="form-label"><?= __('Product type') ?></label>
                            <div class="form-control-wrap">
                              <select class="form-select inp_product_type"
                              >
                                <!-- toggle-change="product_type" -->
                                <?php $v = EProductType::DIGITAL->value; ?>
                                <option <?php if ($v==$product->type): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>" disabled
                                  >Digital</option>
                                <!-- <?php $v = EProductType::PHYSICAL->value; ?>
                                <option <?php if ($v==$product->type): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>" disabled>Físico</option> -->
                              </select>
                            </div>
                          </div>
                        </div>
                       
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label class="form-label"><?= __('Digital content delivery') ?></label>
                            <div class="form-control-wrap">
                              <select class="form-select inp_product_delivery" toggle-change="delivery">
                                <?php $v = EProductDelivery::NOTHING->value; ?>
                                <option <?php if ($v==$product->delivery): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?= __('Just sell') ?></option>
                                <?php $v = EProductDelivery::MEMBERKIT->value; ?>
                                <option <?php if ($v==$product->delivery): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>">Memberkit</option>
                                <?php $v = EProductDelivery::CADEMI->value; ?>
                                <option <?php if ($v==$product->delivery): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>">Cademi</option>
                                <?php $v = EProductDelivery::ROCKETMEMBER->value; ?>
                                <option <?php if ($v==$product->delivery): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>">Migraz Members</option>
                                <?php $v = EProductDelivery::ASTRONMEMBERS->value; ?>
                                <option <?php if ($v==$product->delivery): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>">Astronmembers</option>
                                <?php $v = EProductDelivery::DOWNLOAD->value; ?>
                                <option <?php if ($v==$product->delivery): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?= __('Download file') ?></option>
                                <?php $v = EProductDelivery::EXTERNAL->value; ?>
                                <option <?php if ($v==$product->delivery): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?= __('External link') ?></option>
                                </select>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>

            <div>
              
            <div class="card card-bordered card-preview">
              <div class="card-inner">
                <div>
                    <div>
                      <div class="form-group" toggle-change-item="delivery-download">
                        <div>
                          <div class="d-flex">
                            <label class="form-label" for="inp_product_upload_attachment"><?= __('Upload') ?></label>
                            <div id="productUploading" class="ms-2">
                              <Loading />
                            </div>
                          </div>
                          <div class="d-flex">
                            <div>
                              <img id="imgAttachmentPreview" src="<?= $product->attachment_file ? $product_attachment_icon_url : '/images/extensions/file.png' ?>" class="product-upload-icon" alt="">
                            </div>
                            <div>
                              <div class="form-control-wrap">
                                <input type="hidden" class="inp_product_attachment_file"
                                  value="<?php echo $product->attachment_file; ?>">
                                <input type="file" class="form-control" change="productUploadAttachment" id="inp_product_upload_attachment"
                                  value="<?php echo $product->attachment_file; ?>" placeholder="">
                              </div>
                              <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-danger" click="productRemoveAttachment">
                                  <em class="icon ni ni-trash"></em>
                                  Remover
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <div class=""  toggle-change-item="delivery-external">
                        <div class="form-group">
                          <label class="form-label" for="default-01"><?= __('Product link') ?></label>
                          <div class="form-control-wrap">
                            <input type="text" class="form-control inp_product_attachment_url" id="default-01"
                              value="<?php echo $product->attachment_url; ?>" placeholder="">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 d-flex">
              <div class="col-md-12">
                <div class="card card-bordered card-preview">
                  <div class="card-inner">
                    <div class="preview-block">
                      <div class="row gy-4 mt-4">
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Is the product free?') ?></label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input inp_is_free" <?php if ($product->is_free > 0): ?> checked="" <?php endif; ?>
                                name="reg-public" id="switch-is-free">
                                <!-- !toggle=".div_product_price, .div_product_price_promo" -->
                                <!-- load="toggleStatement(element, true)" -->
                                <label class="custom-control-label" for="switch-is-free"></label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3" style="display:block">
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Product value') ?></label>
                            <div class="form-control-wrap">
                              <input class="form-control inp_price" keyup="$inputCurrency" keydown="$onlyNumbers"
                                blur="$inputCurrency" load="element.value = currency(element.value)"
                                value="<?php echo $product->price; ?>">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Currency') ?></label>
                            <div class="form-control-wrap">
                              <select class="form-select inp_currency">
                                <option <?php if ("brl" == $product->currency): ?> selected="" <?php endif; ?> value="brl">BRL</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row gy-4 mt-4">
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label class="form-label"><?= __('Does this product have different values?') ?></label>
                            <div class="form-group">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" <?php if (count($product->product_links)): ?> checked="" <?php endif; ?>
                                name="reg-public" id="switch-is-diff-val"
                                toggle=".div_product_diff_val_bt, .div_product_diff_val"
                                load="toggleStatement(element, true)">
                                <label class="custom-control-label" for="switch-is-diff-val"></label>
                              </div>
                            </div>
                          </div>
                        </div>      
                        <div class="col-sm-8 div_product_diff_val <?php if (count($product->product_links)): ?> d-block <?php else: ?> d-none <?php endif; ?>">
                          <?php foreach ($product->product_links as $item): ?>
                          <div class="row mb-4">
                            <input type="hidden" class="inp_pd_id" value="<?php echo $item->id; ?>" />
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label class="form-label" for="default-01">Slug</label>
                                <div class="form-control-wrap">
                                  <input class="form-control inp_pd_slug" value="<?php echo $item->slug; ?>" type="slug">
                                </div>
                                <span class="fs-10px d-block"><?= get_subdomain_serialized('checkout') ?>/slug</span>
                              </div>
                            </div>
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label class="form-label" for="default-01"><?= __('Product price') ?></label>
                                <div class="form-control-wrap">
                                  <input class="form-control inp_pd_val" keyup="$inputCurrency" keydown="$onlyNumbers"
                                    blur="$inputCurrency" load="element.value = currency(element.value)"
                                    value="<?php echo $item->amount; ?>">
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-1">
                              <a href="javascript:;" class="del-prod-link" click="delVariation"><em class="icon ni ni-trash"></em></a>
                            </div>
                          </div>
                          <?php endforeach; ?>
                          <style>.del-prod-link { position: relative; top: 40px; } @media screen and (max-width: 576px) { .del-prod-link { top: 10px } } </style>
                          <div class="row mb-4">
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label class="form-label" for="default-01">Slug</label>
                                <div class="form-control-wrap">
                                  <input class="form-control inp_pd_slug" value="" type="slug">
                                </div>
                                <span class="fs-10px d-block"><?= get_subdomain_serialized('checkout') ?>/slug</span>
                              </div>
                            </div>
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label class="form-label" for="default-01"><?= __('Product price') ?></label>
                                <div class="form-control-wrap">
                                  <input class="form-control inp_pd_val" keyup="$inputCurrency" keydown="$onlyNumbers"
                                    blur="$inputCurrency" load="element.value = currency(element.value)"
                                    value="<?php echo 0; ?>">
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-1 col_acts" style="display: none">
                              <a href="javascript:;" class="del-prod-link" click="delVariation"><em class="icon ni ni-trash"></em></a>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-2 div_product_diff_val_bt  <?php if (count($product->product_links)): ?> d-block <?php else: ?> d-none <?php endif; ?>">
                          <div style="display: table; height: calc(100% - 36px); margin-left: auto;">
                            <div style="flex-direction: revert; align-items: end; display: flex; height: 100%;">
                              <button type="button" class="btn btn-primary" click="addVariation" style="margin-top: 32px;"><?= __('Add') ?></button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 d-flex">
              <div class="col-md-12">
                <div class="card card-bordered card-preview">
                  <div class="card-inner">
                    <div class="preview-block">
                      <div class="row gy-4">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Sales page') ?></label>
                            <div class="form-control-wrap">
                              <div class="input-group">
                                <input type="text" value="<?php echo $product->landing_page; ?>" class="form-control inp_landing_page" required id="basic-url">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Support email') ?></label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control inp_support_email" id="default-01"
                                value="<?php echo $product->support_email ?: $user->email; ?>" placeholder="">
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="form-label" for="default-01"><?= __('Producer display name') ?></label>
                            <div class="form-control-wrap">
                              <input type="text" class="form-control inp_author" id="default-01"
                                value="<?php echo $product->author ?: $user->name; ?>" placeholder="">
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="form-label"><?= __('Warranty time') ?></label>
                            <div class="form-control-wrap">
                              <select class="form-select inp_warranty_time">
                                <?php $v = EProductWarrantyTime::ONE_WEEK->value; ?>
                                <option <?php if ($v==$product->warranty_time): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?php echo $v; ?> <?= __('Days') ?></option>
                                <?php $v = EProductWarrantyTime::TWO_WEEKS->value; ?>
                                <option <?php if ($v==$product->warranty_time): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?php echo $v; ?> <?= __('Days') ?></option>
                                <?php $v = EProductWarrantyTime::THREE_WEEKS->value; ?>
                                <option <?php if ($v==$product->warranty_time): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?php echo $v; ?> <?= __('Days') ?></option>
                                <?php $v = EProductWarrantyTime::ONE_MONTH->value; ?>
                                <option <?php if ($v==$product->warranty_time): ?> selected="" <?php endif; ?> value="<?php echo $v; ?>"><?php echo $v; ?> <?= __('Days') ?></option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="c-right ps-1 mt-1 d-flex justify-content-end">
              <button click="productDestroy" data-id="<?php echo $product->id; ?>" class="btn">Excluir produto</button>
              <button click="productOnSubmit" class="btn btn-primary"><?= __('Save') ?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</content>
