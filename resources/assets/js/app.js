/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import VueDraggableResizable from 'vue-draggable-resizable'

Vue.component('chat-widget', require('./components/ChatWidget.vue'));
Vue.component('chat-conversation', require('./components/ChatConversation.vue'));
Vue.component('chat-message', require('./components/ChatMessage.vue'));
Vue.component('vue-draggable-resizable', VueDraggableResizable);

window.vueChat = new Vue({
    el: '#chat'
/*
    data: {
    	messages: []
    },

    created() {
    	this.fetchMessages();
    },

    methods: {
    	fetchMessages() {
    		axios.get('/messages').then(response => {
    			this.messages = response.data;
    		});
    	},

    	addMessage(message) {
    		this.messages.push(message);

    		axios.post('/messages',message).then(response => {
    			console.log(response.data);
    		});
    	}
    }*/
});
