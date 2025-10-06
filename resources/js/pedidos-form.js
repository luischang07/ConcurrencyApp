/**
 * Formulario de creación de pedidos
 * Maneja la lógica de Alpine.js para el formulario de pedidos de medicamentos
 */

window.pedidoForm = function (cadenas = [], medicamentos = []) {
  return {
    // Datos del formulario
    cadenas: cadenas,
    sucursales: [],
    cadena: document.querySelector('input[name="cadena_old"]')?.value || '',
    sucursal: document.querySelector('input[name="sucursal_old"]')?.value || '',

    // Medicamentos
    medicamentoId: null,
    medicamentoNombre: '',
    medicamentoPrecio: 0,
    cantidad: document.querySelector('input[name="cantidad_temp_old"]')?.value || '',
    medicamentosItems: JSON.parse(document.querySelector('input[name="medicamentos_old"]')?.value || '[]'),

    // Búsqueda
    busqueda: document.querySelector('input[name="medicamento_busqueda_old"]')?.value || '',
    mostrarSugerencias: false,
    medicamentosDisponibles: medicamentos,

    // Alertas personalizadas
    alertas: [],
    alertaIdCounter: 0,

    /**
     * Inicialización del componente
     */
    async init() {
      console.log('Inicializando formulario de pedidos...');

      // Si hay cadena preseleccionada, cargar sucursales
      if (this.cadena) {
        await this.onChangeCadena();
        // Después de cargar sucursales, establecer el valor de sucursal
        this.$nextTick(() => {
          if (this.sucursal) {
            // Forzar la actualización del select
            const sucursalSelect = document.getElementById('sucursal');
            if (sucursalSelect) {
              sucursalSelect.value = this.sucursal;
            }
          }
        });
      }

      // Si hay errores, mantener los campos llenos
      const hasErrors = document.querySelector('.bg-red-100') !== null;
      if (hasErrors && this.busqueda) {
        this.medicamentoNombre = this.busqueda;
      }

      // Si hay sucursal preseleccionada, cargar medicamentos con stock
      if (this.sucursal) {
        await this.cargarMedicamentosConStock();
      }

      // Si hay sucursal preseleccionada, cargar medicamentos con stock
      if (this.sucursal) {
        await this.cargarMedicamentosConStock();
      }
    },

    /**
     * Computed: Sugerencias filtradas para el autocompletado
     */
    get sugerenciasFiltradas() {
      if (this.busqueda.length < 2) return [];
      const q = this.busqueda.toLowerCase();
      return this.medicamentosDisponibles.filter(m =>
        (m.nombre_comercial || m).toLowerCase().includes(q)
      );
    },

    /**
     * Maneja el cambio de cadena farmacéutica
     */
    async onChangeCadena() {
      if (!this.cadena) {
        this.sucursales = [];
        this.sucursal = '';
        return;
      }

      try {
        console.log(`Cargando sucursales para cadena: ${this.cadena}`);
        const response = await fetch(`/api/sucursales/${this.cadena}`);

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        this.sucursales = data;
        console.log(`Cargadas ${data.length} sucursales`);

        // Limpiar medicamentos disponibles al cambiar de cadena
        this.medicamentosDisponibles = [];
      } catch (error) {
        console.error('Error loading sucursales:', error);
        this.sucursales = [];
        this.showAlert('Error al cargar las sucursales. Por favor, intente nuevamente.', 'error');
      }
    },

    /**
     * Maneja el cambio de sucursal
     */
    async onChangeSucursal() {
      if (this.sucursal) {
        await this.cargarMedicamentosConStock();
      } else {
        this.medicamentosDisponibles = [];
      }
    },

    /**
     * Carga medicamentos con stock para la sucursal seleccionada
     */
    async cargarMedicamentosConStock() {
      if (!this.sucursal) return;

      try {
        console.log(`Cargando medicamentos con stock para sucursal: ${this.sucursal}`);
        const response = await fetch(`/api/medicamentos-stock/${this.sucursal}`);

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        this.medicamentosDisponibles = data;
        console.log(`Cargados ${data.length} medicamentos con stock`);
      } catch (error) {
        console.error('Error loading medicamentos:', error);
        this.medicamentosDisponibles = [];
        this.showAlert('Error al cargar los medicamentos. Por favor, intente nuevamente.', 'error');
      }
    },

    /**
     * Selecciona un medicamento del autocompletado
     */
    seleccionarMedicamento(medicamento) {
      // Compatibilidad con datos antiguos (string) y nuevos (object)
      if (typeof medicamento === 'string') {
        this.medicamentoId = null;
        this.medicamentoNombre = medicamento;
        this.medicamentoPrecio = 0;
      } else {
        this.medicamentoId = medicamento.id;
        this.medicamentoNombre = medicamento.nombre_comercial;
        this.medicamentoPrecio = parseFloat(medicamento.precio_unitario || 0);
      }

      this.busqueda = this.medicamentoNombre;
      this.mostrarSugerencias = false;

      // Enfocar el campo cantidad
      this.$nextTick(() => {
        document.getElementById('cantidad')?.focus();
      });
    },

    /**
     * Agrega un medicamento a la lista
     */
    agregarMedicamento() {
      // Validaciones
      if (!this.medicamentoNombre && !this.medicamentoId) {
        this.showAlert('Por favor seleccione un medicamento', 'warning');
        return;
      }

      if (!this.cantidad || this.cantidad <= 0) {
        this.showAlert('Por favor ingrese una cantidad válida', 'warning');
        return;
      }

      // Verificar duplicados
      const medicamentoExistente = this.medicamentosItems.find(item =>
        item.id === this.medicamentoId
      );

      if (medicamentoExistente) {
        this.showAlert('Este medicamento ya está en la lista', 'warning');
        return;
      }

      // Agregar a la lista
      const cantidad = parseInt(this.cantidad);
      const subtotal = this.medicamentoPrecio * cantidad;

      this.medicamentosItems.push({
        id: this.medicamentoId,
        nombre: this.medicamentoNombre,
        precio_unitario: this.medicamentoPrecio,
        cantidad: cantidad,
        subtotal: subtotal
      });

      console.log(`Medicamento agregado: ${this.medicamentoNombre} (${this.cantidad})`);

      // Limpiar campos solo si no hay errores
      const hasErrors = document.querySelector('.bg-red-100') !== null;
      if (!hasErrors) {
        this.limpiarCamposMedicamento();
      }
    },

    /**
     * Elimina un medicamento de la lista
     */
    eliminarMedicamento(index) {
      if (index >= 0 && index < this.medicamentosItems.length) {
        const medicamento = this.medicamentosItems[index];
        this.medicamentosItems.splice(index, 1);
        console.log(`Medicamento eliminado: ${medicamento.nombre}`);
      }
    },

    /**
     * Incrementa la cantidad de un medicamento
     */
    incrementarCantidad(index) {
      if (index >= 0 && index < this.medicamentosItems.length) {
        this.medicamentosItems[index].cantidad += 1;
        this.recalcularSubtotal(index);
      }
    },

    /**
     * Decrementa la cantidad de un medicamento
     */
    decrementarCantidad(index) {
      if (index >= 0 && index < this.medicamentosItems.length && this.medicamentosItems[index].cantidad > 1) {
        this.medicamentosItems[index].cantidad -= 1;
        this.recalcularSubtotal(index);
      }
    },

    /**
     * Actualiza la cantidad de un medicamento desde el input
     */
    actualizarCantidad(index, nuevaCantidad) {
      const cantidad = parseInt(nuevaCantidad);
      if (index >= 0 && index < this.medicamentosItems.length && cantidad > 0) {
        this.medicamentosItems[index].cantidad = cantidad;
        this.recalcularSubtotal(index);
      } else if (cantidad <= 0) {
        // Si la cantidad es 0 o menor, establecer a 1
        this.medicamentosItems[index].cantidad = 1;
        this.recalcularSubtotal(index);
      }
    },

    /**
     * Valida el formulario antes del envío
     */
    validarEnvio() {
      if (!this.cadena || !this.sucursal) {
        this.showAlert('Por favor seleccione cadena y sucursal', 'error');
        return false;
      }

      if (this.medicamentosItems.length === 0) {
        this.showAlert('Debe agregar al menos un medicamento', 'error');
        return false;
      }

      return true;
    },

    /**
     * Recalcula el subtotal de un medicamento
     */
    recalcularSubtotal(index) {
      if (index >= 0 && index < this.medicamentosItems.length) {
        const item = this.medicamentosItems[index];
        item.subtotal = item.precio_unitario * item.cantidad;
      }
    },

    /**
     * Computed: Calcula el total general de la compra
     */
    get totalCompra() {
      return this.medicamentosItems.reduce((total, item) => {
        return total + (item.subtotal || 0);
      }, 0);
    },

    /**
     * Limpia los campos de medicamento
     */
    limpiarCamposMedicamento() {
      this.medicamentoId = null;
      this.medicamentoNombre = '';
      this.medicamentoPrecio = 0;
      this.busqueda = '';
      this.cantidad = '';

      // Enfocar el campo de búsqueda
      this.$nextTick(() => {
        document.getElementById('medicamento')?.focus();
      });
    },

    /**
     * Muestra una alerta personalizada al usuario
     */
    showAlert(message, type = 'info') {
      const alerta = {
        id: ++this.alertaIdCounter,
        message,
        type,
        visible: true
      };

      this.alertas.push(alerta);

      // Auto-remover después de 5 segundos
      setTimeout(() => {
        this.removeAlert(alerta.id);
      }, 5000);
    },

    /**
     * Remueve una alerta
     */
    removeAlert(alertaId) {
      const index = this.alertas.findIndex(a => a.id === alertaId);
      if (index !== -1) {
        this.alertas.splice(index, 1);
      }
    },

    /**
     * Obtiene las clases CSS para el tipo de alerta
     */
    getAlertClasses(type) {
      const baseClasses = 'px-4 py-3 rounded mb-4 flex items-center justify-between shadow-lg transition-all duration-300';

      switch (type) {
        case 'error':
          return `${baseClasses} bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400`;
        case 'warning':
          return `${baseClasses} bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-400`;
        case 'success':
          return `${baseClasses} bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400`;
        default:
          return `${baseClasses} bg-blue-100 dark:bg-blue-900/20 border border-blue-400 dark:border-blue-600 text-blue-700 dark:text-blue-400`;
      }
    },

    /**
     * Obtiene el ícono SVG para el tipo de alerta
     */
    getAlertIcon(type) {
      switch (type) {
        case 'error':
          return '<svg class="h-6 w-6 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
        case 'warning':
          return '<svg class="h-6 w-6 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
        case 'success':
          return '<svg class="h-6 w-6 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
        default:
          return '<svg class="h-6 w-6 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
      }
    }
  };
};