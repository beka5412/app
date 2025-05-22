App.User.Profile.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Edit address';
    className = 'App.User.Profile.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/profile/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    profileUploadPhotoOnClick() {
        document.getElementById('profilePhoto')?.click();
    }

    profileUploadPhotoOnChange() {
        const formData = new FormData();
        formData.append('image', document.getElementById('profilePhoto')?.files[0] || '');

        const options = {
            headers: { 'Client-Name': 'Action' },
            method: 'POST',
            body: formData
        };

        fetch(`/ajax/actions/user/profile/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                const img = document.getElementById('profileImgPhoto');
                const icon = document.getElementById('profileIconPhoto');
                if (img && body?.data?.image) {
                    img.style.display = 'block';
                    img.src = body.data.image;
                }
                if (icon) {
                    icon.style.display = 'none';
                }
            }
            else toastError(body.message);
        });
    }

    profileOnSubmit() {
        let inputs = document.querySelectorAll("input:not([type='file'])");
        let formData = new FormData();

        inputs.forEach(input => {
            formData.append(input.name, input.value);
        });

        const options = {
            method: 'POST',
            body: formData,
            headers: {
                'Content-Type': `multipart/form-data;`,
            },
        };

        fetch(`/ajax/actions/user/profile/edit`, options)
            toast('Perfil atualizado com sucesso')
    }
};
