import '../js/common/select2';

import $ from 'jquery';
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
   static targets = ['requiredRolesSelect'];
   static values = {
       locale: String,
   };

   connect() {
      $(this.requiredRolesSelectTarget).select2({
         language: this.localeValue,
         placeholder: "",
      });
   }
}