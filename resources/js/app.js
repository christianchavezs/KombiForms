import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

import { formBuilder } from './formbuilder/formbuilder';
window.formBuilder = formBuilder;

Alpine.start();
