<header class="bg-white shadow dark:bg-gray-800">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="text-lg font-semibold dark:text-white">ConcurrencyApp</div>
        <div class="flex items-center space-x-4">
            <nav class="space-x-4">
                <a href="/"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Inicio</a>
                <a href="{{ route('pedidos.index') }}"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Pedidos</a>
                <a href="{{ route('pedidos.create') }}"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Crear Pedido</a>
                <a href="{{ route('medicamentos.index') }}"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Medicamentos</a>
            </nav>
            <!-- Theme Toggle Button -->
            <button x-data="themeToggle()" x-init="init()" @click="toggle()" type="button"
                class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                <span x-show="!isDark">🌙</span>
                <span x-show="isDark">☀️</span>
            </button>
        </div>
    </div>
</header>

<script>
    function themeToggle() {
        return {
            isDark: false,
            init() {
                // Check localStorage or OS preference
                this.isDark = localStorage.theme === 'dark' ||
                    (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

                // Apply theme
                this.applyTheme();
            },
            toggle() {
                this.isDark = !this.isDark;
                this.applyTheme();
            },
            applyTheme() {
                if (this.isDark) {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                }
            }
        };
    }
</script>
