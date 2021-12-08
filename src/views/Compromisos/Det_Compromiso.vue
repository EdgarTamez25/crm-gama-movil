<template>
	<v-main class="pa-0">
		<!-- //! SNACK BAR  -->
		<v-snackbar v-model="alerta.activo" multi-line vertical top right :color="alerta.color" > 
			<strong> {{alerta.texto}} </strong>
			<template v-slot:action="{ attrs }">
				<v-btn color="white" text @click="alerta.activo = false" v-bind="attrs"> Cerrar </v-btn>
			</template>
		</v-snackbar>
		<v-row >
			<!--//! TITILO DE LA VISTA -->
			<v-card-actions class="mx-3 pa-0"> 
				<v-btn color="gris" dark small fab @click="evualuaRetorno()">
					<!-- onClick="history.go(-1); return false;" -->
					<v-icon  >mdi-arrow-left-thick</v-icon>
				</v-btn>
				<v-card-title class="font-weight-bold subtitle-1">Detalle del Compromiso 
					<span class="mx-2 blue--text">{{ detalle.id }} </span>
				</v-card-title>	
			</v-card-actions>
			<!--//! DETALLE DEL PROSPECTO -->
		  <v-card-text  class="mt-2 py-0"> 
				<v-row justify="center" >
					<v-col cols="12" class="pa-1">
					  <v-card outlined>
						  <v-simple-table dense >
						    <template v-slot:default>
						      <tbody >
										<tr >
                      <td class="font-weight-black">Cliente</td>
											<td class="caption "> {{ detalle.nomcli }}</td>
                    </tr>
                    <tr>
                      <td class="font-weight-black">Catégoria</td>
						          <td class="caption">{{ detalle.nomcatego }}</td>
                    </tr>
										<tr>
                      <td class="font-weight-black">Hora</td>
											<td class="caption">
												{{ detalle.hora >='12:00'? detalle.hora +' '+'pm': detalle.hora+ ' '+'am' }}
											</td>
                    </tr>
										<tr>
                      <td class="font-weight-black">Comentarios</td>
											<td class="caption"> {{ detalle.obs }}</td>
                    </tr>
										<tr>
                      <td class="font-weight-black">Estatus</td>
											<td v-if="detalle.estatus === 0 " class="caption font-weight-black celeste--text"> PENDIENTE </td>
											<td v-if="detalle.estatus === 1 " class="caption font-weight-black orange--text"> SOLICITUD DE DESARROLLO</td>
											<td v-if="detalle.estatus === 2 " class="caption font-weight-black green--text"> PENDIENTE POR AUTORIZAR </td>
                    </tr>
						      </tbody>
						    </template>
						  </v-simple-table>

					  </v-card>
					</v-col>
				</v-row>
      </v-card-text>
			<!--//! TELEFONO 1 -->
			<!-- <v-col cols="6" align="center" v-if="!tSolicitudes "> 
				<v-btn  color="info" outlined block :disabled="detalle.tel1 === '' "><v-icon left>phone</v-icon>
						<a :href="`tel:${detalle.tel1}`" style="text-decoration:none;">{{ detalle.tel1? detalle.tel1: '00-00-00' }}</a>
					</v-btn>
			</v-col> -->
			 <!--//! TELEFONO 2 -->
			<!-- <v-col cols="6" align="center" v-if="!tSolicitudes">
				<v-btn  color="info" outlined block :disabled="detalle.tel2 === '' "><v-icon left>phone</v-icon>
					<a :href="`tel:${detalle.tel2}`" style="text-decoration:none;">{{ detalle.tel2? detalle.tel2: '00-00-00' }}</a>
				</v-btn>
			</v-col> -->
			<!--//! CONFIRMAR CITA  -->
			<v-col cols="12" align="center" v-if="!tSolicitudes && !Terminar && !SCotizacion"> 
				<v-btn color="red darken-4" :disabled="overlay" persistent :loading="overlay" block 
																		 dark  @click="confirmarModal = true" v-if="citaConfirmada === 0" >
					CONFIRMAR CITA
				</v-btn>
				<v-btn color="green" block dark  v-else > CITA CONFIRMADA </v-btn>
			</v-col>
			<!--//! REAGENDAR -->
			<v-col cols="12" v-if="!activaReagendar && !tSolicitudes && !Terminar && !SCotizacion" align="center"> 
				<v-btn color="morado" block dense dark @click="activaReagendar= true"> Reagendar </v-btn>
			</v-col>
			<!--//! FECHA DE COMPROMISO -->
			<v-col cols="6" v-if="activaReagendar"> 
				<v-dialog ref="fecha_compromiso" v-model="fechamodal" :return-value.sync="fecha" persistent width="290px">
					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="fecha" label="Fecha Inicio" append-icon="event" readonly v-on="on"
							outlined dense hide-details color="celeste"
						></v-text-field>
					</template>
					<v-date-picker v-model="fecha" locale="es-es" color="rosa"  scrollable>
						<v-spacer></v-spacer>
						<v-btn text small color="gris" @click="fechamodal = false">Cancelar</v-btn>
						<v-btn dark small color="rosa" @click="$refs.fecha_compromiso.save(fecha)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>
			<!--//! HORA DEL COMPROMISO -->
			<v-col cols="6" v-if="activaReagendar"> 
				<v-dialog ref="hora_compromiso" v-model="horamodal" :return-value.sync="hora" persistent width="290px" >

					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="hora" label="Hora Inicio" append-icon="access_time" readonly v-on="on"
							outlined dense hide-details color="celeste"
						></v-text-field>
					</template>

					<v-time-picker v-if="horamodal" locale="es-es" color="rosa" v-model="hora" full-width 	>
						<v-spacer></v-spacer>
						<v-btn small text color="gris" @click="horamodal = false">Cancel</v-btn>
						<v-btn small dark color="rosa" @click="$refs.hora_compromiso.save(hora)">OK</v-btn>
					</v-time-picker>
				</v-dialog>
			</v-col>
			<!--//! COMENARIO -->
			<v-col cols="12" v-if="activaReagendar" class="py-1"> 
				<v-textarea
					v-model ="obs" outlined label="Comentario" placeholder="Agregar un comentario ..."
					rows="2" hide-details dense color="celeste"
				></v-textarea>
			</v-col>
			<!-- //! CANCELAR REAGENDADO * AGENDADO  -->
			<v-col cols="12" v-if="activaReagendar" class="py-0"> 
				<v-card-actions>
					<v-btn dark color="gris" @click="activaReagendar=false"> Cancelar </v-btn>
					<v-spacer></v-spacer>
					<v-btn dark color="rosa" :disabled="overlay" persistent :loading="overlay" @click="validaFechas" > Reagendar </v-btn>
				</v-card-actions>
			</v-col>
		</v-row>

		<!-- //! OPCIONES DE ACCION **FINALIZAR**SOLICITAR-PEDIDO***SOLICITAR-COTIZACION -->
		<v-row justify="center" v-if="citaConfirmada ===1 && !Terminar && !tSolicitudes && !SCotizacion">
			<v-col cols="12" class="text-center" v-if="detalle.estatus === 3 && !modoResumen">
				<v-btn color="orange" block  dark outlined  
							 @click="modoResumen = true;modo = 2;consulta_productos_x_cotizar(detalle.id)" href="#fase"> 
					Ver resumen de solicitud
				</v-btn>
			</v-col>

			<v-col cols="6" class="text-center" v-if="!modoResumen">
				<v-btn color="rosa" large dark outlined style="height:100px; width:150px" @click="Terminar=true" href="#fase"> 
					Finalizar <br> compromiso
				</v-btn>
			</v-col>

			<v-col cols="6" class="text-center" v-if="detalle.estatus != 2 && detalle.estatus != 3 ">
				<v-btn color="negro" large dark outlined style="height:100px; width:150px" 
							 @click="SCotizacion = true; cotizar = true "
				> 
					Solicitar <br> Cotización 
				</v-btn>
			</v-col>
			<v-col cols="6" class="text-center" v-if="!modoResumen">
				<v-btn color="celeste" large dark outlined style="height:100px; width:150px" @click="tSolicitudes = true" href="#fase"> 
					Solicitar <br> pedido 
				</v-btn>
			</v-col>
		</v-row>
			
		<!-- //!CAPTURA DE RESULTADOS -->
		<v-row v-if="Terminar && !tSolicitudes && !SCotizacion"> 
			<v-col cols="12" align="center" class="font-weight-black "> RESULTADO DEL COMPROMISO </v-col>
			<v-col cols="12" md="6"> <!-- OBSERVACIONES -->
				<v-textarea
					v-model="obs_usuario" 
					label="Resultados" 
					hide-details filled rows="4" 
					placeholder="Escriba el resultado del compromiso..." 
					color="celeste"
				></v-textarea>
			</v-col>

			<v-col cols="12" align="right" class="mt-0" > <!-- BOTON PARA GUARDAR INFORMACION -->
				<v-card-actions>
					<v-btn 
						color="gris" dark outlined  
						@click="Terminar = false"
						class="subtitle-2"
					>Cancelar </v-btn> 
					<v-spacer></v-spacer> 
					<v-btn 
						color="celeste" dark   
						@click="validaInfo"
						class="subtitle-2"
						>Guardar Información
					</v-btn> 
				</v-card-actions>
			</v-col>
		</v-row>

		<!-- //!TABLA DE SOLICITUDES -->
		<v-row v-if="!Terminar && tSolicitudes && !SCotizacion">
			<v-col cols="12">
				<v-card-text class="font-weight-black my-0 pa-0 py-0 subtitle-1" align="center">SOLICITUD DE PEDIDO</v-card-text>
				<v-card-text class="caption">
					DEBES DE AGREGAR LA INFORMACIÓN NECESARIA PARA PODER DARLE SEGUIMIENTO A LA SOLICITUD EJEMPLO: <br> 
					<span class="font-weight-black">	1. NUMERO DE ORDEN DE COMPRA </span> <br>
					<span class="font-weight-black">  2. IDENTIFICADOR DE ENVIO DE CORREO </span> <br>
					<span class="font-weight-black">  3. URGENCIA DE LA SOLICITUD </span> <br>
				  <span class="font-weight-black">  etc... </span>
				</v-card-text>
				<!-- <v-btn color="orange" block dark @click="verDetalle(1)"> AGREGAR PRODUCTO </v-btn> -->
			</v-col>	
			<!-- <v-col cols="12">
				<v-card outlined>
					<v-card-text v-if="!getSolicitudes.length"> No se a registrado ningun producto </v-card-text>
					<v-simple-table v-else>
						<template v-slot:default>
							<thead>
								<tr>
									<th class="text-left"> Referencia </th>
									<th class="text-left"></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item,i) in getSolicitudes" :key="i" >
									<td class="font-weight-black">{{ item.referencia }}</td>
									<td align="right">
										<v-btn small color="success" class="mx-1"  @click="verDetalle(2,item)"> <v-icon>mdi-pencil</v-icon> </v-btn>
										<v-btn small color="error" class="mx-1" @click="EliminarProducto(item.id)"> <v-icon>mdi-delete</v-icon> </v-btn>
									</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col>  -->

			<v-col cols="12" class="py-1 my-0 pb-0">
				<v-textarea
					v-model="nota" 
					filled rows="3" 
					color="celeste"
					label="Nota de la solicitud"  
					placeholder="Agrega toda la información necesaria..."
        ></v-textarea>
			</v-col>

			<!-- //!BOTON PARA GUARDAR INFORMACION -->
			<v-col cols="12" align="right" class="py-0 my-0" > 
				<v-card-actions>
					<v-btn 
						color="gris" dark  outlined  
						@click="tSolicitudes = false"
						class="subtitle-2"
					>Cancelar  
					</v-btn> 
					<v-spacer></v-spacer>
					<v-btn 
						color="celeste" dark            
						@click="validaInfo"
						class="subtitle-2"
					> Guardar Información
					</v-btn> 
				</v-card-actions>
			</v-col>
		</v-row>

		<!-- //!SOLICITAR COTIZACION -->
		<v-row v-if="!Terminar && !tSolicitudes && SCotizacion" class="mt-5">
			<!--
			<v-col cols="12" >
				<v-select
						v-model="depto" :items="deptos" item-text="nombre" item-value="id" outlined color="celeste" 
						dense hide-details  label="Departamentos" return-object placeholder ="Departamentos"
				></v-select>
			</v-col>
			-->
			<v-col cols="12" class="" >
				<v-select
						v-model="tipo" :items="tipos" item-text="nombre" item-value="id" outlined color="pink" 
						dense hide-details label="Tipos de productos" return-object placeholder ="Tipo de producto"
				></v-select>
			</v-col>
		</v-row>

		<!-- //! PRODUCTO EXISTENTE  -->
		<v-row v-if="SCotizacion && tipo.id === 1" class="mt-5">
			<v-col cols="12" class="py-0 " >
				<v-autocomplete
					v-model="producto"
					:items="productos"
					outlined
					dense
					color="celeste"
					label="Productos"
					item-text="codigo"
					item-value="id"
					return-object
					:disabled="!productos.length || editarProdExis  ? true:false " 
					hide-details
				>
					<template v-slot:item="data">
						<template>
							<v-list-item-content>
								<v-list-item-title class="font-weight-black" v-html="data.item.codigo"></v-list-item-title>
								<v-list-item-subtitle >{{ data.item.nombre ? data.item.nombre: 'Producto sin nombre'}}</v-list-item-subtitle>
							</v-list-item-content>
						</template>
					</template>
				</v-autocomplete>
				<span class="font-weight-black overline red--text py-0 pa-1"
						  v-if="producto.id != null && !productos.length">No hay productos de este departamento.
				</span>
			</v-col>

			<v-col cols="12" class="py-0" >
				<v-radio-group v-model="obs_existente" mandatory >
					<v-radio
						label="Reajuste de precio"
						value="Reajuste de precio"
						color="red"
					></v-radio>
					<v-radio
						label="Cotizar producto"
						value="Cotizar producto"
						color="red"
					></v-radio>
				</v-radio-group>
			</v-col>

			<v-col cols="12" align="end" v-if="!editarProdExis">
				<v-btn color="green" block class="white--text" 
							 :disabled="producto.id== null? true: false"
							 @click="agrega_producto_existente()"
				><v-icon> mdi-plus</v-icon>
				</v-btn>
			</v-col>

			<v-col cols="6"  v-if="editarProdExis">
				<v-btn color="green" block class="white--text" 
							@click="actualiza_producto_exis()"
							v-if="editarProdExis"
				><v-icon> mdi-plus</v-icon></v-btn>
			</v-col>
			<v-col cols="6"  v-if="editarProdExis">
				<v-btn color="red" block 
				 class="white--text" 
							 @click="editarProdExis = false; limpiarCampos()"
				><v-icon> mdi-close</v-icon>
				</v-btn>
			</v-col>
		</v-row>

		<!-- //! PRODUCTO NUEVO  -->
		<v-row v-if="SCotizacion && tipo.id === 2">
			<v-col cols="12" class="py-0" >
				<v-radio-group v-model="obs_nuevo" mandatory  class="py-0"	>
					<v-radio
						label="Envíar información por correo"
						value="Envíar información por correo"
					></v-radio>
					<v-radio
						label="Envíar información por what´s app"
						value="Envíar información por what´s app"
					></v-radio>
				</v-radio-group>
			</v-col>
			<v-col cols="12"  align="end" v-if="!editarProdNuevo">
				<v-btn color="green" block class="white--text" 
							 @click="agrega_producto_nuevo()"
							 
				> <v-icon> mdi-plus</v-icon>
				</v-btn>
			</v-col>
			<v-col cols="6" v-if="editarProdNuevo">
				<v-btn color="green" block class="white--text" 
							@click="actualiza_producto_nuevo()"
							
				><v-icon> mdi-plus</v-icon></v-btn>
			</v-col>
			<v-col cols="6" v-if="editarProdNuevo">
				<v-btn color="red" block class="white--text " 
							 @click="editarProdNuevo = false; limpiarCampos()"
							
				><v-icon> mdi-close</v-icon>
				</v-btn>
			</v-col>
		</v-row>

		<!-- //! TABLA DE PRODUCTOS -->
		<v-row v-if="SCotizacion && productosxCotizar.length || modoResumen">
			<v-col cols="12" align="center" class="">
				<span class="font-weight-black subtitle-1"> SOLICITUD DE DESARROLLO </span>
			</v-col>
			<v-col cols="12">
				<v-card outlined>
					<v-card-text v-if="!productosxCotizar.length"> No se a registrado ningun producto </v-card-text>
					<v-simple-table v-else>
						<template v-slot:default>
							<thead>
								<tr>
									<th class="text-left"> Producto </th>
									<!-- <th class="text-left" v-if="modo === 2"> Depto </th>-->
									<th class="text-left"> </th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item,i) in productosxCotizar" :key="i" >
									<td class="font-weight-black caption">{{ item.codigo }}</td>
									<!--
									<td class="font-weight-black caption" v-if="item.id_depto === 1 && modo === 2"> Desarrollo </td>
									<td class="font-weight-black caption" v-if="item.id_depto === 2 && modo === 2"> Digital    </td>
									<td class="font-weight-black caption" v-if="item.id_depto === 3 && modo === 2"> Diseño     </td>
									-->

									<!-- SE MUESTRA SOLO CUANDO NO HAY NINGUNA COTIZACION REALIZADA -->
									<td align="right" v-if="modo === 1"> 
										<v-btn small color="success" class="mx-1"  
													 @click="ver_detalle_producto(item)"
										> <v-icon>mdi-pencil</v-icon> 
									  </v-btn>
										<v-btn small color="error" class="mx-1"  
													 @click="elimina_producto_x_cotizar(item)"
										> <v-icon>mdi-delete</v-icon> 
										</v-btn>
									</td>

									<!-- SI HAY UNA COTIZACION REALIZADA SE MUESTRAN ESTOS BOTONES -->
									<td align="right" v-if="modo === 2">
										<v-btn small color="success" class="mx-1"  
													 @click="ver_detalle_producto(item)"
													 v-if="item.id === null"
										> <v-icon>mdi-pencil</v-icon> 
									  </v-btn>

										<v-btn small color="error" class="mx-1"  
													 @click="elimina_producto_x_cotizar(item)"
													 v-if="item.id === null"
										> <v-icon>mdi-delete</v-icon> 
										</v-btn>
										
										<!-- SE USA PARA ELIMINAR PRODUCTOS EXISTENTES...... -->
										<v-btn small color="error" class="mx-1"  
													 @click="alertaCancelar = true; prodACancelar = item "
													 v-if="item.estatus === 1 && !item.id_encargado && item.id != null"
										> <v-icon>mdi-cancel</v-icon> 
										</v-btn>
										<v-btn x-small text color="rosa"   v-if="item.estatus === 1 && item.id_encargado">ATENDIENDO</v-btn>
										<v-btn x-small text color="orange darken-1" v-if="item.estatus === 2"> POR AUTORIZAR </v-btn>
										<v-btn x-small text color="green"  v-if="item.estatus === 3 || item.estatus === 5"> AUTORIZADO    </v-btn>
										<v-btn x-small text color="error"  v-if="item.estatus === 4"> CANCELADO     </v-btn>
									</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col> 
			<!-- //!BOTON PARA GUARDAR INFORMACION -->
			<v-col cols="12" align="right" class="py-0 my-0" > 
				<v-card-actions>
					<v-btn color="gris" dark outlined 
								 @click="SCotizacion = false"
								 v-if="modo===1"
								 class="subtitle-2"
					>Cancelar 
					</v-btn> <v-spacer></v-spacer> 

					<!-- ESTE BOTON ES EL DE LA SOLICITUD DE DESARROLLO -->
					<v-btn 
						color="celeste" dark 
						@click="validaInfo()" 
						v-if="!modoResumen" 
						class="subtitle-2"
					> 
						{{ modo === 1 ? 'Guardar Información': 'Actualizar Información ' }}
					</v-btn> 

					<v-btn 
						color="error" dark 
						@click="modo=1;modoResumen=false; vaciar_prod_x_cot()" 
						v-if="modoResumen"
						class="subtitle-2"
					> 
						Esconder resumen 
					</v-btn>
				</v-card-actions>
			</v-col>

		</v-row>


		<!-- // !ESTE ES EL BUENO ECHALE GANAS PARA ENTENDERLE TE QUIERO MUCHO -->
		<v-dialog v-model="solicitarModal" persistent max-width="400">
			<v-card class="pa-4 ">
        <v-card-text class="font-weight-black my-1 " align="center">SOLICITUD DE PEDIDO</v-card-text>
				<!-- //! SELECCION DEL DEPARTAMENTO  -->
				<v-select
						v-model="depto" :items="deptos" item-text="nombre" item-value="id" outlined color="celeste" v-if="modoVista === 1"
						dense hide-details  label="Departamentos" return-object placeholder ="Departamentos"
				></v-select> 
					<v-select
						v-model="depto" :items="deptos" item-text="nombre" item-value="id" outlined color="celeste" v-else 
						dense hide-details  label="Departamentos" return-object placeholder ="Departamentos" disabled 
				></v-select> 
				
				<!-- //! FORMULARIOS  -->
				<flexografia 
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===1"
				/>
			
				<digital     
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===3"
				/>
			</v-card>
		</v-dialog>
		<!-- //!PROCESO PARA CONFIRMACION  -->
		<v-dialog v-model="confirmarModal" persistent max-width="400" > 
			<v-card>
				<v-col cols="12"  class="pa-4">
					<strong >LA CITA SE CONFIRMARA, ¿ESTA SEGURO DE SEGUIR ?</strong>
				</v-col>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="gris" text small @click="confirmarModal = false">Cancelar</v-btn>
					<v-btn color="rosa" dark small @click="confirmarCita()">Seguir </v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog> 
		<!--//!PROCESO PARA TERMINAR COMPROMISO  -->
		<v-dialog v-model="terminarCompromiso" persistent max-width="400" > 
			<v-card>
				<v-col cols="12" class="pa-4">
					<strong v-if="cotizar && modo===1">SE PROCEDERA A REALIZAR LA SOLICITUD DE COTIZACIÓN </strong> 
					<strong v-if="cotizar && modo===2">SE PROCEDERA A ACTUALIZAR LA SOLICITUD DE COTIZACIÓN </strong> 
					<strong v-if="Terminar">EL COMPROMISO SE FINALIZARA </strong> 
					<strong v-if="tSolicitudes">LA SOLICITUD DE PEDIDO SE PROCESARA </strong> 
					<br> ¿Esta seguro de continuar? 
				</v-col>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="gris" text small 
								 @click="terminarCompromiso = false; 
								 			   tSolicitudes =false; 
												 Terminar     =false;
												 "
					> Cancelar
					</v-btn>
					<v-btn color="rosa" dark small @click="EnviarSolicitud()"         v-if="tSolicitudes">Continuar </v-btn>
					<v-btn color="rosa" dark small @click="EnviarResultado()"         v-if="Terminar">    Continuar</v-btn>
					<v-btn color="rosa" dark small @click="EnviarSolCotizacion()"     v-if="cotizar && modo===1"> Continuar</v-btn>
					<v-btn color="rosa" dark small @click="ActualizarSolCotizacion()" v-if="cotizar && modo===2"> Continuar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog> 
		<!-- //!ALERTA DE CONTENIDO -->
		<v-dialog v-model="alertaDeContenido" persistent max-width="500">
			<v-card class="pa-1" >
				<v-card-title class="subtitle-1 font-weight-black"> HAY UNA SOLICITUD PENDIENTE  </v-card-title>
				<v-card-subtitle class="subtitle-2 font-weight-black">¿ESTA SEGURO DE QUERER REGRESAR?</v-card-subtitle>
				<v-divider class="my-0 py-3 " ></v-divider>
				<v-card-subtitle align="center" class="red--text font-weight-bold "> SE PERDERA TODA LA INFORMACIÓN </v-card-subtitle>
				<v-divider class="my-0 py-2 "></v-divider>
				<v-card-actions>
					<v-spacer/>
					<v-btn dark outlined color="gris" @click="alertaDeContenido = false">Cancelar</v-btn>
					<v-btn dark color="rosa" @click="volverAtras()" >Confirmar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		<!-- //!ALERTA DE CONTENIDO A ELIMINAR -->
		<v-dialog v-model="alertaCancelar" persistent max-width="500">
			<v-card class="" >
				<v-card-title class="subtitle-1 font-weight-black"> EL PRODUCTO SE CANCELARA  </v-card-title>
				<v-card-subtitle class="subtitle-2 font-weight-black">¿ ESTA SEGURO DE QUERER CONTINUAR ?</v-card-subtitle>
				<v-divider class="my-0 py-3" ></v-divider>
				<v-card-subtitle align="center" class="red--text font-weight-bold "> SE CANCELARA DE FORMA DEFINITIVA </v-card-subtitle>
				<v-divider class="my-0 py-2 " ></v-divider>
				<v-card-actions>
					<v-spacer/>
					<v-btn dark outlined color="gris" @click="alertaCancelar = false">NO</v-btn>
					<v-btn dark color="error" @click="eliminar_producto_a_cotizar()" >Si, CONTINUAR</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>


		<overlay v-if="overlay"/>
		<div id="fase"></div>
	</v-main>
</template>

<script>
	var moment = require('moment'); moment.locale('es') ;	
	import {mapGetters, mapActions} from 'vuex'
	import metodos     from '@/mixins/metodos.js'
	import overlay     from '@/components/overlay.vue'
	import flexografia from '@/views/Formularios/flexografia.vue'
	import digital     from '@/views/Formularios/digital.vue'

	export default {
		mixins:[metodos],
		components: {
			overlay,
			flexografia,
			digital,
		},
		data(){
			return{
				fase_venta  			: '',
				obs_usuario 			: '',
				obs         			: '',
				Terminar    			: false,
				citaConfirmada    : null,
				terminarCompromiso: false,
				nota 						  : '',
				// MANEJO DE FECHAS
				fecha						  : new Date().toISOString().substr(0, 10),
				fechamodal 			  : false,
				fecha_compromiso  : false,
				hora 					    : null,
        horamodal			    : false,
				hora_compromiso   : false,
				activaReagendar   : false, // MODAL DE REAGENDACION
				confirmarModal    : false, // MODAL DE CONFIRMACION
				alertaDeContenido : false, // MODAL DE ALERTA DE CONTENIDO 
				// FORMLARIOS
				tSolicitudes      : false,
				SCotizacion       : false,
				solicitarModal    : false, // !ABRE EL MODAL PARA FORMULARIOS
				activaFormulario  : 1,
				parametros        : '',
				modoVista         : 1,
				depto             : { id:null, nombre:''},
				deptos            : [],
				tipos	  					:[{id:1, nombre:'Producto Existente'}, { id:2, nombre:'Nuevo Producto' }],
				tipo							:{id:null, nombre: ''},
				// ALERTAS
				alerta            : { activo: false, texto:'', color:''},
				Correcto          : false,
				textCorrecto      : '',

				overlay           : false,

				modo              : 1,
        producto          : { id     : null, nombre:''},
        productos         : [],
				obs_existente     : null,
				obs_nuevo         : null,
				comentario        : '',
				count             : 1,     // CUENTA LOS ITEMS DE PROD NUEVO
				producto_a_edit   : {},    // PRODUCTO POR COTIZAR
				editarProdNuevo   : false, // PRODUCTO POR COTIZAR
				editarProdExis    : false, // PRODUCTO POR COTIZAR
				cotizar           : false, // PRODUCTO POR COTIZAR
				prodAEdit         : {},    // PRODUCTO POR COTIZAR

				alertaCancelar    : false, // PROCESO PARA ELIMINAR
				prodACancelar       : null,  // PROCESO PARA ELIMINAR
				modoResumen       : false , // HABILITA LA TABLA PARA VISUALIZACION EN MODO 3

			}
		},
		created(){
			// SI NO OBTENGO LA INFORMACION RETORNO VISTA.
			if(!this.$route.params.detalle){  
				window.history.go(-1); 
			} 
			// PARAMETROS QUE RECIBO DE LA VISTA 
			this.detalle = this.$route.params.detalle; 								 
			// EVALUAR SI ESTOY EN ESTATUS DE COTIZACION 
			if(this.detalle.estatus === 1 ){ 
				this.init_modo_2()  
			}     
			// CORRER INIT
			this.init();
				
		},
		watch:{ 
			depto(){
				// this.activaFormulario = this.depto.id
			}
		},
		computed:{ 
			...mapGetters('Solicitudes'  ,['getSolicitudes']), 
			...mapGetters('Compromisos'  ,['productosxCotizar']), 
			...mapGetters('Usuarios'     ,['getUsuarios']),
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos',
																		 'agregar_producto_a_cotizar',
																		 'elimina_producto_a_cotizar',
																		 'actualiza_producto_a_cotizar',
																		 'vaciar_prod_x_cot',
																		 'consulta_productos_x_cotizar'
																		 ]),// IMPORTANDO USO DE VUEX
		  ...mapActions('Solicitudes'  ,['eliminaProducto','vaciaSolicitudes']),
			
			init(){
				this.vaciar_prod_x_cot();
				this.vaciaSolicitudes(); 
				this.fecha          = this.traerFechaActual();    // ASIGNAR FECHA ACTUAL
				this.hora           = this.traerHoraActual(); 	  // ASIGNAR HORA ACTUAL
				this.citaConfirmada = this.detalle.confirma_cita; // CONFIRMACION DE CITA
				this.consultaDeptos();
				this.consultar_productos();
				
			},
			init_modo_2(){
				this.SCotizacion = true;
				this.modo = 2; // EDITAR PRODUCTOS A COTIZAR 
				this.cotizar = true;
				this.vaciar_prod_x_cot();
				this.consulta_productos_x_cotizar(this.detalle.id);
			},

			consultar_productos(){
				this.productos = []; 
				this.productos = { 
					id:null,
					nombre:''
				}; 
				const payload = {
					id_cliente : this.detalle.id_cliente, 
					// dx : this.depto.id
				}
				this.$http.post('obtener.prodxcli.depto',payload).then( res =>{
					this.productos = res.body;
				}).catch( error =>{ 
					console.log('err prod', error)
				})
			},

			verDetalle(modo,item){
				// console.log('verdetalle otro', this.deptos[item.dx].nombre)
				if(modo === 2){  this.depto = { id: item.dx } };
					this.solicitarModal = true;
					this.parametros 		= item;
					this.modoVista      = modo; 
			},

			EliminarProducto(id){
        this.eliminaProducto(id).then( res =>{
          if(res){ 
						this.alerta =  { activo: true, texto:'El producto se elimino correctamente.', color:'green'};
					}
        })
      },

			confirmarCita(){
				this.confirmarModal = false; 	
				this.overlay = true ; 
				
				const payload = new Object({
					id: this.detalle.id,
					confirma_cita: 1
				})
				 
				this.$http.post('confirmarcita', payload).then((response)=>{
					this.alerta =  { activo: true, texto:response.bodyText, color:'green'};
					this.citaConfirmada = 1;
				}).catch((error)=>{
					this.alerta =  { activo: true, texto:error.bodyText, color:'error'};
					console.log('error', error)
				}).finally(()=> this.overlay= false)
			},

			validaFechas(){
				var fi = this.fecha + " " + this.hora; 	var ff = this.traerFechaActual() + " " + this.traerHoraActual();
				if(!this.fecha)			{ this.snackbar=true;this.text="No puedes omitir la FECHA INICIAL"	; return}
				if(!this.hora)			{ this.snackbar=true;this.text="No puedes omitir la HORA INICIAL"	; return}
				if(ff > fi){ this.snackbar=true;; this.text="La FECHA Y HORA del compromiso no puede ser antes de la actual."; return};
				this.reagendar()
			},

			reagendar(){
				this.overlay = true ; 
				const payload = new Object({
					id   : this.detalle.id , 
					fecha: this.fecha, 
					hora : this.hora, 
					obs  : this.obs
				});

				this.$http.post('reagendar', payload).then((response)=>{
					this.alerta =  { activo: true, texto: response.bodyText , color:'green'};
					this.consultaCompromisos() //ACTUALIZAR CONSULTA DE CLIENTES
					this.regresar_a_compromisos();
				}).catch((error)=>{
					console.log('error', error)
					this.alerta =  { activo: true, texto:error.bodyText, color:'error'};
				}).finally(()=> {
					this.overlay = false; 
				})
			},
	
			validaInfo(){
				if(this.tSolicitudes){
					// if(!this.getSolicitudes.length){
					// 	this.snackbar=true;	this.text="DEBES AGREGAR AL MENOS 1 PRODUCTO"; return;
					// }
				}else if(this.Terminar){
					if(!this.obs_usuario){ 
						this.alerta =  { activo: true, texto:"DEBES AGREGAR EL RESULTADO DEL COMPROMISO.", color:'error'};
					 	return;
					}
				}else if(this.SCotizacion){
					if(!this.productosxCotizar.length){
						this.alerta = { activo: true, texto:"DEBES AGREGAR AL MENOS UN PRODUCTO PARA COTIZAR.", color:'error'};
					 	return;
					}
				}

				this.terminarCompromiso = true;
			},

			EnviarResultado(){
				this.overlay  = true;
				this.terminarCompromiso = false; 
				
				const payload = new Object({
					id 					 : this.detalle.id,
					fecha_cierre : this.traerFechaActual(),
					hora_cierre  : this.traerHoraActual(),
					obs_usuario  : this.obs_usuario,
					cumplimiento : 1 
				});
				
				this.$http.post('terminar.compromiso', payload).then(response =>{
					this.alerta =  { activo: true, texto: response.bodyText , color:'green'};
					this.consultaCompromisos();
					this.regresar_a_compromisos();
				}).catch(error =>{
					this.alerta =  { activo: true, texto: error.bodyText, color:'error'};
					console.log('error', error)
				}).finally(()=> this.overlay = false)
			},

			EnviarSolicitud(){
				let detalle  = this.validaProductos();
				this.overlay = true; 
				this.terminarCompromiso = false;

				const payload = new Object({
					id_compromiso : this.detalle.id,
					fecha         : this.traerFechaActual(),
					hora          : this.traerHoraActual(),
					id_usuario    : this.detalle.id_vendedor,
					id_cliente    : this.detalle.id_cliente,
					nota	        : this.nota,
					detalle       : detalle,
					id_creador    : this.detalle.id_vendedor,
				});

				this.$http.post('add.Solicitud', payload).then(response =>{
					this.alerta =  { activo: true, texto: response.bodyText , color:'green'};
					this.consultaCompromisos() 
					this.regresar_a_compromisos();
				}).catch(error =>{
					this.alerta =  { activo: true, texto: error.bodyText, color:'error'};
					console.log('error', error)
				}).finally(()=> this.overlay = false)
			},

			validaProductos(){
				let Temporal = [];
				for(let i=0;i<this.getSolicitudes.length;i++){
					if(this.getSolicitudes[i].tproducto === 2){
						Temporal.push(this.getSolicitudes[i].xmodificar) 
					} else{
						Temporal.push(this.getSolicitudes[i])
					}
				}
				return Temporal;
			},

			volverAtras(){
				this.alertaDeContenido = false;
				this.vaciaSolicitudes();
				this.vaciar_prod_x_cot();
				window.history.go(-2);
			},

			regresar_a_compromisos(){
				var me = this
				setTimeout(()=>{ me.$router.push({name: 'compromisos' })}, 1000);
			},


			agrega_producto_existente(){
				const prodexit = new Object({
					'id'			   : null,
					'id_producto': this.producto.id,
					'codigo'     : this.producto.codigo,
					'id_depto'   : this.depto.id,
					'descripcion': this.obs_existente,
					'tipo'       : this.tipo.id // PRODUCTO EXISTENTE
				});

				// console.log('prodexit agregar', prodexit)

				this.agregar_producto_a_cotizar(prodexit).then( () =>{
					this.alerta =  { activo: true, texto: 'Se agrego correctamente', color:'green'};
					this.limpiarCampos()
				})
			},

			agrega_producto_nuevo(){
				// if(!this.depto.id){ 
				// 	this.alerta = { activo: true, texto:'Debes seleccionar un departamento', color:'error'};
				// 	return;
				// }
				const prodnuevo = new Object({
					'id'			   : null,
					'id_producto': 'PN'+this.count ,
					'codigo'     : 'PN-'+ this.detalle.id + '-' + this.count + '-' + parseInt(Math.random()*300),
					'id_depto'   : this.depto.id,
					'descripcion': this.obs_nuevo,
					'tipo'       : this.tipo.id  // PRODUCTO NUEVO
				});	

				this.agregar_producto_a_cotizar(prodnuevo).then( () =>{
					this.count++;
					this.alerta = { activo: true, texto:'Se agrego correctamente', color:'green'};
					this.limpiarCampos()
				})
			},
			
			elimina_producto_x_cotizar(data){
				this.elimina_producto_a_cotizar(data.id_producto).then( () =>{
					this.alerta = { activo: true, texto:'El producto se elimino correctamente.', color:'green'};
        })
			},

			eliminar_producto_a_cotizar(){
				this.overlay = true; this.alertaCancelar = false;
				const payload = new Object({ 
					id           : this.prodACancelar.id, 
					fecha				 : this.traerFechaActual(),
					id_compromiso: this.prodACancelar.id_compromiso
				});
				this.$http.post('cancelar.prod.a.cotizar', payload ).then( response =>{
					this.alerta =  { activo: true, texto: response.bodyText , color:'green'};
					this.init_modo_2();
				}).catch( error =>{
					this.alerta = { activo: true, texto: error.bodyText , color:'error'};
				}).finally(()=> this.overlay = false)
			},

			ver_detalle_producto(data){
				if(data.tipo === 1 ){
					this.tipo               = { id: data.tipo };
					this.producto_a_edit    = data;
					this.obs_existente      = data.descripcion;
			   	this.depto              = { id:data.id_depto };
					this.editarProdExis     = true;
					this.producto           = { id: data.id_producto , codigo: data.codigo };
				}else{
					this.tipo               = { id: data.tipo };
					this.producto_a_edit    = data;
					this.obs_nuevo          = data.descripcion;
					this.depto              = { id: data.id_depto    };
					this.editarProdNuevo    = true;
				}

			},

			actualiza_producto_nuevo(){
				const prodnuevo = new Object({
					'id'         : this.producto_a_edit.id,
					'id_producto': this.producto_a_edit.id_producto ,
					'codigo'     : this.producto_a_edit.codigo,
					'id_depto'   : this.depto.id,
					'descripcion': this.obs_nuevo,
					'comentario' : this.comentario,
					'tipo'       : this.tipo.id  // PRODUCTO NUEVO
				});	
				// console.log('prodnuevo', prodnuevo)

				this.actualiza_producto_a_cotizar(prodnuevo).then(() =>{
					this.alerta = { activo: true, texto:'Se actualizo correctamente', color:'green'};
					this.editarProdNuevo = false; 
					this.limpiarCampos();
				})
			},

			actualiza_producto_exis(){
				const prodexit = new Object({
					'id'         : this.producto_a_edit.id,
					'id_producto': this.producto.id,
					'codigo'     : this.producto.codigo,
					'id_depto'   : this.depto.id,
					'descripcion': this.obs_existente,
					'tipo'       : this.tipo.id  // PRODUCTO EXISTENTE
				});

				// console.log('prodexit actualiza', prodexit)
				this.actualiza_producto_a_cotizar(prodexit).then( () =>{
					this.alerta = { activo: true, texto:'Se actualizo correctamente', color:'green'};
					this.editarProdExis = false; 
					this.limpiarCampos()
				})
			},

			EnviarSolCotizacion(){
				console.log('SI ENTRE AQUI ');
				
				this.overlay = true; 
				this.terminarCompromiso = false;
				const payload = {
					'id_cliente'   : this.detalle.id_cliente, 
					'fecha'				 : this.traerFechaActual(),
					'hora'				 : this.traerHoraActual(),
					'id_creador'   : this.getUsuarios.id,
					'id_compromiso': this.detalle.id,
					'productos'    : this.productosxCotizar, // ARREGLO DE PRODUCTOS
					'estatus'      : 1
				}

				console.log('payload de enviar cot', payload);

				this.$http.post('genera.solicitud.cotizacion', payload).then(response =>{
					this.alerta =  { activo: true, texto: response.bodyText , color:'green'};
					this.vaciar_prod_x_cot(); 
					this.consultaCompromisos();
					this.regresar_a_compromisos();
				}).catch(error =>{
					this.alerta = { activo: true, texto: error.bodyText , color:'error'};
				}).finally(()=> this.overlay = false);

			},

			ActualizarSolCotizacion(){
				this.overlay = true; this.terminarCompromiso = false;
				const payload = new Object({
					'id_cliente'   : this.detalle.id_cliente, 
					'fecha'				 : this.traerFechaActual(),
					'hora'				 : this.traerHoraActual(),
					'id_creador'   : this.getUsuarios.id,
					'id_compromiso': this.detalle.id,
					'productos'    : this.productosxCotizar,
					'estatus'      : 1
				})
				// console.log('payload', payload)
				this.$http.post('actualiza.solicitud.cotizacion', payload).then(response =>{
					this.alerta =  { activo: true, texto: response.bodyText , color:'green'};
					this.init_modo_2();
				}).catch(error =>{
					this.alerta = { activo: true, texto: error.bodyText , color:'green'};
				}).finally(()=> this.overlay = false)
			},

			limpiarCampos(){
				this.producto = { id: null, nombre:'' },
				this.depto    = { id: null, nombre:'' };
				this.tipo     = { id: null, nombre:'' }
				this.obs_usuario  = '';
				this.obs_nuevo    = '';
				this.comentario = ''
			}

		}
	}
</script>