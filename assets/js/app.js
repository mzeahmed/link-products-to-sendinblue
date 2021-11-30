const button_add = document.getElementById('userAttributesAdd');
const button_del = document.getElementById('userAttributesDel');

const parent = document.querySelector('#lpts_user_attributes_fields');

if (parent.childElementCount < 2) {
    button_del.disabled = true;
}

/**
 * Clone fields
 */
function addFields() {

    let item = document.querySelector('.attributes_match_row');
    let clone = item.cloneNode(true);

    document.getElementById('lpts_user_attributes_fields').appendChild(clone);

    button_del.disabled = false;
    parent.childElementCount++;
}

/**
 * Remove last section of user attributes fields
 */
function removeFields() {
    let tbody = document.querySelector('#lpts_user_attributes_fields');

    tbody.removeChild(tbody.lastElementChild);
    parent.childElementCount--;

    if (parent.childElementCount < 2) {
        button_del.disabled = true;
    }
}
