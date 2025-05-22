function EditTestimonial({ page, goto, data, changeName, changeText, changeImage, name, text, image, loadingPhoto, setLoadingPhoto, photo, setPhoto }) {
  if (!data) return;
  if (!name) name = '';
  if (!text) text = '';

  return (
    <div style={{ display: page === 'edit' ? 'block' : 'none' }}>

      <div className="flex justify-between mb-4">
        <div>
          <h4 className="title nk-block-title">
            <?= __('Edit testimonial') ?>
          </h4>
        </div>

        <div>
          <div onClick={() => goto('index')} className="btn btn-outline-light bg-white">
            <em className="icon ni ni-arrow-left"></em>
            <span>
              Voltar
            </span>
          </div>
        </div>
      </div>

      <div className="row gy-4">
        <div className="col-sm-12 mt-2">
          <div className="row">
            <div className="col-sm-6">
              <img onClick={() => $(inputFileTestimonial).click()} src={'<?= site_url() ?>' + (photo || '/images/default.png')} />
            </div>
            <div className="col-sm-6">
              <div className="form-group">
                <div className="flex">
                  <label className="form-label">Foto</label>
                  <div style={{ display: loadingPhoto ? 'block' : 'none' }}>
                    <React.Loading />
                  </div>
                </div>

                <div className="form-control-wrap">
                  <input type="file" id="inputFileTestimonial" className="form-control" required="" placeholder="" onChange={changeImage} />
                </div>
              </div>

              <div className="form-group">
                <label className="form-label">Nome</label>
                <div className="form-control-wrap">
                  <input type="text" className="form-control" required="" placeholder="" value={name} onChange={changeName} />
                </div>
              </div>

              <div className="form-group">
                <label className="form-label">Texto</label>
                <div className="form-control-wrap">
                  <textarea className="form-control" onChange={changeText} value={text}></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}