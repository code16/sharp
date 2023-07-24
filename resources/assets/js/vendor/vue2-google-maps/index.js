import lazy from './utils/lazyValue'
import {loadGmapApi} from './manager'

import Marker from './components/marker'

// Vue component imports
import InfoWindow from './components/infoWindow.vue'
import Map from './components/map.vue'

import MapElementMixin from './components/mapElementMixin'
import MapElementFactory from './components/mapElementFactory'
import MountableMixin from './utils/mountableMixin'



let GmapApi = null

// export everything
export {loadGmapApi, Marker,
  InfoWindow, Map, MapElementMixin, MapElementFactory,
  MountableMixin}

export function install (Vue, options) {
  // Set defaults
  options = {
    installComponents: true,
    autobindAllEvents: false,
    ...options
  }

  // Update the global `GmapApi`. This will allow
  // components to use the `google` global reactively
  // via:
  //   import {gmapApi} from 'vue2-google-maps'
  //   export default {  computed: { google: gmapApi }  }
  GmapApi = new Vue({data: {gmapApi: null}})

  const defaultResizeBus = new Vue()

  // Use a lazy to only load the API when
  // a VGM component is loaded
  let gmapApiPromiseLazy = makeGmapApiPromiseLazy(options)

  Vue.mixin({
    created () {
      this.$gmapDefaultResizeBus = defaultResizeBus
      this.$gmapOptions = options
      this.$gmapApiPromiseLazy = gmapApiPromiseLazy
    }
  })
  Vue.$gmapDefaultResizeBus = defaultResizeBus
  Vue.$gmapApiPromiseLazy = gmapApiPromiseLazy

  if (options.installComponents) {
    Vue.component('GmapMap', Map)
    Vue.component('GmapMarker', Marker)
    Vue.component('GmapInfoWindow', InfoWindow)
    Vue.component('GmapPolyline', Polyline)
    Vue.component('GmapPolygon', Polygon)
    Vue.component('GmapCircle', Circle)
    Vue.component('GmapRectangle', Rectangle)
    Vue.component('GmapAutocomplete', Autocomplete)
    Vue.component('GmapPlaceInput', PlaceInput)
    Vue.component('GmapStreetViewPanorama', StreetViewPanorama)
  }
}

function makeGmapApiPromiseLazy (options) {
  // Things to do once the API is loaded
  function onApiLoaded () {
    GmapApi.gmapApi = {}
    return window.google
  }

  if (options.load) { // If library should load the API
    return lazy(() => { // Load the
      // This will only be evaluated once
      if (typeof window === 'undefined') { // server side -- never resolve this promise
        return new Promise(() => {}).then(onApiLoaded)
      } else {
        return new Promise((resolve, reject) => {
          try {
            window['vueGoogleMapsInit'] = resolve
            loadGmapApi(options.load, options.loadCn)
          } catch (err) {
            reject(err)
          }
        })
        .then(onApiLoaded)
      }
    })
  } else { // If library should not handle API, provide
    // end-users with the global `vueGoogleMapsInit: () => undefined`
    // when the Google Maps API has been loaded
    const promise = new Promise((resolve) => {
      if (typeof window === 'undefined') {
        // Do nothing if run from server-side
        return
      }
      window['vueGoogleMapsInit'] = resolve
    }).then(onApiLoaded)

    return lazy(() => promise)
  }
}

export function gmapApi () {
  return GmapApi.gmapApi && window.google
}
