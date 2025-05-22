<title>Settings</title>

<content>

<div class="nk-content-body">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Configurações</h3>
                <div class="nk-block-des text-soft">
                    <p>Gerencie todas as configurações do sistema.</p>
                </div>
            </div>
           
        </div>
    </div>

    <!-- Configurações Listagem -->
    <div class="nk-block">
        <div class="card card-bordered card-stretch">
            <div class="card-inner-group">
                <div class="card-inner p-0">
                    <div class="nk-tb-list nk-tb-ulist">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col">
                                <span class="sub-text">Nome</span>
                            </div>
                            <div class="nk-tb-col">
                                <span class="sub-text">Valor</span>
                            </div>
                            <div class="nk-tb-col">
                                <span class="sub-text">Descrição</span>
                            </div>
                        </div>

                    <form action="/admin/settings/update" method="POST">
                        <!-- Loop de Configurações -->
                        <?php foreach ($settings as $setting): ?>
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <span class="tb-lead"><?= $setting->name ?></span>
                            </div>
                            <div class="nk-tb-col tb-col-mb">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="values[<?= $setting->id ?>]" value="<?= $setting->value ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span><?= $setting->description ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>

            <!-- Botão de salvar todas as configurações -->
            <div class="card-inner">
                <div class="form-group">
                    <div class="form-control-wrap text-end">
                        <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                    </div>
                </div>
            </div>
                                    </form>

        </div>
    </div>
</div>


</content>
</content>