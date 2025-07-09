document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.mct-testimonial-form');

    if (form) {
        form.addEventListener('submit', function (e) {
            const name = form.querySelector('input[name="mct_name"]');
            const testimonial = form.querySelector('textarea[name="mct_testimonial"]');
            const image = form.querySelector('input[name="mct_image"]');
            let isValid = true;

            // Remove old errors
            form.querySelectorAll('.mct-error').forEach(el => el.remove());

            // Name validation
            if (!name.value.trim()) {
                isValid = false;
                name.insertAdjacentHTML('afterend', '<div class="mct-error">Name is required.</div>');
            }

            // Testimonial validation
            if (!testimonial.value.trim()) {
                isValid = false;
                testimonial.insertAdjacentHTML('afterend', '<div class="mct-error">Testimonial is required.</div>');
            }

            // Image validation
            if (image.files.length > 0) {
                const file = image.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    image.insertAdjacentHTML('afterend', '<div class="mct-error">Invalid image format. Allowed: JPG, PNG, GIF, WebP.</div>');
                }

                if (file.size > maxSize) {
                    isValid = false;
                    image.insertAdjacentHTML('afterend', '<div class="mct-error">Image is too large. Max size is 2MB.</div>');
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});
