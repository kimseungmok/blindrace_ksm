<template>
  <b-table striped hover :items="items"></b-table>
</template>

<script>
const items = [
  { Name:'스쿠스쿠 레이스1', page:21, level:'N2', },
  { Name:'스쿠스쿠 레이스1', page:21, level:'N2', },
  { Name:'스쿠스쿠 레이스1', page:21, level:'N2', },
  { Name:'스쿠스쿠 레이스1', page:21, level:'N2', },
  { Name:'스쿠스쿠 레이스1', page:21, level:'N2', },
]

export default {
  data () {
    return {
      items: items
    }
  }
}
</script>