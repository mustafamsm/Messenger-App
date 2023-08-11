import Messenger from './components/messeges/Messenger.vue'
import ChatList from './components/messeges/ChatList.vue'
import {createApp} from 'vue';
const app=createApp({});
app.component('Messenger',Messenger)
.component('ChatList',ChatList)
.mount('#chat-app');
