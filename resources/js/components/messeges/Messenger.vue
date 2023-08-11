<template>
  <!-- Chat -->
  <main class="main is-visible" data-dropzone-area="">
            <div class="container h-100">
                <div class="d-flex flex-column h-100 position-relative">
                    <!-- Chat: Header -->
                    <ChatHeader />
                    <!-- Chat: Header -->

                    <!-- Chat: Content -->
                    <ChatContent />
                    <!-- Chat: Content -->

                    <!-- Chat: Footer -->
                    <ChatFooter />
                    <!-- Chat: Footer -->
                </div>

            </div>
        </main>
     
        <!-- Chat -->
</template>

<script >
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

export default{
    components:{
        ChatHeader,
        ChatContent,
        ChatFooter
    },
 
}
</script>
