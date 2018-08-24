import Vue from 'vue';
import VueSocketio from 'vue-socket.io';
import socketio from 'socket.io-client';
Vue.use(VueSocketio, socketio(':8890'));

import Vuex from 'vuex'

Vue.use(Vuex);

export const store = new Vuex.Store({
    //
});


import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);

require('./bootstrap');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';
import VueConfetti from 'vue-confetti';

Vue.use(VueAxios, axios);
Vue.use(VueConfetti);
import App from './App.vue';
import Vulma from 'vulma'
Vue.use(Vulma)

import Example from './components/Example.vue';
import Login from './components/Login.vue';
import Result from './components/Result.vue';
import Footer from './components/Footer.vue';
import Modal from './components/Modal.vue';
import AJH from './components/AJH.vue';
import chat from './components/chat.vue';
import playing from './components/playing';
const routes = [
  {  path: '/ctest',  component: chat },
  {  path: '/login', component: Login   },
  {  path: '/AJH',      component :AJH },
  {  path: '/playing',      component :playing },
  // {  path: '/race_result',      component :Result }
];

const router = new VueRouter({ mode: 'history', routes: routes});
new Vue(Vue.util.extend({ router }, App)).$mount('#app');
