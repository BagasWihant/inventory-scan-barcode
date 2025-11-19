 <!DOCTYPE html>
 <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="csrf-token" content="{{ csrf_token() }}">

     <title>@yield('title', 'Warehouse')</title>

     <!-- Fonts -->
     <link rel="preconnect" href="https://fonts.bunny.net">
     <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
     <link href="{{ asset('assets/select2.min.css') }}" rel="stylesheet" />
     <!-- Scripts -->
     <script src="{{ asset('assets/jquery.js') }}"></script>
     <script src="{{ asset('assets/select2.min.js') }}"></script>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
 </head>

 <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">

     <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
         <div class="flex">
             <div class="p-4 w-full mt-7">
                 <!-- Page Content -->
                 <main>
                     {{ $slot }}
                 </main>
             </div>
         </div>

     </div>

 </body>

 </html>
