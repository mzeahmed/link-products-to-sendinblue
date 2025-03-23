import {__} from '@wordpress/i18n';

type ListItem = { key: string; label: string };

export const productPanel = {
  init: function () {
    const panelContainer = document.getElementById('sendinblue_data_panel') as HTMLElement;

    const addButton = document.getElementById('add_lpts_list_row') as HTMLButtonElement;
    const rowsContainer = document.getElementById('lpts_list_rows') as HTMLElement;

    if (!addButton || !rowsContainer || !panelContainer) return;

    let lists: ListItem[] = [];

    try {
      const data = panelContainer.dataset.lists as string;
      lists = JSON.parse(data);
    } catch (e) {
      console.error('Invalid JSON in data-lists:', e);
      return;
    }

    this._addList(addButton, rowsContainer, lists);

    this._removeList(rowsContainer);
  },

  _addList: function (addButton: HTMLButtonElement, rowsContainer: HTMLElement, lists: ListItem[]) {
    addButton.addEventListener('click', () => {
      const row = document.createElement('tr');

      console.log('addList', lists);

      const listOptions = Object.entries(lists)
        .map(([key, label]) => `<option value="${key}">${label}</option>`)
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
