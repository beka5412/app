<title>Cadastre-se</title>
<content>

	<div class="nk-app-root">
		<!-- main @s -->
		<div class="nk-main ">
			<!-- wrap @s -->
			<div class="nk-wrap nk-wrap-nosidebar">
				<!-- content @s -->
				<div class="nk-content ">
					<div class="nk-block nk-block-middle nk-auth-body wide-sm">
						<div class="brand-logo pb-4 text-center">
							<a href="/" class="logo-link">
								<img style="width: 160px; max-height: fit-content;" class="logo-light logo-img logo-img-lg" src="./images/logo.png" srcset="./images/logo.png 2x" alt="logo">
								<img style="width: 160px; max-height: fit-content;" class="logo-dark logo-img logo-img-lg" src="./images/logo-dark.png" srcset="./images/logo-dark.png 2x" alt="logo-dark">
							</a>
						</div>
						<div class="card card-bordered">
							<div class="card-inner card-inner-lg">
								<div class="nk-block-head">
									
								</div>
								<div>
									<div class="row mb-3">
										<label for="inpName" class="col-md-4 col-form-label text-md-end">Nome</label>
										<div class="col-md-6">
											<input id="inpName" type="text" class="form-control">
										</div>
									</div>

									<div class="row mb-3">
										<label for="inpEmail" class="col-md-4 col-form-label text-md-end">E-mail</label>
										<div class="col-md-6">
											<input id="inpEmail" type="text" class="form-control">
										</div>
									</div>

									<div class="row mb-3">
										<label for="inpPassword" class="col-md-4 col-form-label text-md-end">Criar senha</label>
										<div class="col-md-6">
											<input id="inpPassword" type="password" class="form-control">
										</div>
									</div>

									<div class="row mb-3">
										<label for="inpPasswordConfirm" class="col-md-4 col-form-label text-md-end">Confirmar senha</label>
										<div class="col-md-6">
											<input id="inpPasswordConfirm" type="password" class="form-control">
										</div>
									</div>

									<div class="row mb-3">
										<label for="inpDoc" class="col-md-4 col-form-label text-md-end">CPF</label>
										<div class="col-md-6">
											<input id="inpDoc" type="text" class="form-control">
										</div>
									</div>

									<div class="row mb-3">
										<label for="inpBirthdate" class="col-md-4 col-form-label text-md-end">Data de nascimento</label>
										<div class="col-md-6">
											<input id="inpBirthdate" type="text" class="form-control">
										</div>
									</div>

									<div class="row mb-3">
										<label for="inpPhone" class="col-md-4 col-form-label text-md-end">Telefone</label>
										<div class="col-md-6">
											<input id="inpPhone" type="text" class="form-control">
										</div>
									</div>

									<div class="row mb-3">
										<label for="inpPhone" class="col-md-4 col-form-label text-md-end"></label>
										<div class="col-md-6">
											<div class="mb-2">
												<div class="error login-error alert alert-danger"></div>
											</div>
											<button type="button w-100" style="width:100%;text-align:center;display:block" click="onSubmit" class="btn btn-primary">
												<?= __('Sign up') ?>
											</button>
										</div>
									</div>


								</div>
								<div class="form-note-s2 text-center pt-4"> 
									<?= __('Already have an account?') ?> 
									<a href="/login" class="a-dark" style="font-weight: 500; color: #02a0fc"><strong><?= __('Sign in instead') ?></strong></a>.
								</div>
								<div class="form-note-s2 text-center">
									<span><?= __('By registering, you agree to our terms.') ?></span>
									<a href="/terms" class="a-dark" style="font-weight: 500; color: #02a0fc"><?= __('Read our terms.') ?></a>
								</div>
							</div>

						</div>
					</div>
				</div>

			</div>
			<!-- wrap @e -->
		</div>
		<!-- content @e -->
	</div>
	<!-- main @e -->
	</div>


</content>