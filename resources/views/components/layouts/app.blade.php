<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('imagens/logo_branco.png') }}" type="image/png">

    <title>{{ $title ?? 'Devprod Venda' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>
    @vite('resources/css/app.css')
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>
    <flux:brand href="/" logo="{{ asset('imagens/logo_branco.png') }}" name="{{ config('app.name','Devprod Vendas') }}" class="px-2 dark:hidden"/>
    <flux:brand href="/" logo="{{ asset('imagens/logo_branco.png') }}" name="{{ config('app.name','Devprod Vendas') }}" class="px-2 hidden dark:flex"/>
    <flux:input as="button" variant="filled" placeholder="Pesquisar..." icon="magnifying-glass"/>
    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" href="/vendas">Vendas</flux:navlist.item>
    </flux:navlist>
    <flux:spacer/>
    <flux:navlist variant="outline">
        <flux:navlist.item icon="cog-6-tooth" href="#">Configurações</flux:navlist.item>
        <flux:navlist.item icon="information-circle" href="#">Ajuda</flux:navlist.item>
    </flux:navlist>
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <div class="flex space-x-1">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png" name="Administrador"/>
            <flux:separator vertical variant="subtle" class="my-2"/>
            <flux:dropdown x-data align="end">
                <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
                    <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="text-zinc-500 dark:text-white"/>
                    <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="text-zinc-500 dark:text-white"/>
                    <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini"/>
                    <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini"/>
                </flux:button>

                <flux:menu>
                    <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Claro</flux:menu.item>
                    <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Escuro</flux:menu.item>
                    <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">Automático</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>

        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>Administrador</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>
    <flux:spacer/>
    <flux:dropdown position="top" alignt="start">
        <div class="flex space-x-1">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png"/>
            <flux:separator vertical variant="subtle" class="my-2"/>
            <flux:dropdown x-data align="end">
                <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
                    <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="text-zinc-500 dark:text-white"/>
                    <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="text-zinc-500 dark:text-white"/>
                    <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini"/>
                    <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini"/>
                </flux:button>

                <flux:menu>
                    <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Claro</flux:menu.item>
                    <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Escuro</flux:menu.item>
                    <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">Automático</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>

        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>Administrador</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</flux:header>
<flux:main>
    {{ $slot }}
</flux:main>
@fluxScripts
<flux:toast position="top right"/>
</body>
</html>
