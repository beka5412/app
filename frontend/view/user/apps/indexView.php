<title>Apps</title>
<style>
  .card {
    box-shadow: 0 3px 3px 0 rgb(0 0 0 / 5%), 0 5px 15px 0 rgb(0 0 0 / 5%);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .dark-mode .card {
    background: #141c2600 !important;
  }
</style>
<content>
  <div class="nk-content-body">
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
              <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                  <?= __('Integrations') ?>
                </h3>
                <nav>
                  <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                      <a href="">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                      <?= __('Integrations') ?>
                    </li>
                  </ul>
                </nav>
              </div>
              <div class="nk-bloxk-head-content"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="col-12"></div>
          </div>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="row g-gs">

        <div class="col-md-4">
          <div class="card-bordered card">
            <div class="card-inner">
              <div class="row g-gs">
                <div class="col-sm-3 ml-2 px-0">
                  <img src="<?= site_url() . '/images/apps/utmify.png' ?>" id="blah4" width="100px"
                    class="img_integration">
                </div>
                <div class="col-sm-9 d-flex align-items-center justify-content-end">
                  <div class="custom-control custom-switch checked">
                    <input type="checkbox" class="custom-control-input" id="switchEnableAppUTMify"
                      change="onChangeAppUTMify" <?php if ($utmify->status ?? false) : ?> checked=""
                    <?php endif; ?>>
                    <label class="custom-control-label" for="switchEnableAppUTMify">
                      <?= __('Activate') ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-inner card-text  border-bottom ">
              <p>
                <?= __('Monitor UTMs and financials on the UTMify dashboard.') ?>
              </p>
            </div>
            <div class="card-inner mt-2">
              <a href="<?= site_url() . '/app/utmify' ?>" class="btn btn-primary">
                <?= __('See Integrations') ?>
              </a>
            </div>
          </div>

        </div>

        <div class="col-md-4">
          <div class="card-bordered card">
            <div class="card-inner">
                  <img src="<?= site_url() . '/images/apps/memberkit.png' ?>" id="blah4" height="30px"
                    class="img_integration">
            </div>
            <div class="card-inner card-text border-bottom pt-1">
              <p>
                <?= __('Place your customers in a professional members area.') ?>
              </p>
            </div>
            <div class="card-inner mt-2">
              <a href="<?= site_url() . '/app/memberkit' ?>" class="btn btn-primary">
                <?= __('See Integrations') ?>
              </a>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card-bordered card">
            <div class="card-inner">
                <div class="col-sm-3 ml-2 px-0">
                  <img src="<?= site_url() . '/images/apps/astronmembers.png' ?>" id="blah4" height="30px"
                    class="img_integration">
                </div>
            </div>
            <div class="card-inner card-text border-bottom pt-1">
              <p>
                <?= __('Place your customers in a professional members area.') ?>
              </p>
            </div>
            <div class="card-inner mt-2">
              <a href="<?= site_url() . '/app/astronmembers' ?>" class="btn btn-primary">
                <?= __('See Integrations') ?>
              </a>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card-bordered card">
            <div class="card-inner">
                <div class="col-sm-3 ml-2 px-0">
                  <img src="<?= site_url() . '/images/apps/sellflux.webp' ?>" id="blah4" height="30px"
                    class="img_integration">
                </div>
            </div>
            <div class="card-inner card-text border-bottom pt-1">
              <p>
                <?= __('Track the flow of your customer\'s actions in the sales process.') ?>
              </p>
            </div>
            <div class="card-inner mt-2">
              <a href="<?= site_url() . '/app/sellflux' ?>" class="btn btn-primary">
                <?= __('See Integrations') ?>
              </a>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card-bordered card">
            <div class="card-inner">
                <div class="col-sm-3 ml-2 px-0">
                  <img src="<?= site_url() . '/images/apps/cademi.png' ?>" id="blah4" height="30px"
                    class="img_integration">
                </div>
            </div>
            <div class="card-inner card-text border-bottom pt-1">
              <p>
                <?= __('Register students in your cademi members area.') ?>
              </p>
            </div>
            <div class="card-inner mt-2">
              <a href="<?= site_url() . '/app/cademi' ?>" class="btn btn-primary">
                <?= __('See Integrations') ?>
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</content>