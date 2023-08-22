<template>
    <!-- Chat -->


    <main class="main is-visible" data-dropzone-area="">
        <div class="container h-100">
            <div v-if="!$root.conversation" class="d-flex flex-column h-100 justify-content-center">

                <div class="mb-6">
                    <span class="icon icon-xl text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" ></svg>
                    </span>
                </div>


                <p class="text-muted text-center">
                    Pick A person to start a conversation
                </p>
            </div>
            <div v-if="$root.conversation" class="d-flex flex-column h-100 position-relative">
                <!-- Chat: Header -->
                <ChatHeader :conversation="conversation"/>
                <!-- Chat: Header -->

                <!-- Chat: Content -->
                <ChatContent :conversation="conversation"/>
                <!-- Chat: Content -->

                <!-- Chat: Footer -->
                <ChatFooter :conversation="conversation"/>
                <!-- Chat: Footer -->
            </div>
        </div>

    </main>

    <!-- Chat -->
</template>

<script>
import ChatHeader from './ChatHeader.vue'
import ChatContent from './ChatContent.vue'
import ChatFooter from './ChatFooter.vue'

// import { ref } from 'vue'
// import { useStore } from 'vuex'
// import { useRoute } from 'vue-router'
// import { useToast } from 'vue-toastification'
// import { useDropzone } from 'vue-dropzone'
// import { useClickAway } from '@vueuse/core'
// import { useInfiniteScroll } from '@vueuse/core'
// import { useWindowSize } from '@vueuse/core'

export default {
    components: {
        ChatHeader,
        ChatContent,
        ChatFooter
    },

    props: ['conversation'],

    data() {
        return {}
    }
}
</script>
