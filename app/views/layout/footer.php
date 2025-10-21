    </main>
    
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">FarmaXpress</h3>
                    <p class="text-gray-300">Sistema de gestión farmacéutica desarrollado con las mejores prácticas de desarrollo web.</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white">Inicio</a></li>
                        <li><a href="/medicamentos" class="text-gray-300 hover:text-white">Medicamentos</a></li>
                        <li><a href="/farmacias" class="text-gray-300 hover:text-white">Farmacias</a></li>
                        <li><a href="/contacto" class="text-gray-300 hover:text-white">Contacto</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Tecnologías</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li>PHP 7.4+</li>
                        <li>MySQL 5.7+</li>
                        <li>HTML5 & CSS3</li>
                        <li>JavaScript ES6+</li>
                        <li>Python (IA)</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; 2024 FarmaXpress. Desarrollado con patrón MVC y metodología SCRUM.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts adicionales -->
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script><?= $script ?></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

