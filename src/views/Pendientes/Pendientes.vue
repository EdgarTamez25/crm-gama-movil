<template>
  <v-row class="fill-height">
    <v-col>
			<v-card-title  class="font-weight-bold"> PENDIENTES </v-card-title>
      <v-sheet height="70">
        <v-toolbar flat color="white">
          <v-btn fab x-small color="rosa" dark class="ma-1" @click="prev"> <!--  FECHA  ANTERIOR -->
            <v-icon small>mdi-chevron-left</v-icon>
          </v-btn>
					<v-toolbar-title class="ma-1">{{ title }}</v-toolbar-title>  <!-- FECHA ACTUAL-->
          <v-btn fab x-small color="rosa" dark class="ma-1" @click="next"> <!-- SIGUIENTE FECHA -->
            <v-icon small>mdi-chevron-right</v-icon>
          </v-btn>
          
          <v-spacer></v-spacer>
					<!-- BOTON PARA FILTRO DE FECHAS-->
          <v-menu bottom right> 
            <template v-slot:activator="{ on }">
              <v-btn outlined color="celeste"  v-on="on" small>
                <span>{{ typeToLabel[type] }}</span> <v-icon right>mdi-menu-down</v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item @click="type = 'day'">
                <v-list-item-title>Día</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'week'">
                <v-list-item-title>Semana</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'month'">
                <v-list-item-title>Mes</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-toolbar>
      </v-sheet>

      <v-sheet :height="pantalla"> <!-- CALENDARIO -->
        <v-calendar
          ref="calendar"
          v-model="focus"
          color="negro white--text"
          :events="events"
          :event-color="getEventColor"
          :type="type"
          @click:event="GenerarEvento"
          @click:more="verDiaActual"
          @click:date="verDiaActual"
          @change="ActualizaCompromisos"
					locale="es-es"
        ></v-calendar>
      </v-sheet>

			<v-dialog v-model="detalleModal" width="500">
				<v-card >
					<v-card-actions class="headline white--text celeste">
						<v-card-title >{{ name }}</v-card-title>
						<v-spacer></v-spacer>
						<v-btn color="white" small  text @click="detalleModal=false"><v-icon  >clear</v-icon></v-btn>
					</v-card-actions>
					
					<v-btn color="green" text small block v-if="cumplimiento === 1">Compromiso Cumplido</v-btn>
					
					<v-simple-table fixed-header height="auto">
						<template v-slot:default>
							<thead>
								<tr><th class="text-left">Responsable</th><th class="text-left">{{ nomvend}}</th></tr>
								<tr><th class="text-left">Cliente</th><th class="text-left">{{ nomcli }}</th></tr>
								<tr><th class="text-left">Fecha </th> <th class="text-left">{{ fecha }}</th></tr>
								<tr><th class="text-left">Hora</th><th class="text-left">{{hora}}</th></tr>
								<tr v-if="comentarios"><th class="text-left">Comentarios</th><th class="text-left">{{comentarios}}</th></tr>
							</thead>
						</template>
					</v-simple-table>
					
				</v-card>
			</v-dialog>

    </v-col>
  </v-row>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	var moment = require('moment'); 
  export default {
    data: () => ({
      focus: '',
      type: 'day',
      typeToLabel: {
        month: 'Mes',
        week: 'Semana',
        day: 'Dia ',
			},
			
      start: null,
			end: null,
			compromisos: [],
			events: [],
			colors: ['rosa', 'gris', 'morado', 'celeste' ],
			// VARIABLES PRINCIPALES
			name 				 : '',
			start_p 			 : '',
			end    			 : '',
			color  			 : '',
			nomcli 			 : '',
			nomvend 		 : '',
			cumplimiento : '',
			nomuser 		 : '',
			fecha        : '',
			hora         : '',
			comentarios  : '',
			// ALERTAS
			detalleModal: false,
			fechaActual: ''
		}),

		created(){ 
			if(!this.getUsuarios.id){ this.Salir() }

			const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate(); // ASIGNAR FECHA
			this.fechaActual = y + "-" + m +"-" + d;
			moment.locale('es') /// inciar Moment 
		}, 

    computed: {
				...mapGetters('Compromisos'  ,['getCompromisos','Loading']), // IMPORTANDO USO DE VUEX - CLIENTES (GETTERS)
      	...mapGetters('Usuarios',['getUsuarios']),

      pantalla () {
        switch (this.$vuetify.breakpoint.name) {
          case 'xs': return 500
          case 'sm': return 500
          case 'md': return 400
          case 'lg': return 500
          case 'xl': return 700
        }
      },

      title () {
        const { start, end } = this
        if (!start || !end) { return '' }

				// EJEMPLO: https://www.lawebdelingles.com/nivel-a1/ordinales-en-ingles/
        const startMonth = this.monthFormatter(start);// INICIO DEL MES ACTUAL
        const endMonth 	 = this.monthFormatter(end); 	// FIN DEL MES ACTUAL
        const startYear  = start.year ; // INICIO AÑO ACTUAL
        const endYear 	 = end.year ; 	// FIN AÑO ACTUAL
        const startDay	 = start.day + this.nth(start.day) ;  // DIA DE INICO
        const endDay 		 = end.day + this.nth(end.day) ;      // DIA FINAL

        switch (this.type) {
          case 'month':
            return `${startMonth} ${startYear}` // RETORNO MES INICIAL Y AÑO INICIAL
          case 'week':
          case 'day':
            return `${startMonth} ${startDay} ${startYear}` // RETORNO MES, DIA Y AÑO INICIAL.
        }
        return ''
			},
			
      monthFormatter () { // FORMATO DEL MES
        return this.$refs.calendar.getFormatter({
          timeZone: 'UTC', month: 'long',
        })
      },
		},
		
    mounted () { this.$refs.calendar.checkChange() 	},// GENERA EL CAMBIO EN EL CALENDARIO
	
    methods: {
			...mapActions('Compromisos'  ,['consultaCompromisos']),
      ...mapGetters('Usuarios',['Salir']),


      verDiaActual ({ date }) {  this.focus = date; this.type = 'day'}, // COLOCO LA VISTA DEL CALENDARIO EN MODO "DIA ACTUAL"
			prev () { this.$refs.calendar.prev()  },  // FUNCION PARA RETROCEDER FECHA
      next () { this.$refs.calendar.next()  }, 	// FUNCION PARA SIGUIENTE FECHA
      nth (d) { return d > 3 && d < 21? 'th': ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10] 	}, // RETORNA EL DIA
      getEventColor (event) { return event.color }, 	// GENERA COLOR RANDOM

			GenerarEvento ({ event }) { // ABRE EL DIALOGO PARA MOSTRAR EL EVENTO
				this.name 				= event.name
				this.start_p 			= event.start
				this.end    			= event.end
				this.color  			= event.nomcli
				this.nomcli 			= event.nomcli
				this.nomvend 			= event.nomvend
				this.cumplimiento = event.cumplimiento
				this.nomuser 			= event.nomuser
				this.fecha        = moment(event.fecha).format('LL')
				this.hora         = moment(event.start).calendar(); 
				this.detalleModal = true;
				this.comentarios  = event.comentarios
			},

      ActualizaCompromisos ({ start, end }) {
				const events = [];
				const payload = { id_vendedor: this.getUsuarios.id , fecha: this.fechaActual }
				this.$http.post('pendientesxvend',payload).then( response =>{
					this.compromisos = response.body

					for(var i = 0; i < this.compromisos.length; i++) {
						events.push({	name: this.compromisos[i].nomcatego,
													start: this.compromisos[i].fecha + " " + this.compromisos[i].hora,
													end:   this.compromisos[i].fecha_fin + " " + this.compromisos[i].hora_fin,
													color: this.colors[this.rnd(0, this.colors.length - 1)],
													nomcli: this.compromisos[i].nomcli,
													nomvend: this.compromisos[i].nomvend,
													cumplimiento: this.compromisos[i].cumplimiento,
													nomuser: this.compromisos[i].nomuser,
													fecha: this.compromisos[i].fecha,
													hora: this.compromisos[i].hora,
													comentarios: this.compromisos[i].comentarios
												})
					}
					this.start = start
					this.end = end
					this.events = events

				}).catch(error =>{
					console.log('error', error)
				})
			},
      
      rnd (a, b) { return Math.floor((b - a + 1) * Math.random()) + a }, // GENRA UN NUMERO RANDOM
			
    },
  }
</script>

