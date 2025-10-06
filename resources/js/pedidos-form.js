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
    cantidad: document.querySelector('input[name="cantidad_temp_old"]')?.value || '',
    medicamentosItems: JSON.parse(document.querySelector('input[name="medicamentos_old"]')?.value || '[]'),

    // Búsqueda
    busqueda: document.querySelector('input[name="medicamento_busqueda_old"]')?.value || '',
    mostrarSugerencias: false,
    medicamentosDisponibles: medicamentos,

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
      } catch (error) {
        console.error('Error loading sucursales:', error);
        this.sucursales = [];
        this.showAlert('Error al cargar las sucursales. Por favor, intente nuevamente.', 'error');
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
      } else {
        this.medicamentoId = medicamento.id;
        this.medicamentoNombre = medicamento.nombre_comercial;
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
      this.medicamentosItems.push({
        id: this.medicamentoId,
        nombre: this.medicamentoNombre,
        cantidad: parseInt(this.cantidad),
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
     * Limpia los campos de medicamento
     */
    limpiarCamposMedicamento() {
      this.medicamentoId = null;
      this.medicamentoNombre = '';
      this.busqueda = '';
      this.cantidad = '';

      // Enfocar el campo de búsqueda
      this.$nextTick(() => {
        document.getElementById('medicamento')?.focus();
      });
    },

    /**
     * Muestra una alerta al usuario
     */
    showAlert(message, type = 'info') {
      // Simple alert por ahora, se puede mejorar con notificaciones más elegantes
      alert(message);
    }
  };
};