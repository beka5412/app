function Testimonials({ page, goto, add, redirectEdit, removeItem, testimonials, setName, setText, setTestimonialID }) {
  return (
    <div style={{ display: page === 'index' ? 'block' : 'none' }} className="nk-block">

      <div className="flex justify-between">
        <h4 className="title nk-block-title">
          <?= __('Testimonials') ?>
        </h4>

        <div onClick={add} className="btn btn-outline-light bg-white d-none d-sm-inline-flex">
          <em className="icon ni ni-edit"></em>
          <span>
            Adicionar
          </span>
        </div>
      </div>

      <div className="nk-tb-list is-separate mb-3">

        <div className="nk-tb-item nk-tb-head">
          <div className="nk-tb-col nk-tb-col-check">
            <div className="custom-control custom-control-sm custom-checkbox notext">
              <input type="checkbox" className="custom-control-input" id="pid" />
              <label className="custom-control-label" htmlFor="pid"></label>
            </div>
          </div>

          <div className="nk-tb-col tb-col-sm">
            <span>
              <?= __('Name') ?>
            </span>
          </div>
          <div className="nk-tb-col nk-tb-col-tools">

          </div>
        </div>

        {testimonials.map(testimonial => {
          return (
            <div key={testimonial.id} className="nk-tb-item tr">
              <div className="nk-tb-col nk-tb-col-check">
                <div className="custom-control custom-control-sm custom-checkbox notext">
                  <input type="checkbox" className="custom-control-input" id="pid1" />
                  <label className="custom-control-label" htmlFor="pid1"></label>
                </div>
              </div>
              <div onClick={() => redirectEdit(testimonial)} className="nk-tb-col tb-col-sm">
                <a className="tb-product">
                  <span className="title">
                    {testimonial.name}
                  </span>
                </a>
              </div>
              <div onClick={() => redirectEdit(testimonial)} className="nk-tb-col tb-col-sm">
                <a className="tb-product">
                  <span className="title">
                    {String(testimonial.text || '').substring(0, 50) + (String(testimonial.text || '').length > 50 ? '...' : '')}
                  </span>
                </a>
              </div>

              <div className="nk-tb-col nk-tb-col-tools">
                <ul className="nk-tb-actions gx-1 my-n1">
                  <li className="me-n1">
                    <div className="dropdown">
                      <a href="#" className="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                        <em className="icon ni ni-more-h"></em>
                      </a>
                      <div className="dropdown-menu dropdown-menu-end">
                        <ul className="link-list-opt no-bdr">
                          <li>
                            <a onClick={() => redirectEdit(testimonial)}>
                              <em className="icon ni ni-edit"></em>
                              <span><?= __('Edit') ?></span>
                            </a>
                          </li>
                          <li>
                            <a onClick={() => removeItem(testimonial)}>
                              <em className="icon ni ni-trash"></em>
                              <span><?= __('Delete') ?></span>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>

            </div>
          )
        })}


      </div>
    </div>
  )
}