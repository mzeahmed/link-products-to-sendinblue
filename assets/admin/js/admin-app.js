/**
 * Clone user attributes synch form fields
 */
// function user_attributes_synch_fields_clone() {
//     let item = document.querySelector('tbody');
//
//     let clone = item.cloneNode(true);
//     clone.id = 'user_attrs_field' + 1;
//
//     document.querySelector('table').appendChild(clone);
//
//     let b = document.getElementById('user_attr_less');
//     if (b.style.display === 'none') {
//         b.style.display = 'block';
//     }
// }

var counter = 0;

/**
 * Add fields
 */
function moreUserAttrSynchFields() {
    counter++;

    let newFields = document.querySelector('table').cloneNode(true);
    newFields.classList.add('table_plus');

    let newField = newFields.childNodes;

    for (let i = 0; i < newField.length; i++) {
        let theName = newField[i].name;
        if (theName) {
            newField[i] = theName + counter;
        }
    }

    let b = document.getElementById('user_attr_less');
    if (b.style.display === 'none') {
        b.style.display = 'block';
    }

    let insertHere = document.getElementById('write_root');
    insertHere.parentNode.insertBefore(newFields, insertHere);
}

/**
 * Remove added fields
 */
function lessUserAttrSynchFields() {
    let item = document.querySelector('.form-table.table_plus');
    item.parentNode.removeChild(item);

    // if (typeof (item) == 'undefined') {
    //     let b = document.getElementById('user_attr_less');
    //     b.style.display = 'none';
    // }
}
