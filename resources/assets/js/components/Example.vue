<template>
    <div>
        <Minsu></Minsu>
        
        <Nav></Nav>
    </div>
</template>
<script>
 import Footer from './Footer.vue';
    import Minsu from './Minsu.vue';
    import Modal from './Modal.vue';
    import Nav from './Nav.vue';
    export default {
       components:{
            Footer  ,  Minsu , Modal , Nav
       }
    }


</script>