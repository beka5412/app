<?php $sku = $default_checkout?->sku; ?>
<content>
	<AffiliationMenu />
	<div>
		<!-- card pagina de vendas -->
		<div class="card card-bordered card-inner card-preview mb-2">
			<div class="row gy-4">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="form-label" for="default-01">Página de Vendas</label>
						<div class="form-control-wrap">
							<input type="text" class="form-control" id="default-01" placeholder="Input placeholder"
								value="<?php echo $product?->landing_page; ?>" onkeypress="return false" onkeyup="return false"
								onkeydown="return false">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<label class="form-label" for="default-01">.</label>
					<div class="form-control-wrap">
						<a href="javascript:;" data-link="<?php echo $product?->landing_page; ?>" click="copyLink"
							class="btn btn-primary"><em class="icon ni ni-copy"></em><span>Copiar link</span></a>
						<a href="javascript:;" class="btn btn-outline-primary"><em
								class="icon ni ni-share"></em><span>Compartilhar</span></a>
						<a href="javascript:;" class="btn btn-outline-primary"><em class="icon ni ni-external"></em><span>Visualizar
								Pagina</span></a>
					</div>
				</div>
			</div>
		</div>
		<!-- card links -->
		<div class="card card-bordered card-inner card-preview mb-2">
			<div class="row gy-4">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="form-label" for="default-01">Checkout Padrão</label>
						<div class="form-control-wrap">
							<input type="text" class="form-control" id="default-01" placeholder="Input placeholder"
								value="<?php echo get_subdomain_serialized('checkout')."/{$sku}?aff={$user->sku}"; ?>"
							onkeypress="return false" onkeyup="return false" onkeydown="return false">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<label class="form-label" for="default-01">.</label>
					<div class="form-control-wrap">
						<a data-link="<?php echo get_subdomain_serialized('checkout')."/{$sku}?aff={$user->sku}"; ?>" click="copyLink"
							class="btn btn-primary"><em class="icon ni ni-copy"></em><span>Copiar link</span></a>
						<a href="#" class="btn btn-outline-primary"><em class="icon ni ni-share"></em><span>Compartilhar</span></a>
						<a href="<?php echo get_subdomain_serialized('checkout')."/{$sku}?aff={$user->sku}"; ?>" target="_blank" class="btn
							btn-outline-primary"><em class="icon ni ni-external"></em><span>Visualizar
								Pagina</span></a>
					</div>
				</div>
			</div>
		</div>

		<!-- card links -->
		<?php foreach ($product->checkouts as $checkout): ?>
			<label class="form-label">Variação de preço de
				<?php echo $checkout->name; ?>
			</label>
			<?php foreach ($product->product_links as $item): 
					$link = get_subdomain_serialized('checkout')."/{$checkout->sku}/{$item?->slug}?aff={$user->sku}"; ?>
			<div class="card card-bordered card-inner card-preview mb-2">
				<div class="row gy-4">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="form-label" for="default-01">
								<?php echo $item?->slug; ?>
							</label>
							<div class="form-control-wrap">
								<input type="text" class="form-control" id="default-01" placeholder="" value="<?php echo $link; ?>"
									onkeypress="return false" onkeyup="return false" onkeydown="return false">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<label class="form-label" for="default-01">.</label>
						<div class="form-control-wrap">
							<a href="javascript:;" class="btn btn-primary" data-link="<?php echo $link; ?>" click="copyLink"><em
									class="icon ni ni-copy"></em><span>Copiar link</span></a>
							<a href="#" class="btn btn-outline-primary"><em class="icon ni ni-share"></em><span>Compartilhar</span></a>
							<a href="<?php echo $link; ?>" target="_blank" class="btn btn-outline-primary"><em
									class="icon ni ni-external"></em><span>Visualizar
									Pagina</span></a>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</div>
</content>