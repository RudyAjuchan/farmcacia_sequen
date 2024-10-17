<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Farmacia Sequen</title>
    <link rel="stylesheet" href="/css/style.css">
    @vite('resources/js/app.js')
</head>

<body>
    <div id="app">
        <div :class="['navigation', { active: isActive }]">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon"><img src="/images/image.png" alt="" width="40"
                                style="margin-top: 10px;"></span>
                        <span class="title">FARMACIA SEQUEN</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard#/categorias">
                        <span class="icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                        <span class="title">Categorias</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard#/subcategorias">
                        <span class="icon"><ion-icon name="folder-open-outline"></ion-icon></span>
                        <span class="title">Sub categorias</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard#/proveedores">
                        <span class="icon"><ion-icon name="business-outline"></ion-icon></span>
                        <span class="title">Proveedores</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard#/productos">
                        <span class="icon"><ion-icon name="cube-outline"></ion-icon></span>
                        <span class="title">Productos</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard#/compras">
                        <span class="icon"><ion-icon name="cash-outline"></ion-icon></span>
                        <span class="title">Compras</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard#/ventas">
                        <span class="icon"><ion-icon name="cart-outline"></ion-icon></span>
                        <span class="title">Ventas</span>
                    </a>
                </li>
                <li>
                    <a href="/dashboard#/promociones">
                        <span class="icon"><ion-icon name="trending-down-outline"></ion-icon></span>
                        <span class="title">Promociones</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
            <div class="toggle" @click="toggleNavigation"></div>
        </div>

        <div class="content">
            <!-- Contenido principal aquí -->
            <router-view ></router-view>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        let navigation = document.querySelector('.navigation');
        let toggle = document.querySelector('.toggle');
        toggle.onclick = function(){
            console.log("hola")
            navigation.classList.toggle('active');
        }
    </script>
</body>

</html>
