import {__} from '@wordpress/i18n';

declare global {
  interface Window {
    lptsLists: { key: string; label: string }[];
  }
}

export const productPanel = {
  init: function () {

    const postBody = document.getElementById('post-body') as HTMLElement;

    console.log(postBody);

    const addButton = document.getElementById('add_lpts_list_row') as HTMLButtonElement;
    const rowsContainer = document.getElementById('lpts_list_rows') as HTMLElement;

    if (!addButton || !rowsContainer || !window.lptsLists) return;

    this._addList(addButton, rowsContainer);

    this._removeList(rowsContainer);
  },

  _addList: function (addButton: HTMLButtonElement, rowsContainer: HTMLElement) {
    addButton.addEventListener('click', () => {
      const row = document.createElement('tr');

      const listOptions = window.lptsLists
        .map(({key, label}) => `<option value="${key}">${label}</option>`)
        .join('');

      row.innerHTML = `
        <td>
          <select name="_selec_list[][list_id]" class="wc-enhanced-select">
            ${listOptions}
          </select>
        </td>
        
        <td>
          <select name="_selec_list[][condition]">
            <option value="always">${__('Always', 'link-products-to-sendinblue')}</option>
            <option value="order_total_gt">${__('Order Total >', 'link-products-to-sendinblue')}</option>
            <option value="order_total_eq">${__('Order Total =', 'link-products-to-sendinblue')}</option>
            <option value="user_role">${__('User Role', 'link-products-to-sendinblue')}</option>
          </select>
        </td>
        
        <td><input type="text" name="_selec_list[][param]" /></td>
        <td><button type="button" class="button remove-row">${__('Remove', 'link-products-to-sendinblue')}</button></td>
      `;

      rowsContainer.appendChild(row);
    });
  },

  _removeList: function (rowsContainer: HTMLElement) {
    rowsContainer.addEventListener('click', (e: Event) => {
      const target = e.target as HTMLElement;

      if (target.classList.contains('remove-row')) {
        const row = target.closest('tr');

        if (row) row.remove();
      }
    });
  }
};
