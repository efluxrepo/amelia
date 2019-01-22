<template>
    <div v-if="$root.isLite">
        <!-- Dialog Lite -->
        <transition name="slide">
            <el-dialog class="am-side-dialog" :visible.sync="$root.dialogLite" :show-close="false" v-if="$root.dialogLite">
                <div class="am-dialog-scrollable am-lite-dialog-no-margin">
                    <!-- Dialog Header -->
                    <div class="am-dialog-header">
                        <el-row>
                            <el-col :span="20">
                                <h2> Upgrade </h2>
                            </el-col>
                            <el-col :span="4" class="align-right">
                                <el-button @click="$root.dialogLite = false" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
                            </el-col>
                        </el-row>
                    </div>

                    <div class="am-lite am-lite-dialog">
                        <img :src="$root.getUrl + 'public/img/calendar-illustration-min.png'" class="am-lite-dialog-image">

                        <p class="am-lite-dialog-title">Oops, this is a full version feature!</p>
                        <p class="am-lite-dialog-description">Upgrade today to make the appointment booking process even easier and more flexible:</p>

                        <el-button @click="upgrade" type="primary">Upgrade</el-button>

                        <ul class="am-lite-features">
                            <li>
                                <i class="am-lite-check"></i><span>   Unlimited appointments</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Unlimited services and staff members</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Unlimited locations</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Multiple shortcodes</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Service extras</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Discount coupons</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Premium support</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Front-end interfaces</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   PayPal and Stripe integration</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   Google Calendar 2-way sync</span>
                            </li>
                            <li>
                                <i class="am-lite-check"></i><span>   WooCommerce integration</span>
                            </li>
                            <li>
                                <span>   <br/>and many more...</span>
                            </li>
                        </ul>

                        <span>   Pre-purchase question? <a href="https://wpamelia.com/contact/" target="_blank">Contact us!</a></span>
                    </div>
                </div>
            </el-dialog>
        </transition>
    </div>
</template>

<script>
  export default {

    data () {
      return {
      }
    },

    methods: {
      upgrade: function () {
        window.open('https://wpamelia.com', '_blank')
      }
    }
  }
</script>
