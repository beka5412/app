<?php use Backend\Enums\Pixel\EPixelPlatform; ?>
<title>Editar pixel</title>

<content>
	<div class="nk-content-body">

		<div class="nk-block-head">
			<div class="nk-block-between g-3">
				<div class="nk-block-head-content">
					<h3 class="nk-block-title page-title">
						<?php echo $product->sku; ?>
					</h3>
				</div>
				<div class="nk-block-head-content">
					<a to="<?php echo site_url() . "/product/" . $product->id . "/plans"; ?>"
						href="<?php echo site_url() . "/product/" . $product->id . "/plans"; ?>"
						class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
						<em class="icon ni ni-arrow-left"></em>
						<span>Voltar</span>
					</a>
					<a to="<?php echo site_url() . "/product/" . $product->id . "/plans"; ?>"
						href="<?php echo site_url() . "/product/" . $product->id . "/plans"; ?>"
						class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none">
						<em class="icon ni ni-arrow-left"></em>
					</a>
				</div>
			</div>
		</div>

		<div class="nk-block frm_edit_plan">
			<div class="nk-tb-list is-separate mb-3">

				<div class="card card-bordered card-preview">
					<div class="card-inner">

						<div class="row gy-4">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="form-label">SKU</label>
									<div class="form-control-wrap">
										<input class="form-control inp_plan_slug" value="<?php echo $plan->slug; ?>" disabled="" />
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label class="form-label">Nome</label>
									<div class="form-control-wrap">
										<input class="form-control inp_plan_name" value="<?php echo $plan->name; ?>" />
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label class="form-label">Preço</label>
									<div class="form-control-wrap">
										<input class="form-control inp_plan_price" value="<?php echo $plan->price; ?>" />
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label class="form-label">Período da recorrência</label>
									<div class="form-control-wrap">
										<select class="form-select inp_recurrence_period">
											<option value="daily" <?php if ($plan->recurrence_interval == 'daily' && $plan->recurrence_interval_count == 1): ?> selected="" <?php endif; ?>>Diário</option>
											<option value="monthly" <?php if ($plan->recurrence_interval == 'month' && $plan->recurrence_interval_count == 1): ?> selected="" <?php endif; ?>>Mensal</option>
											<option value="bimonthly" <?php if ($plan->recurrence_interval == 'month' && $plan->recurrence_interval_count == 2): ?> selected="" <?php endif; ?>>Bimestral</option>
											<option value="quarterly" <?php if ($plan->recurrence_interval == 'month' && $plan->recurrence_interval_count == 3): ?> selected="" <?php endif; ?>>Trimestral</option>
											<option value="biannual" <?php if ($plan->recurrence_interval == 'month' && $plan->recurrence_interval_count == 6): ?> selected="" <?php endif; ?>>Semestral</option>
											<option value="yearly" <?php if ($plan->recurrence_interval == 'year' && $plan->recurrence_interval_count == 1): ?> selected="" <?php endif; ?>>Anual</option>
										</select>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="c-right ps-1 mt-1 d-flex justify-content-end">
					<button click="planOnSubmit" class="btn btn-primary">Salvar</button>
				</div>

			</div>
		</div>

	</div>
</content>