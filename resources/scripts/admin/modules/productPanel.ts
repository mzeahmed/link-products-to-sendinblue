import {__} from '@wordpress/i18n';

type ListItem = { key: string; label: string };
type RoleItem = { [key: string]: string };

export const productPanel = {
  init: function () {
    const panelContainer = document.getElementById('sendinblue_data_panel') as HTMLElement;

    console.log('%c[productPanel] Init called', 'color: limegreen');

    const addButton = document.getElementById('add_lpts_list_row') as HTMLButtonElement;
    const rowsContainer = document.getElementById('lpts_list_rows') as HTMLElement;

    if (!addButton || !rowsContainer || !panelContainer) return;

    let lists: ListItem[] = [];
    let roles: RoleItem = {};

    try {
      lists = JSON.parse(panelContainer.dataset.lists || '{}');
      roles = JSON.parse(panelContainer.dataset.roles || '{}');
    } catch (e) {
      console.error('Invalid JSON in data-lists:', e);
      return;
    }

    this._addList(addButton, rowsContainer, lists, roles);
    this._removeList(rowsContainer);
    // this._onUserRoleSelect(roles);
  },

  _addList: function (addButton: HTMLButtonElement, rowsContainer: HTMLElement, lists: ListItem[], roles: RoleItem) {
    addButton.addEventListener('click', () => {
      const index = rowsContainer.querySelectorAll('tr').length;

      const row = document.createElement('tr');

      const listOptions = Object.entries(lists)
        .map(([key, label]) => `<option value="${key}">${label}</option>`)
        .join('');

      row.innerHTML = `
        <td class="list-cell">
          <select name="_selec_list[${index}][list_id]" class="wc-enhanced-select lpts-list">
            ${listOptions}
          </select>
        </td>

        <td class="condition-cell">
          <select name="_selec_list[${index}][condition]" class="lpts-condition">
            <option value="always">${__('Always', 'link-products-to-sendinblue')}</option>
            <option value="order_total_gt">${__('Order Total >=', 'link-products-to-sendinblue')}</option>
            <option value="order_total_eq">${__('Order Total =', 'link-products-to-sendinblue')}</option>
            <!-- <option value="user_role">${__('User Role', 'link-products-to-sendinblue')}</option> -->
          </select>
        </td>

        <td class="param-cell">
          <input type="text" name="_selec_list[${index}][param]" />
        </td>

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

        if (row) {
          row.remove();
        }
      }
    });
  },

  _onUserRoleSelect: function (roles: RoleItem) {
    console.log('%c[_onUserRoleSelect] Registered', 'color: orange');

    const roleOptions = Object.entries(roles)
      .map(([key, label]) => `<option value="${key}">${label}</option>`)
      .join('');

    const conditionSelect = document.querySelectorAll('.lpts-condition') as NodeListOf<HTMLSelectElement>;

    console.log('conditionSelect:', conditionSelect);

    conditionSelect.forEach((select, index) => {
      const tr = select.parentElement?.parentElement as HTMLTableRowElement;
      const paramCell = tr.querySelector('.param-cell') as HTMLElement;

      select.addEventListener('change', (e) => {
        const value = select.value as string;

        console.log('value:', value, e);

        if (value === 'user_role') {
          const roleSelect = document.createElement('select');

          roleSelect.name = `_selec_list[${index}][param]`;

          Object.entries(roles).forEach(([key, label]) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = label;
            roleSelect.appendChild(option);
          });

          paramCell.innerHTML = '';
          paramCell.appendChild(roleSelect);
        } else {
          paramCell.innerHTML = `<input type="text" name="_selec_list[${index}][param]" />`;
        }
      });
    });
  }
};
