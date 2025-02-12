<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <script>
            if (globalThis === undefined) {
                var globalThis = window;
            }
            const allSettled = (promises) => {
                // map the promises to return custom response.
                const mappedPromises = promises.map(
                    p => Promise.resolve(p)
                        .then(
                            val => ({ status: 'fulfilled', value: val }),
                            err => ({ status: 'rejected', reason: err })
                        )
                );

                // run all the promises once with .all
                return Promise.all(mappedPromises);
            }
            Promise.allSettled = allSettled;
        </script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
