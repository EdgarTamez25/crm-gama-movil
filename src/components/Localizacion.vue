<template>
	<v-layout row wrap>
	  <v-flex xs12>
	    <v-card class="pa-2">
			  <div class="hello">
			    <h1>{{ msg }}</h1>
			    <v-divider></v-divider>
			    <div id="map"></div>
			  </div>
	    </v-card>
	  </v-flex>
	</v-layout>

</template>

<script>
  document.addEventListener("deviceready", onDeviceReady, false);
  function onDeviceReady() {
    console.log("navigator.geolocation works well");
  }

export default {
  name: 'hello',
  data () {
    return {
      map:'',
      latitude: '',
      longitude: '',
      msg: 'Mi ubicación'
    }
  },

  created(){
    this.iniciar()
  },

  methods:{
    iniciar(){
      //Llamar el dispositivo y hacer que escuhe al gps
      document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
    },

    onDeviceReady(){
      //si se activo el documento llamar la funcion de receivedEvent
      this.receivedEvent('deviceready')
    },

    receivedEvent(id){
      //se crea la variable me para ser utilizada dentro de las sigueintes funciones
      var me = this
      //esta funcion se activa si no tuvo ningun error al iniciar la ubicación
      var onMapSuccess = function (position) {
        //se crea el mapa de google maps y se dibuja en la etiqueta con el id ("myMap")
        me.map = new google.maps.Map(document.getElementById("map"),{
          //center manda las coordenadas de tuubicación
          center:{lat: position.coords.latitude , lng: position.coords.longitude},
          //el zoom es que tan preciso quieres que se vea el mapa, en este caso 20 para que aparezca la casa
          // si quieres que apareta el municipio o estado se coloca un 11
          zoom: 20 ,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          mapTypeControl: true,
          mapTypeControlOptions: {
              style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
              position: google.maps.ControlPosition.TOP_LEFT
          },
          zoomControl: true,
          zoomControlOptions: {
              position: google.maps.ControlPosition.RIGHT_CENTER
          },
          scaleControl: true,
          streetViewControl: false,
          streetViewControlOptions: {
              position: google.maps.ControlPosition.LEFT_TOP
          },
          fullscreenControl: true,
          disableDefaultUI: true,    
        });

        var image = new google.maps.MarkerImage("http://www.md3.mx/clientes/marker2.ico");
        var beachMarker = new google.maps.Marker({
          position: {lat: position.coords.latitude , lng: position.coords.longitude},
          map: me.map,
          icon: image
        });
        
      }

      //Esta funcion se llama solo si se ocurre algun error
      function onMapError(error) {
        console.log('code: ' + error.code + '\n' + 'message: ' + error.message + '\n');
      }
      
      //se manda a llamar la posición mediante el gps
      navigator.geolocation.getCurrentPosition(onMapSuccess, onMapError, { enableHighAccuracy: true });
    },
  }
}


</script>
<style scoped>
    #map {
    height:300px;
    width: 100%;
   }
</style>