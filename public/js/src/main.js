async function onClickItemOperation(event, path) {
    let form = event.target.closest('.request-form');
    let inputs = form.querySelectorAll('[name]');
    let values = {};
    [].map.call(inputs, (input) => {
        values[input.getAttribute('name')] = input.value
    });
    //console.log(JSON.stringify(values));
    let headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }

    let response = await fetch(path, {
        method: 'POST',
        headers,
        body: JSON.stringify(values)
    });

    let result = await response.json();
    //console.log(result);
    clearErrorMassage(form);
    if (result.status === 'false') errorMassage(form, result.errors);
    else successMessage();
}

async function onClickItemRemove(event) {
    let form = event.target.closest('.request-form');
    let id = form.getAttribute('data-row');
    let response = await fetch(('/admin/catalog/rm/' + id));
    let result = await response.json();
    if (result.status === 'true') successMessage();
}

async function onClickItemCreate(event) {
    await onClickItemOperation(event, '/admin/catalog/create');
}

//////////////////// ALERT
function errorMassage(form, errors) {
    for (let key in errors) {
        let input = form.querySelector('[name='+ key +']');
        let message = input.closest('.item-group').querySelector('.error-message');
        input.classList.add('error');
        message.innerHTML = errors[key];
    }
}

function clearErrorMassage(form) {
    form.querySelectorAll('[name].error').forEach( input => input.classList.remove('error'));
    form.querySelectorAll('.error-message').forEach( mess => mess.innerHTML = '');
}

function successMessage(event) {
   alert('Успех');
   window.location.reload(false);
}
export {onClickItemCreate, onClickItemOperation, onClickItemRemove }