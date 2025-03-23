export const attributeFields = {
  init: function () {
    const parent = document.querySelector('#lpts_user_attributes_fields') as HTMLElement;

    const button_add = document.getElementById('userAttributesAdd') as HTMLButtonElement;
    const button_del = document.getElementById('userAttributesDel') as HTMLButtonElement;

    if (parent && parent.childElementCount < 2) {
      button_del.disabled = true;
    }

    if (button_add) button_add.addEventListener('click', () => {
      this._addFields(parent, button_del);
    });

    if (button_del) button_del.addEventListener('click', () => {
      this._removeFields(parent, button_del);
    });
  },

  _addFields: function (parent: HTMLElement, button_del: HTMLButtonElement) {
    const item = document.querySelector('.attributes_match_row') as HTMLElement;
    if (!item) return;

    const clone = item.cloneNode(true) as HTMLElement;
    parent.appendChild(clone);

    button_del.disabled = false;
  },

  _removeFields: function (parent: HTMLElement, button_del: HTMLButtonElement) {
    if (parent.childElementCount < 2) return;

    const lastChild = parent.lastElementChild;

    if (lastChild) {
      parent.removeChild(lastChild);
    }

    if (parent.childElementCount < 2) {
      button_del.disabled = true;
    }
  }
};
