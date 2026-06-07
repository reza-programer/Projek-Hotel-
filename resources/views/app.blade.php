<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>Darma Mizuki</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead

        <!-- Midtrans Snap.js -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
