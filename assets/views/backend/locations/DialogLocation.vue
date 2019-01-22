<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :xs="24" :sm="14">
            <h2 v-if="location.id != 0">{{ $root.labels.edit_location }}</h2>
            <h2 v-else>{{ $root.labels.new_location }}</h2>
          </el-col>
          <el-col :xs="24" :sm="10" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="location" ref="location" :rules="rules" label-position="top" @submit.prevent="onSubmit">

        <!-- Profile Picture -->
        <div class="am-location-profile">
          <picture-upload
              :edited-entity="this.location"
              :entity-name="'location'"
              @pictureSelected="pictureSelected"
          >
          </picture-upload>
          <h2>{{ location.name }}</h2>
        </div>

        <!-- Name -->
        <el-form-item :label="$root.labels.name + ':'" prop="name">
          <el-input
              v-model="location.name"
              placeholder=""
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

        <!-- Address -->
        <el-form-item :label="$root.labels.address + ':'" prop="address">
          <div class="el-input">
            <vue-google-autocomplete
                ref="location.address"
                id="address-autocomplete"
                classname="el-input__inner"
                placeholder=""
                @placechanged="getAddressData"
                :value="location.address"
            >
            </vue-google-autocomplete>
          </div>
        </el-form-item>

        <!-- Pin Icon -->
        <el-form-item :label="$root.labels.pin_icon+':'">
          <el-select
              v-model="location.pin"
              placeholder=""
              @change="initMap(location.latitude, location.longitude, location.pin)"
          >
            <el-option
                v-for="item in formOptions.pins"
                :key="item.id"
                :label="item.name"
                :value="item.iconUrl"
                class="pin-icon"
            >
              <img :src="item.iconUrl"/> <span>{{ item.name }}</span>
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Map -->
        <el-form-item :label="$root.labels.map+':'">
          <div id="map"></div>
        </el-form-item>

        <!-- Not Right Address -->
        <el-button type="text" class="am-text-button-icon" @click="showLatLng = !showLatLng">
          <img class="svg" :alt="$root.labels.delete" :src="$root.getUrl+'public/img/location.svg'"/>
          {{ $root.labels.not_right_address }}
        </el-button>

        <transition name="slide-down">
          <div v-show="showLatLng">
            <el-row :gutter="16">

              <!-- Latitude -->
              <el-col :span="12">
                <el-form-item :label="$root.labels.latitude+':'">
                  <el-input-number
                      v-model="location.latitude"
                      @change="initMap(location.latitude, location.longitude, location.pin)"
                  >
                  </el-input-number>
                </el-form-item>
              </el-col>

              <!-- Longitude -->
              <el-col :span="12">
                <el-form-item :label="$root.labels.longitude+':'">
                  <el-input-number
                      v-model="location.longitude"
                      @change="initMap(location.latitude, location.longitude, location.pin)"
                  >
                  </el-input-number>
                </el-form-item>
              </el-col>

            </el-row>
          </div>
        </transition>

        <!-- Phone -->
        <el-form-item :label="$root.labels.phone+':'">
          <phone-input
              :savedPhone="location.phone"
              @phoneFormatted="phoneFormatted"
          >
          </phone-input>
        </el-form-item>

        <!-- Description -->
        <el-form-item :label="$root.labels.description+':'">
          <el-input
              type="textarea"
              :autosize="{ minRows: 4, maxRows: 6}"
              placeholder=""
              v-model="location.description"
              @input="clearValidation()"
          >
          </el-input>
        </el-form-item>

      </el-form>
    </div>

    <dialog-actions
        formName="location"
        urlName="locations"
        :isNew="location.id === 0"
        :entity="location"
        :getParsedEntity="getParsedEntity"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: true,
          haveRemove: $root.settings.capabilities.canDelete === true,
          haveRemoveEffect: true,
          ignoreDeleteEffect: true,
          haveDuplicate: true
        }"

        :message="{
          success: {
            save: $root.labels.location_saved,
            remove: $root.labels.location_deleted,
            show: $root.labels.location_visible,
            hide: $root.labels.location_hidden
          },
          confirm: {
            remove: $root.labels.confirm_delete_location,
            show: $root.labels.confirm_show_location,
            hide: $root.labels.confirm_hide_location,
            duplicate: $root.labels.confirm_duplicate_location
          }
        }"
    >
    </dialog-actions>
  </div>
</template>

<script>
  import DialogActions from '../parts/DialogActions.vue'
  import PhoneInput from '../../parts/PhoneInput.vue'
  import PictureUpload from '../parts/PictureUpload.vue'
  import VueGoogleAutocomplete from 'vue-google-autocomplete'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'

  export default {

    mixins: [imageMixin, notifyMixin],

    props: {
      location: null
    },

    data () {
      return {
        showLatLng: false,

        formOptions: {
          pins: [
            {
              id: 1,
              name: 'Orange',
              iconUrl: `${this.$root.getUrl}/public/img/pins/orange.png`
            },
            {
              id: 2,
              name: 'Red',
              iconUrl: `${this.$root.getUrl}/public/img/pins/red.png`
            },
            {
              id: 3,
              name: 'Purple',
              iconUrl: `${this.$root.getUrl}/public/img/pins/purple.png`
            },
            {
              id: 4,
              name: 'Green',
              iconUrl: `${this.$root.getUrl}/public/img/pins/green.png`
            }
          ]
        },

        rules: {
          name: [
            {required: true, message: this.$root.labels.enter_location_name_warning, trigger: 'submit'}
          ],
          address: [
            {required: true, message: this.$root.labels.enter_location_address_warning, trigger: 'submit'}
          ]
        }

      }
    },

    created () {

    },

    mounted: !AMELIA_LITE_VERSION ? function () {
      this.initMap(this.location.latitude, this.location.longitude, this.location.pin)
      this.inlineSVG()
    } : function () {},

    methods: {
      getParsedEntity: !AMELIA_LITE_VERSION ? function () {
        return this.location
      } : function () {},

      closeDialog: !AMELIA_LITE_VERSION ? function () {
        this.$emit('closeDialog')
      } : function () {},

      getAddressData: !AMELIA_LITE_VERSION ? function (addressData) {
        this.clearValidation()
        this.location.latitude = addressData.latitude
        this.location.longitude = addressData.longitude
        this.location.address = document.getElementById('address-autocomplete').value

        this.initMap(this.location.latitude, this.location.longitude, this.location.pin)

        return addressData
      } : function () {},

      initMap: !AMELIA_LITE_VERSION ? function (lat = this.location.latitude, lng = this.location.longitude, pin) {
        if (typeof lat !== 'undefined' && typeof lng !== 'undefined') {
          this.clearValidation()
          let google = window.google
          let location = this.location

          let map = new google.maps.Map(document.getElementById('map'), {
            zoom: 16,
            center: {lat: lat, lng: lng}
          })

          let beachMarker = new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: map,
            icon: pin
          })

          google.maps.event.addListener(map, 'click', function (event) {
            beachMarker.setPosition({lat: event.latLng.lat(), lng: event.latLng.lng()})

            location.latitude = event.latLng.lat()
            location.longitude = event.latLng.lng()
          })
        }
      } : function () {},

      phoneFormatted: !AMELIA_LITE_VERSION ? function (phone) {
        this.clearValidation()
        this.location.phone = phone
      } : function () {},

      pictureSelected: !AMELIA_LITE_VERSION ? function (pictureFullPath, pictureThumbPath) {
        this.clearValidation()
        this.location.pictureFullPath = pictureFullPath
        this.location.pictureThumbPath = pictureThumbPath
      } : function () {},

      clearValidation: !AMELIA_LITE_VERSION ? function () {
        if (typeof this.$refs.location !== 'undefined') {
          this.$refs.location.clearValidate()
        }
      } : function () {}
    },

    watch: {
      'location.latitude' () {
        if (typeof this.location.latitude === 'undefined') {
          this.location.latitude = 0
          this.initMap()
        }
      },

      'location.longitude' () {
        if (typeof this.location.longitude === 'undefined') {
          this.location.longitude = 0
          this.initMap()
        }
      }
    },

    components: {
      VueGoogleAutocomplete,
      PhoneInput,
      PictureUpload,
      DialogActions
    }

  }
</script>