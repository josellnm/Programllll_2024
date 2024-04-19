<template>
    <div class="row">
            <div class="col col-md-6">
                <div class="card text-bg-dark">
                    <div class="card-header">REGISTRO DE USUARIOS</div>
                    <div class="catd-body">
                        <div class="row p-1">
                            <div class="col col-md-2">NOMBRE</div>
                            <div class="col col-md-3">
                                <input v-model="registro.nombre" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col col-md-2">APELLIDO</div>
                            <div class="col col-md-5">
                                <input v-model="registro.apellido" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col col-md-2">EDAD</div>
                            <div class="col col-md-5">
                                <input v-model="registro.edad" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col col-md-2">TELEFONO</div>
                            <div class="col col-md-5">
                                <input v-model="registro.telefono" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col col-md-2">CORREO</div>
                            <div class="col col-md-5">
                                <input v-model="registro.correo" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col col-md-2">CONTRASEÑA</div>
                            <div class="col col-md-5">
                                <input v-model="registro.password" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row p-1">
                            <div class="col">
                                <button @click.prevent.default="guardarRegistro" class="btn btn-success">GUARDAR</button>
                                <button @click.prevent.default="nuevoRegistro" class="btn btn-warning">NUEVO</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col col-md-6">
                <div class="card text-bg-dark">
                    <div class="card-header">LISTADO DE REGISTROS</div>
                    <div class="card-body">
                        <form id="frmRegistro">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>BUSCAR</th>
                                        <th colspan="5">
                                            <input placeholder="nombre, apellido, edad, telefono, correo" type="search" v-model="valor" @keyup="buscarRegistro" class="form-control">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NOMBRE</th>
                                        <th>APELLIDO</th>
                                        <th>EDAD</th>
                                        <th>TELEFONO</th>
                                        <th>CORREO</th>
                                        <th>CONTRASEÑA</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr @click="modificarRegistro(registro)" v-for="registro in registros" :key="registro.idRegistro">
                                        <td>{{registro.nombre}}</td>
                                        <td>{{registro.apellido}}</td>
                                        <td>{{registro.edad}}</td>
                                        <td>{{registro.telefono}}</td>
                                        <td>{{registro.correo}}</td>
                                        <td>{{registro.password}}</td>
                                        <td><button @click.prevent.default="eliminarRegistro(registro.idRegistro)" class="btn btn-danger">del</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</template>
<script>
    import axios from 'axios';
    import alertify from 'alertifyjs';
    export default {
        data() {
            return {
                valor:'',
                registros:[],
                accion:'nuevo',
                registro:{
                    idRegistro: new Date().getTime(),
                    nombre:'',
                    apellido:'',
                    edad:'',
                    telefono:'',
                    correo:'',
                    password:'',
                }
            }
        },
        methods:{
            buscarRegistro(e){
                this.listar();
            },
            
            async eliminarRegistro(idRegistro){
                if( confirm(`Esta seguro de elimina el registro?`) ){
                    this.accion='eliminar';
                    await db.registros.where("idRegistro").equals(idRegistro).delete();
                    axios({
                        url: 'registros',
                        method: 'DELETE',
                        data: {idRegistro}
                    }).then(resp=>{
                        if( resp.data.msg==='ok' ){
                            alertify.success('registro eliminado con exito');
                        }else{
                            alertify.error(`Error: ${resp.data.msg}`);
                        }
                    }).catch(err=>{
                        alertify.error(`Error: ${err}`);
                    })
                    this.nuevoRegistro();
                    this.listar();
                }
            },
            modificarRegistro(registro){
                this.accion = 'modificar';
                this.registro = registro;
            },
            async guardarRegistro(){
                
                //almacenamiento del objeto registros en indexedDB
                await db.registros.bulkPut([{...this.registro}]);
                let method = 'POST';
                if( this.accion === 'modificar' ){
                    method = 'PUT';
                }
                axios({
                    url: 'registros',
                    method,
                    data: this.registro
                }).then(resp=>{
                    if( resp.data.msg==='ok' ){
                        alertify.success('registro guardado con exito');
                    }else{
                        alertify.error(`Error: ${resp.data.msg}`);
                    }
                }).catch(err=>{
                    alertify.error(`Error: ${err}`);
                });
                this.nuevoRegistro();
                this.listar();
            },
            nuevoRegistro(){
                this.accion = 'nuevo';
                this.registro = {
                    idRegistro: new Date().getTime(),
                    nombre:'',
                    apellido:'',
                    edad:'',
                    telefono:'',
                    correo:'',
                    password:'',
                }
            },
            async listar(){
                let collections = db.registros.orderBy('nombre')
                .filter(registro=>registro.nombre.includes(this.valor) ||
                    registro.apellido.toLowerCase().includes(this.valor.toLowerCase()) ||
                    registro.edad.toLowerCase().includes(this.valor.toLowerCase())||
                    registro.telefono.toLowerCase().includes(this.valor.toLowerCase())||
                    registro.correo.toLowerCase().includes(this.valor.toLowerCase())||
                    registro.password.toLowerCase().includes(this.valor.toLowerCase()));
                this.registros = await collections.toArray();
                if( this.registros.length<=0 ){
                    let respuesta = await fetch('registros'),
                        data = await respuesta.json();
                    this.registros = data;
                    db.registros.bulkPut(data);
                }
            }
        }
    }
</script>