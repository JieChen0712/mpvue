<template>
  <div class="richtext">
    	<wxParse :content="context" />
  </div>
</template>

<script type="text/ecmascript">
import wxParse from 'mpvue-wxparse'
export default {
  data () {
    return {
      context: '<p style="text-align: left;">12352</p>'
    }
  },
  components: {
    wxParse
  },
  created () {},
  mounted () {},
  methods: {}
}
</script>

<style type="text/css">
@import url("~mpvue-wxparse/src/wxParse.css");
</style>
