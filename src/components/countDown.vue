<template>
  <div class="countDown">
    <span>{{timeText}}123</span>
  </div>
</template>

<script type="text/ecmascript">
  export default {
    props: {
      startTime: Number,
      endTime: Number,
      start: Boolean
    },
    data () {
      return {
        timeText: '00:00:00',
        saleStatus: 1 // 1为未开始，2为抢购中，3为已结束
      }
    },
    created () {
      if(this.start){
        console.log(this.startTime + ' : ' + this.endTime)
        setCountDow(this.startTime, this.endTime);
      }
    },
    mounted () {},
    methods: {
      freshTime (time, sectime) {
        let endtime = time
        let nowtime = new Date().valueOf()
        let lefttime = parseInt(endtime - nowtime) // 这是毫秒，如果再/1000就是秒  
        if(lefttime > 0) {
          // 获取剩下的日、小时、分钟、秒钟  
          // 一天有多少毫秒，一小时有多少毫秒，一分钟有多少毫秒，一秒钟有多少毫秒  
          let dm = 24 * 60 * 60 * 1000
          let d = parseInt(lefttime / dm)
          let hm = 60 * 60 * 1000
          let h = parseInt((lefttime / hm) % 24)
          let mm = 60 * 1000
          let m = parseInt((lefttime / mm) % 60)
          let s = parseInt((lefttime / 1000) % 60)
          m = checktime(m)
          s = checktime(s)
          let str = ""
          if(d > 0) {
            str = d + "天" + h + "小时" + m + "分钟"
          } else {
            str = h + "小时" + m + "分钟" + s + "秒"
          }
          return str
        } else if(lefttime <= 0) {
          if(sectime !== undefined) {
            setCountDow(time, sectime)
          } else {
            return false
          }
        }
      },
      checkTime (i) {
        if(i < 10) {
          i = "0" + i
        } else {
          i = i
        }
        return i
      },
      setCountDow (startTime, endTime) {
        let txt = ''
        let _nowTime = new Date().valueOf()
        if(_nowTime >= endTime && endTime != 0) {
          // 超过购买时间
          this.timeText = "活动已结束"
          this.saleStatus = 3
        } else if((_nowTime > startTime && _nowTime < endTime) || (startTime == 0 && _nowTime < endTime)) {
          // 抢购中
          txt = '距结束：'
          this.saleStatus = 2
          let timeStr = freshTime(endTime)
          if(timeStr == false || timeStr == undefined) {
            this.timeText = '活动已结束'
            this.saleStatus = 3
          } else {
            this.timeText = txt + timeStr
          }
          let timer1
          timer1 = setInterval(() => {
            let timeStr = freshTime(endTime)
            if(timeStr == false || timeStr == undefined) {
              this.timeText = '活动已结束'
              this.saleStatus = 3
              clearInterval(timer1)
            } else {
              this.timeText = txt + timeStr
            }
          }, 1000)
        } else if(_nowTime <= startTime) {
          // 倒计时中
          txt = '距开始：'
          let timer2
          this.saleStatus = 1
          timer2 = setInterval(() => {
            let timeStr = freshTime(startTime, endTime)
            if(timeStr == false || timeStr == undefined) {
              clearInterval(timer2)
            } else {
              this.timeText = txt + timeStr
            }
          }, 1000)
        } else if(_nowTime > startTime && endTime == 0) {
          this.saleStatus = 2
          this.timeText = '抢购中'
        }
      }
    }
  }
</script>

<style lang="scss" scoped="" type="text/css">

</style>
