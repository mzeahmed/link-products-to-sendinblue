import '../../styles/admin/admin-app.scss';
import {attributeFields, productPanel} from './modules';

document.addEventListener('DOMContentLoaded', () => {
  attributeFields.init();
  productPanel.init();
});
