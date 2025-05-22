<content>
    <div class="nk-content-body">
        <!-- <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Perfil
                    </h3>
                </div>
            </div>
        </div> -->

        <div class="nk-block">
            <div class="card">
                <div class="card-inner">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3>Perfil</h3>
                        </div>
                        <!-- <div>
                            <button class="btn btn-primary notification-small-button d-inline-block">Alterar</button>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex justify-content-center align-items-center flex-column h-100">
                                <div class="d-flex justify-content-center">
                                    <div class="user-avatar" style="width: 80px; height: 80px; font-size: 40px;">
                                        <em class="icon ni ni-user-alt" id="profileIconPhoto" style="display: <?= $user->photo ? 'none': 'block' ?>"></em>
                                        <img src="<?= $user->photo ?: '' ?>" alt="" id="profileImgPhoto" style="display: <?= $user->photo ? 'block': 'none' ?>">
                                    </div>
                                </div>
                                <div>
                                    <input type="file" class="d-none" id="profilePhoto" change="profileUploadPhotoOnChange">
                                </div>
                                <div class="d-flex justify-content-center mt-2">
                                    <button class="btn btn-primary notification-small-button d-inline-block" click="profileUploadPhotoOnClick">Alterar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label" for="">Nome</label>
                                            <input type="text" class="form-control" name="name" value="<?= $profile->name ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="">CPF</label>
                                            <input type="text" class="form-control" name="doc" value="<?= $profile->document ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <label class="form-label" for="">E-mail</label>
                                    <input type="text" class="form-control" name="email" value="<?= $profile->email ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4"></div>

            <div class="card">
                <div class="card-inner">
                    <div class="mt-4 mb-2 d-flex justify-content-between">
                        <div>
                            <h3>Endereço</h3>
                        </div>
                        <!-- <div>
                            <button class="btn btn-primary notification-small-button d-inline-block">Alterar</button>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="">CEP</label>
                                <input type="text" class="form-control" name="zipcode" value="<?= $address->zipcode ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="">Rua</label>
                                <input type="text" class="form-control" name="street" value="<?= $address->street ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label" for="">Nº</label>
                                <input type="text" class="form-control" name="number" value="<?= $address->number ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label" for="">Bairro</label>
                                <input type="text" class="form-control" name="neighborhood" value="<?= $address->neighborhood ?>">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label" for="">Cidade</label>
                                <input type="text" class="form-control" name="city" value="<?= $address->city ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label" for="">Estado</label>
                                <input type="text" class="form-control" name="state" value="<?= $address->state ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button click="profileOnSubmit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</content>