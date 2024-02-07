const wtContactForms = document.querySelectorAll('.wtContactForm');
const csrfToken = document.querySelector('meta[name="_token"]').content;

function removeErrorMessages(fields) {
    fields.forEach(f => {
        if(f.name != 'form_parameters') {
            f.classList.remove('wtFieldError');
            let errorEl = f.nextSibling;
            if(errorEl) errorEl.remove();
        }
    });
}

function resetForm() {

}

if(wtContactForms.length) {
    wtContactForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const fieldsToSubmit = form.querySelectorAll('[data-wt-rules]');
            removeErrorMessages(fieldsToSubmit);

            let xhr = new XMLHttpRequest();
            xhr.open('POST', '/submit-wt-contact-form');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    // if(response.errors.length) { // errors!
                    if(Object.keys(response.errors).length > 0) {
                        for(const [fname, fvalue] of Object.entries(response.errors)) {
                            let wtField = form.querySelector('[name="' + fname + '"]');
                            wtField.classList.add('wtFieldError');
                            let span = document.createElement('span');
                            let txt = document.createTextNode(fvalue);
                            span.appendChild(txt);
                            wtField.after(span);
                        }
                        console.log(response.errorText);
                    } else { //success!
                        form.reset();
                        console.log(response.successText);
                    }
                } else {
                    console.error('Error:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Error:', xhr.statusText);
            };
            let fieldData = [];
            if(fieldsToSubmit.length) {
                fieldsToSubmit.forEach(field => {
                    f = {
                        name: field.name,
                        value: field.value,
                        rules: field.dataset.wtRules
                    };

                    fieldData.push(f);
                });
            }

            let formData = {
                fields: fieldData
                // email: callForm.querySelector('input[name=email]').value,
                // name: callForm.querySelector('input[name=name]').value,
                // phone: callForm.querySelector('input[name=phone]').value,
                // company: callForm.querySelector('input[name=company]').value,
                // valkuil: callForm.querySelector('input[name=valkuil]').value,
                // valstrik: callForm.querySelector('input[name=valstrik]').value,
            };

            xhr.send(JSON.stringify(formData));

        });
    });
}