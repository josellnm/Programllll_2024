import './bootstrap';
import { createApp } from 'vue';
import Dexie from 'dexie';
import registros from './components/RegistroComponent.vue';
window.db = '';

const app = createApp({
    components:{
        
        registros,
    },
    data(){
        return{
            forms:{
                
                registro:{mostrar:false},
            }
        }
    },
    methods:{
        abrirFormulario(form){
            this.forms[form].mostrar = !this.forms[form].mostrar;
            this.$refs[form].listar();
        },
        funcdb(){
            db = new Dexie("db_sistema");
            db.version(1).stores({
                
                registros: 'idRegistro, nombre, apellido, edad, telefono, correo, password',
            });
        }
    },
    created(){
        this.funcdb();
    }
});
app.mount('#app');
