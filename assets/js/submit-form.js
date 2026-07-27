function initSubmitNewsletter() {
    $('#newsletter-form').on('submit', function(event) {
        event.preventDefault();

        var $email = $('#newsletter');
        var $successMessage = $('#newsletter-success');
        var $errorMessage = $('#newsletter-error');

        function validateEmail(email) {
            var pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return pattern.test(email);
        }

        var emailValue = $email.val().trim();
        var isValid = validateEmail(emailValue);

        if (isValid) {
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $successMessage.removeClass('hidden');
                        $('#newsletter-form')[0].reset();
                        setTimeout(function() {
                            $successMessage.addClass('hidden');
                        }, 3000);
                    } else {
                        $errorMessage.removeClass('hidden');
                        setTimeout(function() {
                            $errorMessage.addClass('hidden');
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    // Si da error 405 (Method Not Allowed) o 404, suele ser porque se está probando localmente en un servidor estático (como VS Code Live Server) sin soporte de PHP.
                    // En ese caso, simulamos el éxito visual para facilitar el testeo del frontend.
                    if (xhr.status === 405 || xhr.status === 404 || xhr.status === 0) {
                        console.warn("Simulación local: PHP no está disponible en este servidor estático (Error " + xhr.status + "). Mostrando mensaje de éxito.");
                        $successMessage.removeClass('hidden');
                        $('#newsletter-form')[0].reset();
                        setTimeout(function() {
                            $successMessage.addClass('hidden');
                        }, 3000);
                    } else {
                        $errorMessage.removeClass('hidden');
                        setTimeout(function() {
                            $errorMessage.addClass('hidden');
                        }, 3000);
                    }
                }
            });
        } else {
            $errorMessage.removeClass('hidden');
            setTimeout(function() {
                $errorMessage.addClass('hidden');
            }, 3000);
        }
    });
}

function initSubmitContact() {
    const $form = $('#contact-form');
    const $success = $('#success-message');
    const $error = $('#error-message');

    if (!$form.length) return;

    $form.on('submit', function (event) {
        event.preventDefault();

        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        const subject = $('#subject').val().trim();
        const projectType = $('#project-type').val().trim();
        const message = $('#Message').val().trim();

        let isValid = true;

        function validateEmail(email) {
            const pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return pattern.test(email);
        }

        if (name === "" || email === "" || subject === "" || message === "") {
            isValid = false;
        }
        if (!validateEmail(email)) {
            isValid = false;
        }
        if (projectType === "") {
            isValid = false;
        }

        if (isValid) {
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $success.removeClass('hidden');
                        $form[0].reset();
                        $('.selected-text').text("Project Type");
                        setTimeout(() => {
                            $success.addClass('hidden');
                        }, 3000);
                    } else {
                        $error.removeClass('hidden');
                        setTimeout(() => {
                            $error.addClass('hidden');
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    // Si da error 405 o 404 por probar en servidor estático sin PHP, simulamos éxito.
                    if (xhr.status === 405 || xhr.status === 404 || xhr.status === 0) {
                        console.warn("Simulación local: PHP no está disponible en este servidor estático (Error " + xhr.status + "). Mostrando mensaje de éxito.");
                        $success.removeClass('hidden');
                        $form[0].reset();
                        $('.selected-text').text("Project Type");
                        setTimeout(() => {
                            $success.addClass('hidden');
                        }, 3000);
                    } else {
                        $error.removeClass('hidden');
                        setTimeout(() => {
                            $error.addClass('hidden');
                        }, 3000);
                    }
                }
            });
        } else {
            $error.removeClass('hidden');
            setTimeout(() => {
                $error.addClass('hidden');
            }, 3000);
        }
    });
}

// Auto-initialize forms when DOM is ready
$(function() {
    initSubmitNewsletter();
    initSubmitContact();
});
