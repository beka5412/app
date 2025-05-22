function Index({ testimonials: rows, productID, checkoutID }) {
  const { useState } = React

  const [testimonials, setTestimonials] = useState(rows)
  const [page, setPage] = useState('index')
  const [data, setData] = useState(null)
  const [testimonialID, setTestimonialID] = useState('')
  const [name, setName] = useState()
  const [text, setText] = useState()
  const [loadingPhoto, setLoadingPhoto] = useState(false)
  const [photo, setPhoto] = useState(false)

  function changeName(event) {
    setName(event.target.value)
  }

  function changeText(event) {
    setText(event.target.value)
  }

  function goto(pageName, data) {
    setPage(pageName)
    setData(data)
  }

  function updateTestimonial(item) {
    setName(item.name)
    setText(item.text)
    setTestimonialID(item.id)
    setPhoto(item.photo)
    setTestimonials(
      testimonials.map(row => {
        if (row.id === item.id)
          return item;
        return row;
      })
    )
  }

  function removeTestimonialItem(item) {
    setName('')
    setText('')
    setTestimonialID('')
    setPhoto('')
    setTestimonials(
      testimonials.filter(row => row.id !== item.id)
    )
  }

  function redirectEdit(item) {
    updateTestimonial(item)
    goto('edit', item);
  }

  function redirectIndex(item) {
    updateTestimonial(item)
    goto('index', item);
  }

  function changeImage(event) {
    const file = event.target.files[0];

    let formData = new FormData();
    formData.append('image', file || '');

    let options = {
      headers: { 'Client-Name': 'Action' },
      method: 'POST',
      body: formData
    };

    setLoadingPhoto(true)

    fetch(`/ajax/actions/user/product/${productID}/checkout/${checkoutID}/testimonial/${testimonialID}/uploadImage`, options).then(response => response.json()).then(body => {
      setLoadingPhoto(false)
      if (body.status == 'success') {
        setPhoto(body.data.image)
      }
      else toastError(body.message);
    });
  }

  function add() {
    let options = {
      headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
      method: 'POST'
    };

    fetch(`/ajax/actions/user/product/${productID}/checkout/${checkoutID}/testimonial/new`, options).then(response => response.json()).then(body => {
      if (body.status === 'success') {
        redirectEdit(body.data)
        setTestimonials([...testimonials, body.data])
      }
      else toastError(body.message);
    });
  }

  function removeItem(item) {
    let options = {
      headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
      method: 'DELETE'
    };

    fetch(`/ajax/actions/user/product/${productID}/checkout/${checkoutID}/testimonial/${item.id}/destroy`, options).then(response => response.json()).then(body => {
      if (body.status === 'success') {
        removeTestimonialItem(item)
      }
      else toastError(body.message);
    });
  }

  function submit() {
    if (!testimonialID) return;

    let data = {
      name,
      text,
      photo
    };

    let options = {
      headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
      method: 'PATCH',
      body: JSON.stringify(data)
    };

    fetch(`/ajax/actions/user/product/${productID}/checkout/${checkoutID}/testimonial/${testimonialID}/edit`, options).then(response => response.json()).then(body => {
      if (body.status === 'success') {
        redirectIndex(body.data)
      }
      else toastError(body.message);
    });
  }

  return (
    <div>
      <div className="card card-bordered card-preview">
        <div className="card-inner">
          <div className="preview-block">
            <div className="nk-block-head">
              <div className="nk-block-head-content">

                <Testimonials testimonials={testimonials} page={page} goto={goto} data={data} setName={setName} setText={setText} name={name} text={text} testimonialID={testimonialID} setTestimonialID={setTestimonialID} removeItem={removeItem} add={add} redirectEdit={redirectEdit} />

                <EditTestimonial testimonials={testimonials} page={page} goto={goto} data={data} changeName={changeName} name={name} changeText={changeText} text={text} changeImage={changeImage} testimonialID={testimonialID} setTestimonialID={setTestimonialID} removeItem={removeItem} loadingPhoto={loadingPhoto} setLoadingPhoto={setLoadingPhoto} photo={photo} setPhoto={setPhoto} redirectEdit={redirectEdit} />

              </div>
            </div>
          </div>
        </div>
      </div>

      <button onClick={submit} className="mt-4 w-100 btn-afilie btn-demote mt-4"><?= __('Save') ?></button>
    </div>
  );
}