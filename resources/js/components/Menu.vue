<template>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Consignee</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Vehicle</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Container</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Export</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Price</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reporting</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Accounting</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Settings
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <router-link :to="{ name: 'settings.locations' }" class="dropdown-item" active-class="active">
                               Location
                            </router-link>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</template>
<script>
export default {
    name: 'Menu',
}
</script>
