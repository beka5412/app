<div class="card-inner">
  <div class="nk-block-between-md g-3">
    <div class="g">
      <ul class="pagination justify-content-center justify-content-md-start">
        <?php if (!empty($info)): ?>
          <?php $item_json = json_decode(json_encode($info)); ?>
          <?php $links = $item_json->links ?? []; ?>
          <?php if (!empty($links)): ?>
            <?php $pagenum = 0; ?>
            <?php foreach ($links as $link): ?>
              <?php if ($pagenum == 0): ?>
              <li class="page-item">
                <a class="page-link"
                  href="<?php echo $url; ?><?php echo ltrim($link->url ?? '', '/'); ?>"
                  to="<?php echo $url; ?><?php echo ltrim($link->url ?? '', '/'); ?>">
                  <em class="icon ni ni-chevrons-left"></em>
                </a>
              </li>
              <?php endif; ?>
              <?php $pagination_length = sizeof($links)-1; ?>
              
              <?php if ($pagenum > 0 && $pagenum < $pagination_length): ?>
                <li class="page-item <?php echo $link->active == 1 ? 'active' : ''; ?>">
                  <a class="page-link"
                    href="<?php echo $url; ?><?php echo ltrim($link->url ?? '', '/'); ?>"
                    to="<?php echo $url; ?><?php echo ltrim($link->url ?? '', '/'); ?>"
                    ><?php echo $link->label; ?></a>
                </li>
              <?php endif; ?>

              <?php if ($pagenum == sizeof($links)-1): ?>
              <li class="page-item">
                <a class="page-link"
                  href="<?php echo $url; ?><?php echo ltrim($link->url ?? '', '/'); ?>"
                  to="<?php echo $url; ?><?php echo ltrim($link->url ?? '', '/'); ?>">
                  <em class="icon ni ni-chevrons-right"></em>
                </a>
              </li>
              <?php endif; ?>
              <?php $pagenum++; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>