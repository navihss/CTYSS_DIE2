var miExpReg = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{1,50}$/;
var miExpReg_Nombre = /^[a-zA-Z áéíóúñüÁÉÍÓÚÑÜ\.\']{1,50}$/;
var miExpReg_Nombre_Sinodal = /^[a-zA-Z áéíóúñüÁÉÍÓÚÑÜ\.\']{1,100}$/;
var miExpReg_Contrasena = /^[a-zA-Z0-9]{1,15}$/;
var miExpReg_Fecha = /^([0-9]{2}\/[0-9]{2}\/[0-9]{4})$/;
var miExpReg_Mail =
    /^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/;
var miExpReg_NoCta = /^[0-9]{1,10}$/;
var miExpReg_Clave = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9]{1,18}$/;

var miExpReg_CP = /^[0-9]{5}$/;
var miExpReg_Anio = /^[0-9]{4}$/;
var miExpReg_Semestre = /^[1-2]{1}$/;
var miExpReg_Telefono_Letras =
    /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\:\+\(\)\-\,\;]{1,20}$/;
var miExpReg_Extension = /^[0-9]{1,5}$/;
var miExpReg_Telefono_Celular = /^[0-9]{10}$/;
var miExpReg_Direccion = /^[a-zA-Z áéíóúüñÁÉÍÓÚÑÜ0-9\#\.\'\-]{1,100}$/;

var miExpReg_Percepcion = /^[0-9]{1,7}(\.[0-9]{1,2})?$/;
var miExpReg_EnteroSinSigno = /^[0-9]{1,3}$/;
var miExpReg_Porcentaje = /^[0-9]{1,3}(\.[0-9]{1,2})?$/;
var miExpReg_Promedio = /^[0-9]{1,2}(\.[0-9]{1,2})?$/;
var miExpReg_Clave_Programa = /^[0-9]{4}\-[0-9]{1,5}\/[0-9]{1,5}\-[0-9]{1,5}$/;
var miExpReg_Lentras_Numeros = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9]{1,50}$/;

var miExpReg_Nota_Aceptacion =
    /^[a-zA-Z áéíóúñüÁÉÍÓÚÑÜ0-9\.\,\;\:\?\¿\(\)\-\_\#\+\n\']{0,500}$/;
var miExpReg_Nota_Rechazo =
    /^[a-zA-Z áéíóúñüÁÉÍÓÚÑÜ0-9\.\,\;\:\?\¿\(\)\-\_\#\+\n\']{1,500}$/;

var miExpReg_Horas_Realizadas = /^[0-9]{1,3}$/;

var miExpReg_Servicio_Social = /^[0-9]{6}(\-[0-9]{3})$/;

var miExpReg_Buscar = /^[a-zA-Z0-9 áéíóúñüÁÉÍÓÚÑÜ\.\']{0,50}$/;

var miExpReg_RFC = /^[a-zA-Z0-9]{1,13}$/;
var miExpReg_CURP = /^[a-zA-Z0-9]{1,18}$/;

var miExpReg_Desc_Programa =
    /^[a-zA-Z áéíóúñüÁÉÍÓÚÑÜ0-9\.\,\;\:\?\¿\(\)\-\_\#\n\']{1,300}$/;
var miExpReg_Subdireccion =
    /^[a-zA-Z áéíóúñüÁÉÍÓÚÑÜ0-9\.\,\;\:\?\¿\(\)\-\_\#\n\']{1,200}$/;
var miExpReg_Responsable = /^[a-zA-Z áéíóúñüÁÉÍÓÚÑÜ\.\']{1,100}$/;
var miExpReg_Telefono_SS = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\:\+\(\)\-\,\;]{0,100}$/;
var miExpReg_Num_Ext_SS = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\-]{1,50}$/;
var miExpReg_Num_Int_SS = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\-]{0,50}$/;
