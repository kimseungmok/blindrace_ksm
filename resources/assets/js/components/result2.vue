<template>
  <main>
      
       <button @click="start">Start</button>
      <button @click="stop">Stop</button>


    <button v-on:click="show = !show">결과  표시</button>

  <div class="d2"><transition name="fade"><p v-if="!show"><img src="https://i.imgur.com/wlfxK1r.png"></p></transition></div>
  <transition name="fade"><p v-if="!show"><img src="https://i.imgur.com/HJdZ4qZ.png"></p></transition>
  <transition name="fade"><p v-if="!show"><img src="https://i.imgur.com/VXh9aAI.png"></p></transition>
<div class="progress">
  <div class="bar" :style="'height:30px;background:blue;width:' + progress + '%'"></div>
</div>

  

  </main> 
  
  
</template>




<script>
export default {
  data() {
    
    return {
      show : true
    }
  },

   methods: {
      start () {
        this.$confetti.start()
      },

      stop () {
        this.$confetti.stop()
      }
    }
   
  
  
}


</script>




    <style>
         body {    
        
          background-image: url('https://i.imgur.com/aiJMdSy.png');
          background-size: 65%;
          background-repeat: no-repeat;
         }

         
      
          .fade-enter-active, .fade-leave-active {
            transition: opacity .5s;
          }
          .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
            opacity: 0;
          }
        

        /* .d2 {
          position: absolute;
          bottom:5px;
          right: 20px;
        } */


        </style>