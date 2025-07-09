document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.mct-testimonial-form');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const messageBox = form.querySelector('.mct-message');

        if (!messageBox) return;

        messageBox.style.display = 'none';
        messageBox.innerHTML = '';

        // Basic field validation
        const name = form.querySelector('input[name="mct_name"]');
        const testimonial = form.querySelector('textarea[name="mct_testimonial"]');
        const image = form.querySelector('input[name="mct_image"]');
        let hasError = false;

        form.querySelectorAll('.mct-error').forEach(el => el.remove());

        if (!name.value.trim()) {
            name.insertAdjacentHTML('afterend', '<div class="mct-error">Name is required.</div>');
            hasError = true;
        }

        if (!testimonial.value.trim()) {
            testimonial.insertAdjacentHTML('afterend', '<div class="mct-error">Testimonial is required.</div>');
            hasError = true;
        }

        if (image.files.length === 0) {
            image.insertAdjacentHTML('afterend', '<div class="mct-error">Image is required.</div>');
            hasError = true;
        }

        if (hasError) return;

        // ✅ AJAX Submission
        fetch(mct_ajax_object.ajax_url, {
            method: 'POST',
            body: formData,
        })
            .then(res => res.text())
            .then(response => {
                messageBox.innerHTML = response;
                messageBox.style.display = 'block';

                if (response.includes('mct-success')) {
                    form.reset();
                    setTimeout(() => {
                        messageBox.style.display = 'none';
                        messageBox.innerHTML = '';
                    }, 5000);
                }
            })
            .catch(() => {
                messageBox.innerHTML = '<p class="mct-error">❌ Submission failed. Please try again later.</p>';
                messageBox.style.display = 'block';
            });
    });
});
