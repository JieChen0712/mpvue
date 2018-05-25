<template>
  <div class="countTime">
    <p class="time">{{timeText}}</span>
  </div>
</template>

<script>
  export default {
    props: {
      startTime: Number,
      endTime: Number,
      start: Boolean,
      index: Number
    },
    data () {
      return {
        timeText: '00:00:00',
        saleStatus: 1 // 1为未开始，2为抢购中，3为已结束
      }
    },
    created () {
      if (this.start) {
        this.setCountDow(Number(this.startTime) * 1000, Number(this.endTime) * 1000)
      }
    },
    methods: {
      checkTime (i) {
        if (i < 10) {
          i = '0' + i
        }
        return i
      },
      freshTime (time, sectime) {
        let endtime = Number(time)
        let nowtime = new Date().valueOf()
        let lefttime = parseInt(endtime - nowtime)
        if (lefttime > 0) {
          let dm = 24 * 60 * 60 * 1000
          let d = parseInt(lefttime / dm)
          let hm = 60 * 60 * 1000
          let h = parseInt((lefttime / hm) % 24)
          let mm = 60 * 1000
          let m = parseInt((lefttime / mm) % 60)
          let s = parseInt((lefttime / 1000) % 60)
          m = this.checkTime(m)
          s = this.checkTime(s)
          let str = ''
          if (d > 0) {
            str = d + '天' + h + '小时' + m + '分钟'
          } else {
            str = h + '小时' + m + '分钟' + s + '秒'
          }
          return str
        } else if (lefttime <= 0) {
          if (sectime !== undefined) {
            this.setCountDow(time, sectime)
          } else {
            return false
          }
        }
      },
      setCountDow (startTime, endTime) {
        let txt = ''
        let _nowTime = new Date().valueOf()
        if (_nowTime >= endTime && endTime !== 0) {
          // 超过购买时间
          this.timeText = '活动已结束'
          this.saleStatus = 3
          this.$emit('over', this.saleStatus, this.index)
        } else if ((_nowTime > startTime && _nowTime < endTime) || (startTime === 0 && _nowTime < endTime)) {
          // 抢购中
          txt = '距结束：'
          this.saleStatus = 2
          this.$emit('over', this.saleStatus, this.index)
          let timeStr = this.$options.methods.freshTime(endTime)
          if (timeStr === false || timeStr === undefined) {
            this.timeText = '活动已结束'
            this.saleStatus = 3
            this.$emit('over', this.saleStatus, this.index)
          } else {
            this.timeText = txt + timeStr
          }
          let timer1
          timer1 = setInterval(() => {
            let timeStr = this.freshTime(endTime)
            if (timeStr === false || timeStr === undefined) {
              this.timeText = '活动已结束'
              this.saleStatus = 3
              this.$emit('over', this.saleStatus, this.index)
              clearInterval(timer1)
            } else {
              this.timeText = txt + timeStr
            }
          }, 1000)
        } else if (_nowTime <= startTime) {
          // 倒计时中
          txt = '距开始：'
          let timer2
          this.saleStatus = 1
          this.$emit('over', this.saleStatus, this.index)
          timer2 = setInterval(() => {
            let timeStr = this.freshTime(startTime, endTime)
            if (timeStr === false || timeStr === undefined) {
              clearInterval(timer2)
            } else {
              this.timeText = txt + timeStr
            }
          }, 1000)
        } else if (_nowTime > startTime && endTime === 0 && startTime !== 0) {
          this.saleStatus = 2
          this.$emit('over', this.saleStatus, this.index)
          this.timeText = '抢购中'
        }
      }
    }
  }
</script>

<style lang="scss" scoped="" type="text/css">
.countTime{
  .time{
    text-align: center;
    font-size: 14px;
    color: $thame-color;
  }
}
</style>