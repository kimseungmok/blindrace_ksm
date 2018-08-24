<template>
    <div>
    <b-btn v-b-modal.modal1>그룹 만들기</b-btn>
     <b-modal id="modal1" title="그룹만들기" >
    <p class="my-4"> 
        <b-container fluid>
    <b-row class="my-1" v-for="type in types" :key="type">
      <b-col sm="3"><label :for="`type-${type}`"> {{ type }}:</label></b-col>
      <b-col sm="9"><b-form-input :id="`type-${type}`" :type="type"></b-form-input></b-col>
    </b-row>
  </b-container></p>
  </b-modal>
</div>
    
</template>

<script>
       export default {
  data () {
    return {
      types: [
        '그룹이름', 'Pin 번호'
        ]
    }
  }
       }
</script>