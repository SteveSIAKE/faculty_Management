<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion de Faculté</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <img class="h-8 w-auto" src="FM_logo_Noir.svg" alt="Logo">
                        <span class="ml-2 text-xl font-bold text-gray-800">Faculté Manager</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="/login" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Connexion</a>
                    <a href="/register" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium ml-4">Inscription</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block">Gérez votre faculté</span>
                            <span class="block text-blue-600">en toute simplicité</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Une solution complète pour la gestion administrative et pédagogique de votre établissement d'enseignement supérieur.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="/register" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                                    Commencer
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="#features" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10">
                                    En savoir plus
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Fonctionnalités</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Une meilleure façon de gérer votre faculté
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <!-- Feature 1 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Gestion des utilisateurs</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            Gérez facilement les comptes des administrateurs, professeurs et étudiants.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-book"></i>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Gestion des cours</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            Organisez vos cours, modules et programmes d'études.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Suivi des notes</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            Suivez les performances des étudiants et générez des rapports détaillés.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Gestion des présences</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            Suivez les présences et absences des étudiants en temps réel.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">À propos</h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="#" class="text-base text-gray-300 hover:text-white">
                                Qui sommes-nous
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-base text-gray-300 hover:text-white">
                                Contact
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Support</h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="#" class="text-base text-gray-300 hover:text-white">
                                Aide
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-base text-gray-300 hover:text-white">
                                Documentation
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-700 pt-8">
                <p class="text-base text-gray-400 text-center">
                    &copy; 2024 Système de Gestion de Faculté. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
