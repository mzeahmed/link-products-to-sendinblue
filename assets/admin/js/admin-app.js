function cloneMatchAttributes() {
    // var section = document.getElementById('wcprotosl-match-attr');
    // var match_fields = document.createElement(section);
    //
    // document.body.appendChild(match_fields);

    let match_fields = document.getElementById('wcprotosl-match-attr');
    let match_fields_copy = match_fields.cloneNode(true);
    match_fields_copy.id = 'wcprotosl-match-attr_2';

    match_fields.after(match_fields_copy);
}

function init() {
    let click = document.getElementById('wcprotosl-match-list-plus');

    click.onClick = cloneMatchAttributes();
}

init();
