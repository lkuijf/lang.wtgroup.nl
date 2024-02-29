const wtContactForms = document.querySelectorAll('.wtContactForm');
const csrfToken = document.querySelector('meta[name="_token"]').content;


function displayAlertBox(type = 'danger', message = 'the message') {
    let divAlertWrap = document.createElement('div');
    let divIcon = document.createElement('div');
    let divText = document.createElement('div');
    let divClose = document.createElement('div');
    let pIcon = document.createElement('p');
    let pText = document.createElement('p');
    let pClose = document.createElement('p');
    let tText = document.createTextNode(message);

    pText.append(tText);
    divIcon.append(pIcon);
    divText.append(pText);
    divClose.append(pClose);
    divAlertWrap.append(divIcon, divText, divClose);

    divAlertWrap.classList.add('alert');
    divAlertWrap.classList.add('alert-' + type);
    pIcon.classList.add('icon');
    pIcon.classList.add(type + 'Icon');
    pClose.classList.add('alertClose');

    document.body.append(divAlertWrap);
    divAlertWrap.classList.add('fadeInOut')

    divAlertWrap.addEventListener('animationend', () => {
        divAlertWrap.remove();
    });
    pClose.addEventListener('click', () => {
        divAlertWrap.remove();
    });

}

function removeErrorMessages(fields) {
    fields.forEach(f => {
        if(f.name != 'form_parameters' && f.name != 'valkuil' && f.name != 'valstrik') {
            f.classList.remove('wtFieldError');
            let errorEl = f.nextSibling;
            if(errorEl) errorEl.remove();
        }
    });
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
console.log(response.errors);
                    if(Object.keys(response.errors).length > 0) {
                        for(const [fname, fvalue] of Object.entries(response.errors)) {
                            if(fname != 'honeypot') {
                                let wtField = form.querySelector('[name="' + fname + '"]');
                                wtField.classList.add('wtFieldError');
                                let span = document.createElement('span');
                                let txt = document.createTextNode(fvalue);
                                span.appendChild(txt);
                                wtField.after(span);
                            }
                        }
                        // console.log(response.errorText);
                        displayAlertBox('danger', response.errorText);
                    } else { //success!
                        form.reset();
                        // console.log(response.successText);
                        displayAlertBox('success', response.successText);
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