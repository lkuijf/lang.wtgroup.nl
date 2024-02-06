const wtContactForms = document.querySelectorAll('.wtContactForm');
const csrfToken = document.querySelector('meta[name="_token"]').content;


if(wtContactForms.length) {
    wtContactForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
// console.log('denied submit');

            const fieldsToSubmit = form.querySelectorAll('[data-wt-rules]');
            fieldsToSubmit.forEach(field => {
                field.classList.remove('wtFieldError');
                let errorEl = field.nextSibling;
                if(errorEl) errorEl.remove();
            });

            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'http://wsl2-lang.wtgroup.nl/submit-wt-contact-form');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    // if(response.errors.length) { // errors!
                    if(Object.keys(response.errors).length > 0) {
                        console.log('errors!');
                        // console.log(response);
                        for(const [fname, fvalue] of Object.entries(response.errors)) {
                            let wtField = form.querySelector('[name="' + fname + '"]');
                            wtField.classList.add('wtFieldError');
                            let span = document.createElement('span');
                            let txt = document.createTextNode(fvalue);
                            span.appendChild(txt);
                            wtField.after(span);
                        }
                     
                    } else { //no errors!
                        console.log('no errors');
                        console.log(response);
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
// console.log(formData);
            xhr.send(JSON.stringify(formData));



        });
    });
}