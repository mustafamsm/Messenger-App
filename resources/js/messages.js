import Messenger from "./components/messeges/Messenger.vue";
import ChatList from "./components/messeges/ChatList.vue";
import { createApp } from "vue";

import Echo from "laravel-echo";

window.Pusher = require("pusher-js");
Pusher.logToConsole = true;
const app = createApp({
    data() {
        return {
            conversations:[], //all conversations
            messages: [], //sheared messages
            conversation: null, //current conversation
            userId: userId, //current user id
            csrf_token: csrf_token, //csrf token
            laravelEcho: null, //laravel echo instance
            users: [], //friends list
            chatChanel:null,
            alertAudio: new Audio('/assets/notification-tone-swift-gesture.mp3'),

        };
    },
    mounted() {
        this.alertAudio.addEventListener('ended', () => {
            this.alertAudio.currentTime = 0;
        })
        this.laravelEcho = new Echo({
            broadcaster: "pusher",
            key: process.env.MIX_PUSHER_APP_KEY,
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            forceTLS: true,
        });
        this.laravelEcho
            .join(`Messenger.${this.userId}`)
            .listen(".new-message", (data) => {
                let exists = false;
                for (let i in this.conversations) {
                    let conversation = this.conversations[i];
                    if (conversation.id == data.message.conversation_id) {
                        if (!conversation.hasOwnProperty('new_messages')) {
                            conversation.new_messages = 0;
                        }
                        conversation.new_messages++;
                        conversation.last_message = data.message;
                        exists = true;
                        this.conversations.splice(i, 1);
                        this.conversations.unshift(conversation);

                        if (this.conversation && this.conversation.id == conversation.id) {
                            this.messages.push(data.message);
                            let container = document.querySelector('#chat-body');
                            container.scrollTop = container.scrollHeight;
                        }
                        break;
                    }
                }
                if (!exists) {
                    fetch(`/api/conversations/${data.message.conversation_id}`)
                        .then(response => response.json())
                        .then(json => {
                            this.conversations.unshift(json)
                        })
                }

                this.alertAudio.play();

            });
       this.chatChanel= this.laravelEcho
            .join('Chat')
            .joining((user) => {
              for(let i in this.conversations){
                  let conversation=this.conversations[i];
                  if(conversation.participants[0].id==user.id){
                      console.log('user is online')
                      this.conversations[i].participants[0].isOnline=true;
                      return;
                  }
              }
            })
            .leaving((user) => {
                for(let i in this.conversations){
                    let conversation=this.conversations[i];
                    if(conversation.participants[0].id==user.id){
                        console.log('user is offline')
                        this.conversations[i].participants[0].isOnline=false;
                        return;
                    }
                }
            })
           .listenForWhisper('typing', (e) => {
               let user=this.findUser(e.id,e.conversation_id)
               if(user){
                   user.isTyping=true;
               }
               console.log(e.id);
           })
           .listenForWhisper('stopped-typing', (e) => {
               let user=this.findUser(e.id,e.conversation_id);
               if(user){
                   user.isTyping=false;
               }
               console.log(e.id);
           });

    },
    methods: {
        moment(time) {
            return moment(time).fromNow();
        },
        isOnline(user){
            for(let i in this.users){
                if(this.users[i].id==user.id){
                    return this.users[i].isOnline;
                }
                return false;
            }
        },
        findUser(id,conversation_id){
            for(let i in this.conversations){
                let conversation=this.conversations[i];
                if(conversation.id ==conversation_id && conversation.participants[0].id==id){

                  return this.conversations[i].participants[0];

                }
            }
        },
        markAsRead(conversation=null){
                if(conversation==null){
                    conversation=this.conversation;
                }
            fetch(`/api/conversations/${conversation.id}/read`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.$root.csrf_token
                }
            }).then(res=>res.json())
                .then(json=>{
                    this.conversation.new_messages = 0;
                })

        },
        deleteMessage(message){
            fetch(`/api/messages/${message.id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.$root.csrf_token
                }
            }).then(res=>res.json())
                .then(json=>{
                    //delete parmantly from array
              // let index=this.messages.indexOf(message)
              //    this.messages.splice(index,1);
              message.body='This message has been deleted';
                })
        }
    },
});
app.component("Messenger", Messenger)
    .component("ChatList", ChatList)
    .mount("#chat-app");
