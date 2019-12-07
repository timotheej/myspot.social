<template>
  <div>
    <div id="map"></div>

    <!-- Modal for add spot -->

    <v-modal :showing="dialogAddSpot" @close="dialogAddSpot = false">
      <h2 class="text-xl font-bold text-gray-900">{{ $t('add_spot') }}</h2>
      <p class="mb-6 mt-4">{{ $t('message_add_spot') }}</p>

      <div class="flex justify-start">
        <button @click="addSpot" class="btn-secondary small mr-4">{{ $t('ok') }}</button>
        <button @click="dialogAddSpot = false" class="btn-primary small">{{ $t('cancel') }}</button>
      </div>
    </v-modal>

    <!-- Modal for confirm add spot -->

    <v-modal :showing="dialogConfirmSpot" @close="dialogConfirmSpot = false">
      <h2 class="text-xl font-bold text-gray-900">{{ $t('add_new_spot') }}</h2>
      <p class="mb-6 mt-4">{{ $t('message_add_new_spot') }}</p>

      <div class="flex">
        <button @click="pushSpot" class="btn-secondary small mr-4">{{ $t('yes') }}</button>
        <button @click="deleteSpot" class="btn-primary small">{{ $t('delete') }}</button>
      </div>
    </v-modal>

    <!-- Overlay spot -->

    <v-overlay :showing="overlay" @close="overlay = false" :fetchSpot="fetchSpot"></v-overlay>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import L from "leaflet";
import * as firebase from "firebase";
import circle from "@turf/circle";
import mask from "@turf/mask";
import axios from "axios";

const geofire = require("geofire");

let config = {
  apiKey: "AIzaSyCT96lcVOlMfv9eHGtPhagdYygm5gthEgA",
  authDomain: "myspot-50538.firebaseapp.com",
  databaseURL: "https://myspot-50538.firebaseio.com",
  projectId: "myspot-50538",
  storageBucket: "myspot-50538.appspot.com",
  messagingSenderId: "864468983319"
};

firebase.initializeApp(config);

function markerStyle(state) {
  return `background-image: url('/images/marker_${state}.svg');
  background-size: contain;
  width: 2.7rem;
  height: 2.7rem;
  display: block;
  left: -1.4rem;
  top: -1.8rem;
  position: relative;`;
}

function getIcon(state) {
  return L.divIcon({
    className: "my-custom-pin",
    iconAnchor: [0, 16],
    labelAnchor: [-6, 0],
    popupAnchor: [0, -36],
    html: `<span style="${markerStyle(state)}" />`
  });
}

let iconSelected = getIcon("on");

export default {
  middleware: "auth",
  layout: "basic",

  data() {
    return {
      map: {},
      dialogAddSpot: false,
      dialogConfirmSpot: false,
      newSpotLatLng: [],
      marker: {},
      markersRef: {},
      spotId: "",
      uid: "",
      geofire: {},
      overlay: false,
      fetchSpot: ""
    };
  },

  metaInfo() {
    return { title: this.$t("home") };
  },

  methods: {
    // add spot from front map
    addSpot() {
      this.dialogAddSpot = false;
      this.marker = L.marker(this.newSpotLatLng, { draggable: true })
        .on("click", () => {
          this.marker.bindPopup("").openPopup();
          this.dialogConfirmSpot = true;
        })
        .addTo(this.map);

      this.marker.bindPopup(this.$t("move_and_click")).openPopup();
    },
    // delete spot from front map
    deleteSpot() {
      this.dialogConfirmSpot = false;
      this.map.removeLayer(this.marker);
    },
    // push store in firebase and laravel api with async function
    async pushSpot() {
      let vm = this;
      let timestamp = Date.now();
      let name = "spot-" + timestamp;
      let lat = vm.marker.getLatLng().lat;
      let lng = vm.marker.getLatLng().lng;

      // store in laravel api
      try {
        const response = await axios
          .post("/api/spot", {
            spot_id: name,
            lat: lat,
            long: lng,
            uid: vm.user.id
          })
          .then(function(value) {
            // if no error store geofire in firebase
            vm.geofire
              .set(name, [vm.newSpotLatLng.lat, vm.newSpotLatLng.lng])
              .then(
                () => {},
                () => {
                  error = true;
                }
              );
            // if no error store spot in firebase
            firebase
              .database()
              .ref("/spots/" + name)
              .set({
                lat: lat,
                lon: lng,
                uid: vm.user.id
              })
              .then(
                () => {},
                () => {
                  error = true;
                }
              );
          });
      } catch (err) {
        console.log("fetch failed", err);
      }

      vm.dialogConfirmSpot = false;
      vm.marker.removeFrom(vm.map);
    }
  },
  watch: {
    spotId(newId, oldId) {
      if (oldId) {
        let oldMarker = this.markersRef[oldId].marker;
        let oldIcon = this.markersRef[oldId].icon;
        oldMarker.setIcon(oldIcon);
      }
      this.markersRef[newId].marker.setIcon(iconSelected);
    }
  },

  // get user
  computed: mapGetters({
    user: "auth/user"
  }),

  mounted() {
    let vm = this;
    let map = L.map("map", {});

    let flyToMyPosition = true;

    if (
      localStorage.getItem("zoom") &&
      localStorage.getItem("lat") &&
      localStorage.getItem("lng")
    ) {
      flyToMyPosition = false;
      map.setView([localStorage.lat, localStorage.lng], localStorage.zoom);
    } else {
      let lat = 48.85;
      let lng = 2.34;
      let zoom = 13;
      map.setView([lat, lng], zoom);
      localStorage.zoom = zoom;
      localStorage.lat = lat;
      localStorage.lng = lng;
    }

    vm.map = map;

    // set layer

    L.tileLayer(
      "https://api.mapbox.com/styles/v1/istopopoki/cj9ydd0jg7it52sp7pubunya6/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1IjoidGltb3RoZWVqIiwiYSI6ImNrMXhiN2MxdjAzd2czY2xpb29lamxicjgifQ.3phUR7543gGGJA9bhXFbpA",
      {
        attribution:
          'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
      }
    ).addTo(map);

    let accuracy = 0;
    let cst =
      40075016.686 * Math.abs(Math.cos((map.getCenter().lat * 180) / Math.PI));
    console.log("CST " + cst);
    let metresPerPixel = cst / Math.pow(2, map.getZoom() + 8);
    let markerPosition = L.circleMarker([], {
      stroke: false,
      color: "#667EEA",
      fillOpacity: 0.5,
      radius: 5
    });

    let markerAccuracy = L.circleMarker([], {
      stroke: false,
      color: "#667EEA",
      fillOpacity: 0.03,
      radius: 0
    });

    // add locate icon
    let LocationControl = L.Control.extend({
      options: {
        position: "topleft"
      },
      onAdd: function(map) {
        let container = L.DomUtil.create(
          "div",
          "leaflet-bar leaflet-control leaflet-control-custom"
        );
        container.style.backgroundColor = "white";
        container.innerHTML =
          '<a style="text-align:center; padding:0.3em;"><img src="/images/locate.svg"/></a>';
        container.style.cursor = "pointer";
        container.onclick = function() {
          if (vm.position.length) {
            map.flyTo(vm.position, 15);
          } else {
            flyToMyPosition = true;
            map.locate({ watch: true });
          }
        };
        return container;
      }
    });
    map.addControl(new LocationControl());
    map.locate({ watch: true });

    map
      .on("zoomend", function() {
        metresPerPixel = cst / Math.pow(2, map.getZoom() + 8);
        if (accuracy < 1000) {
          markerAccuracy.setRadius(accuracy / metresPerPixel).bringToBack();
        }
        localStorage.setItem("zoom", map.getZoom());
      })
      .on("locationfound", function(evt) {
        vm.position = [evt.latitude, evt.longitude];
        accuracy = evt.accuracy;
        if (accuracy < 1000) {
          markerAccuracy.setRadius(accuracy / metresPerPixel).bringToBack();
        }
        markerAccuracy.setLatLng(vm.position).addTo(map);
        markerPosition.setLatLng(vm.position).addTo(map);
        if (flyToMyPosition) {
          map.flyTo(vm.position, 15);
          flyToMyPosition = false;
        }
      })
      .on("moveend", function(evt) {
        localStorage.setItem("lat", map.getCenter().lat);
        localStorage.setItem("lng", map.getCenter().lng);
      });

    let markers = L.layerGroup().addTo(map);

    let database = firebase.database();
    let spots = database.ref("spots");

    // geofire
    let firebaseRef = firebase.database().ref("geofire");

    const geoFireInstance = new geofire.GeoFire(firebaseRef);

    vm.geofire = geoFireInstance;
    let center = map.getCenter();

    // radius for the geoquery, in meters

    let radius = L.Browser.mobile ? 3000 : 5000;

    let geoQuery = geoFireInstance.query({
      center: [center.lat, center.lng],
      radius: radius / 1000
    });

    function getCircle(center, radius) {
      let c = [center.lng, center.lat];
      var options = { units: "meters" };
      return circle(c, radius, options);
    }

    function getCircleLayer() {
      let options = {
        fillColor: "black",
        fillOpacity: 0.1,
        stroke: false
      };
      return L.geoJSON(
        mask(getCircle(center, radius), getCircle(center, 1000000)),
        options
      );
    }
    let spotCircle = getCircleLayer().addTo(map);

    // on right click
    map
      .on("contextmenu", function(e) {
        if (!map.hasLayer(vm.marker)) {
          vm.dialogAddSpot = true;
          vm.newSpotLatLng = e.latlng;
        }
      })
      .on("click", () => {
        vm.spotId = "";
        vm.overlay = false;
      });

    // on move
    map.on("moveend", function() {
      if (map.distance(map.getCenter(), center) > radius * 0.66) {
        center = map.getCenter();
        geoQuery.updateCriteria({
          center: [center.lat, center.lng],
          radius: radius / 1000
        });
        spotCircle.clearLayers();
        spotCircle = getCircleLayer().addTo(map);
      }
    });

    let icon1 = getIcon("on");
    let icon2 = getIcon("off");

    geoQuery.on("key_entered", function(key, location, distance) {
      spots.child(key).once("value", function(snapshot) {
        let spot = snapshot.val();

        if (spot) {
          let icon = icon2;
          let marker = L.marker([spot.lat, spot.lon], {
            icon: icon
          }).on("click", function(ev) {
            stop(ev);
            vm.spotId = key;
            vm.overlay = true;
            vm.fetchSpot = key;
            vm.spotPosition = [spot.lat, spot.lon];

            // center map

            /*             let spotPoint = map.latLngToLayerPoint(
              vm.spotPosition,
              map.getZoom()
            );
            setTimeout(function() {
              let bw = document.getElementsByClassName("overlay")[0]
                .clientWidth;
              spotPoint = spotPoint.add([bw / 2, 0]);
              let newCenter = map.layerPointToLatLng(spotPoint, map.getZoom());
              map.panTo(newCenter);
            }, 500); */
          });
          vm.markersRef[key] = { marker: marker, icon: icon };
          markers.addLayer(marker);
        }
      });
    });

    geoQuery.on("key_exited", function(key, location, distance) {
      if (key in vm.markersRef) {
        markers.removeLayer(vm.markersRef[key].marker);
      }
    });
  }
};
</script>
<style lang="scss">
@import "https://unpkg.com/leaflet@1.3.4/dist/leaflet.css";

#map {
  position: fixed;
  height: calc(100vh - 4rem);
  top: 4rem;
  width: 100%;
  z-index: 1;
}
</style>
