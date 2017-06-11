import Vue from 'vue/dist/vue.js'
import VeeValidate from 'vee-validate/dist/vee-validate.js';

Vue.use(VeeValidate);
new Vue({

	el: '.general', 

	components: {
		DeHForm
	},

	data: {
		title: 'qualquer coisa'
	},
})