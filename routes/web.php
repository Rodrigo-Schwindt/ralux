<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CarritoConfigController;
use App\Http\Controllers\Admin\FacturaController;
use App\Http\Controllers\Precios\PrecioController;
use App\Http\Controllers\Sliders\SlidersController;
use App\Http\Controllers\Sliders\SlidersCreate;
use App\Http\Controllers\Sliders\SlidersEdit;
use App\Http\Controllers\VehiculoTipo\VehiculoTipoController;
use App\Http\Controllers\VehiculoTipo\VehiculoTipoCreate;
use App\Http\Controllers\VehiculoTipo\VehiculoTipoEdit;
use App\Http\Controllers\Marcas\MarcaController;
use App\Http\Controllers\Marcas\MarcaCreate;
use App\Http\Controllers\Marcas\MarcaEdit;
use App\Http\Controllers\Modelos\ModeloController;
use App\Http\Controllers\Modelos\ModeloCreate;
use App\Http\Controllers\Modelos\ModeloEdit;
use App\Http\Controllers\ProductoTipo\ProductoTipoController;
use App\Http\Controllers\ProductoTipo\ProductoTipoCreate;
use App\Http\Controllers\ProductoTipo\ProductoTipoEdit;
use App\Http\Controllers\Productos\ProductoController;
use App\Http\Controllers\Productos\ProductoCreate;
use App\Http\Controllers\Productos\ProductoEdit;
use App\Http\Controllers\Productos\FichaTecnicaController;
use App\Http\Controllers\Contact\ContactManager;
use App\Http\Controllers\Nosotros\NosotrosAdmin;
use App\Http\Controllers\Nosotros\NosotrosHomeAdmin;
use App\Livewire\Vistas\Nosotros\NosotrosPage;
use App\Livewire\Vistas\Productos\ProductosPage;
use App\Livewire\Vistas\Productos\ProductoDetalle;
use App\Livewire\Vistas\Home\Inicio;
use App\Http\Controllers\Novedades\NovCategoriesIndex;
use App\Http\Controllers\Novedades\NovedadesIndex;
use App\Http\Controllers\Novedades\NovedadesCreate;
use App\Http\Controllers\Novedades\NovedadesEdit;
use App\Http\Controllers\Novedades\NovCategoriesCreate;
use App\Http\Controllers\Novedades\NovCategoriesEdit;
use App\Livewire\Vistas\Novedades\NovedadesPublic;
use App\Livewire\Vistas\Novedades\NovedadDetalle;
use App\Http\Controllers\Catalogo\CatalogoController;
use App\Livewire\Vistas\Contact\ContactPage;
use App\Livewire\Vistas\Catalogos\CatalogosPage;
use App\Http\Controllers\Servicios\ServicioController;
use App\Livewire\Vistas\Servicios\Servicios;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Usuarios\UsuariosController;
use App\Http\Controllers\Auth\ClienteAuthController;
use App\Http\Controllers\Equivalencias\EquivalenciaController;
use App\Http\Controllers\Equivalencias\EquivalenciaCreate;
use App\Http\Controllers\Equivalencias\EquivalenciaEdit;
use App\Http\Controllers\CodigoOM\CodigoOMController;
use App\Http\Controllers\CodigoOM\CodigoOMCreate;
use App\Http\Controllers\CodigoOM\CodigoOMEdit;
use App\Http\Controllers\Admin\Newsletter\NewsletterCrud;
use App\Http\Controllers\Metadata\MetadataCrud;
use App\Http\Controllers\Clientes\ClienteController;





    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/', Inicio::class)->name('home');
    Route::get('/nosotros', NosotrosPage::class)->name('nosotros');
    Route::get('/productos', ProductosPage::class)->name('productos');
    Route::get('/productos/{id}', ProductoDetalle::class)->name('productos.detalle');
    Route::get('/productos/{id}/ficha-tecnica', [FichaTecnicaController::class, 'download'])->name('productos.ficha-tecnica');
    Route::get('/productos/{id}/ficha-tecnica/ver', [FichaTecnicaController::class, 'view'])->name('productos.ficha-tecnica.ver');
    Route::get('/novedades', NovedadesPublic::class)->name('novedades.public');
    Route::get('/novedades/{id}', NovedadDetalle::class)->name('novedad.detalle');
    Route::get('/contacto', ContactPage::class)->name('contacto');
    Route::get('/catalogos', CatalogosPage::class)->name('catalogos');
    Route::get('/servicios', Servicios::class)->name('servicio');
    

    Route::prefix('zona-privada')->name('cliente.')->group(function () {
    Route::get('/login', [ClienteAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClienteAuthController::class, 'login'])->name('login.post');
    
    Route::middleware('auth:cliente')->group(function () {
        Route::get('/productos', \App\Livewire\Zona\ProductosZona::class)->name('productos');
        Route::get('/carrito', \App\Livewire\Zona\CarritoZona::class)->name('carrito');
        Route::post('/carrito/realizar-pedido', [\App\Http\Controllers\Cliente\CarritoController::class, 'realizarPedido'])->name('carrito.realizar-pedido');

        Route::get('/pedidos', \App\Livewire\Zona\MisPedidos::class)->name('pedidos');
        Route::get('/pedidos/{id}', \App\Livewire\Zona\DetallePedido::class)->name('pedidos.detalle');

        Route::get('/precios', \App\Livewire\Zona\Precios::class)->name('precios');
        Route::get('/precios/{id}/ver', \App\Livewire\Zona\PrecioPreview::class)->name('precios.ver');

        Route::post('/logout', [ClienteAuthController::class, 'logout'])->name('logout');
    });
});
    
    Route::middleware(['auth', 'is_admin'])->group(function () {
        Route::get('/admin', function () {
            return redirect()->route('productos.index');})->name('admin.dashboard');
            Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
  
   Route::prefix('admin/sliders')->group(function () {
        Route::get('/', [SlidersController::class, 'index'])->name('sliders.index');
        Route::get('/create', [SlidersCreate::class, 'index'])->name('sliders.create');
        Route::post('/', [SlidersCreate::class, 'store'])->name('sliders.store');
        Route::get('/{id}/edit', [SlidersEdit::class, 'index'])->name('sliders.edit');
        Route::post('/{id}', [SlidersEdit::class, 'update'])->name('sliders.update');
        Route::delete('/{id}', [SlidersController::class, 'destroy'])->name('sliders.destroy');
    });

    Route::get('/admin/contacto', [ContactManager::class, 'index'])->name('admin.contacto');
    Route::post('/admin/contacto', [ContactManager::class, 'save'])->name('admin.contacto.save');

    Route::get('admin/nosotros', [NosotrosAdmin::class, 'index'])->name('nosotros.index');
    Route::post('admin/nosotros', [NosotrosAdmin::class, 'save'])->name('nosotros.save');
    Route::delete('admin/nosotros/image/{field}', [NosotrosAdmin::class, 'deleteImage'])->name('nosotros.image.delete');

    Route::get('admin/nosotros/home', [NosotrosHomeAdmin::class, 'index'])->name('nosotros.home.index');
    Route::post('admin/nosotros/home', [NosotrosHomeAdmin::class, 'save'])->name('nosotros.home.save');
    Route::delete('admin/nosotros/home/image', [NosotrosHomeAdmin::class, 'deleteImage'])->name('nosotros.home.image.delete');


    Route::get('/admin/novcategorias', [NovCategoriesIndex::class, 'index'])->name('novcategories.index');
    Route::get('/admin/novcategorias/create', [NovCategoriesCreate::class, 'create'])->name('novcategories.create');
    Route::post('/admin/novcategorias/store', [NovCategoriesCreate::class, 'store'])->name('novcategories.store');
    Route::get('/admin/novcategorias/{id}/edit', [NovCategoriesEdit::class, 'edit'])->name('novcategories.edit');
    Route::post('/admin/novcategorias/{id}/update', [NovCategoriesEdit::class, 'update'])->name('novcategories.update');
    Route::delete('/admin/novcategorias/{id}', [NovCategoriesIndex::class, 'delete'])->name('novcategories.delete');

    Route::get('/admin/novedades', [NovedadesIndex::class, 'index'])->name('novedades.index');
    Route::get('/admin/novedades/create', [NovedadesCreate::class, 'create'])->name('novedades.create');
    Route::post('/admin/novedades', [NovedadesCreate::class, 'store'])->name('novedades.store');
    Route::post('/admin/novedades/banner', [NovedadesIndex::class, 'saveBanner'])->name('novedades.banner.save');
    Route::delete('/admin/novedades/banner', [NovedadesIndex::class, 'removeBanner'])->name('novedades.banner.remove');
    Route::delete('/admin/novedades/{id}', [NovedadesIndex::class, 'delete'])->name('novedades.delete');
    Route::post('/admin/novedades/{id}/destacado', [NovedadesIndex::class, 'toggleDestacado'])->name('novedades.toggle');
    Route::get('/admin/novedades/{id}/edit', [NovedadesEdit::class, 'edit'])->name('novedades.edit');
    Route::put('/admin/novedades/{id}', [NovedadesEdit::class, 'update'])->name('novedades.update');

    Route::prefix('admin/vehiculo-tipo')->group(function () {
        Route::get('/', [VehiculoTipoController::class, 'index'])->name('vehiculo-tipo.index');
        Route::get('/create', [VehiculoTipoCreate::class, 'create'])->name('vehiculo-tipo.create');
        Route::post('/', [VehiculoTipoCreate::class, 'store'])->name('vehiculo-tipo.store');
        Route::get('/{id}/edit', [VehiculoTipoEdit::class, 'edit'])->name('vehiculo-tipo.edit');
        Route::post('/{id}', [VehiculoTipoEdit::class, 'update'])->name('vehiculo-tipo.update');
        Route::delete('/{id}', [VehiculoTipoController::class, 'destroy'])->name('vehiculo-tipo.destroy');
    });

    Route::prefix('admin/marcas')->group(function () {
        Route::get('/', [MarcaController::class, 'index'])->name('marcas.index');
        Route::get('/create', [MarcaCreate::class, 'create'])->name('marcas.create');
        Route::post('/', [MarcaCreate::class, 'store'])->name('marcas.store');
        Route::get('/{id}/edit', [MarcaEdit::class, 'edit'])->name('marcas.edit');
        Route::post('/{id}', [MarcaEdit::class, 'update'])->name('marcas.update');
        Route::delete('/{id}', [MarcaController::class, 'destroy'])->name('marcas.destroy');
    });

    Route::prefix('admin/modelos')->group(function () {
        Route::get('/', [ModeloController::class, 'index'])->name('modelos.index');
        Route::get('/create', [ModeloCreate::class, 'create'])->name('modelos.create');
        Route::post('/', [ModeloCreate::class, 'store'])->name('modelos.store');
        Route::get('/{id}/edit', [ModeloEdit::class, 'edit'])->name('modelos.edit');
        Route::post('/{id}', [ModeloEdit::class, 'update'])->name('modelos.update');
        Route::delete('/{id}', [ModeloController::class, 'destroy'])->name('modelos.destroy');
    });

    Route::prefix('admin/producto-tipo')->group(function () {
        Route::get('/', [ProductoTipoController::class, 'index'])->name('producto-tipo.index');
        Route::get('/create', [ProductoTipoCreate::class, 'create'])->name('producto-tipo.create');
        Route::post('/', [ProductoTipoCreate::class, 'store'])->name('producto-tipo.store');
        Route::get('/{id}/edit', [ProductoTipoEdit::class, 'edit'])->name('producto-tipo.edit');
        Route::post('/{id}', [ProductoTipoEdit::class, 'update'])->name('producto-tipo.update');
        Route::delete('/{id}', [ProductoTipoController::class, 'destroy'])->name('producto-tipo.destroy');
    });
    Route::prefix('admin/precios')->group(function () {
        Route::get('/', [PrecioController::class, 'index'])->name('precios.index');
        Route::get('/create', [PrecioController::class, 'create'])->name('precios.create');
        Route::post('/', [PrecioController::class, 'store'])->name('precios.store');
        Route::post('/generar-desde-productos', [PrecioController::class, 'generarDesdeProductos'])->name('precios.generar-desde-productos');
        Route::post('/{id}/publicar', [PrecioController::class, 'publicar'])->name('precios.publicar');
        Route::get('/{id}/edit', [PrecioController::class, 'edit'])->name('precios.edit');
        Route::put('/{id}', [PrecioController::class, 'update'])->name('precios.update');
        Route::delete('/{id}', [PrecioController::class, 'destroy'])->name('precios.destroy');
    });

    Route::prefix('admin/catalogos')->group(function () {
    Route::get('/', [CatalogoController::class, 'index'])->name('catalogos.index');
    Route::get('/create', [CatalogoController::class, 'create'])->name('catalogos.create');
    Route::post('/', [CatalogoController::class, 'store'])->name('catalogos.store');
    Route::get('/{catalogo}/edit', [CatalogoController::class, 'edit'])->name('catalogos.edit');
    Route::put('/{catalogo}', [CatalogoController::class, 'update'])->name('catalogos.update');
    Route::delete('/{catalogo}', [CatalogoController::class, 'destroy'])->name('catalogos.destroy');
    Route::post('/update-banner', [CatalogoController::class, 'updateBanner'])->name('catalogos.update-banner');
    Route::delete('/delete-banner-image', [CatalogoController::class, 'deleteBannerImage'])->name('catalogos.delete-banner-image');
    });

    Route::prefix('admin/productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/create', [ProductoCreate::class, 'create'])->name('productos.create');
        Route::post('/', [ProductoCreate::class, 'store'])->name('productos.store');
        // Rutas específicas antes de las que tienen {id} para evitar conflictos
        Route::get('/exportar/productos', [ProductoController::class, 'exportar'])->name('productos.exportar');
        Route::get('/exportar/plantilla', [ProductoController::class, 'exportarPlantilla'])->name('productos.exportar-plantilla');
        Route::get('/exportar/copia-seguridad', [ProductoController::class, 'descargarCopiaSeguridad'])->name('productos.backup-db');
        Route::post('/importar', [ProductoController::class, 'importar'])->name('productos.importar');
        Route::get('/{id}/edit', [ProductoEdit::class, 'edit'])->name('productos.edit');
        Route::post('/{id}', [ProductoEdit::class, 'update'])->name('productos.update');
        Route::delete('/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::delete('/{id}/archivo/{field}', [ProductoController::class, 'destroyArchivo'])->name('productos.archivo.destroy');
        Route::delete('/{id}/imagenes/{imagenId}', [ProductoController::class, 'destroyImagen'])->name('productos.imagenes.destroy');
        Route::post('/{id}/imagenes/{imagenId}/principal', [ProductoController::class, 'setPrincipalImagen'])->name('productos.imagenes.principal');
    });

    Route::prefix('admin/equivalencias')->group(function () {
        Route::get('/', [EquivalenciaController::class, 'index'])->name('equivalencias.index');
        Route::get('/create', [EquivalenciaCreate::class, 'create'])->name('equivalencias.create');
        Route::post('/', [EquivalenciaCreate::class, 'store'])->name('equivalencias.store');
        Route::get('/{id}/edit', [EquivalenciaEdit::class, 'edit'])->name('equivalencias.edit');
        Route::post('/{id}', [EquivalenciaEdit::class, 'update'])->name('equivalencias.update');
        Route::delete('/{id}', [EquivalenciaController::class, 'destroy'])->name('equivalencias.destroy');
    });

    Route::prefix('admin/codigo-om')->group(function () {
        Route::get('/', [CodigoOMController::class, 'index'])->name('codigo-om.index');
        Route::get('/create', [CodigoOMCreate::class, 'create'])->name('codigo-om.create');
        Route::post('/', [CodigoOMCreate::class, 'store'])->name('codigo-om.store');
        Route::get('/{id}/edit', [CodigoOMEdit::class, 'edit'])->name('codigo-om.edit');
        Route::post('/{id}', [CodigoOMEdit::class, 'update'])->name('codigo-om.update');
        Route::delete('/{id}', [CodigoOMController::class, 'destroy'])->name('codigo-om.destroy');
    });

    Route::get('/admin/servicio', [ServicioController::class, 'index'])->name('servicios.admin');
    Route::post('/admin/servicio/save', [ServicioController::class, 'save'])->name('servicios.save');
    Route::delete('/admin/servicio/delete-image', [ServicioController::class, 'deleteImage'])->name('servicios.deleteImage');
    Route::post('/admin/servicio/downloads', [ServicioController::class, 'addDownload'])->name('servicios.downloads.add');
    Route::delete('/admin/servicio/downloads/{id}', [ServicioController::class, 'deleteDownload'])->name('servicios.downloads.delete');

        Route::prefix('admin/usuarios')->group(function () {
        Route::get('/', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/create', [UsuariosController::class, 'create'])->name('usuarios.create');
        Route::post('/', [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/{id}/edit', [UsuariosController::class, 'edit'])->name('usuarios.edit');
        Route::put('/{id}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

        });
        Route::prefix('admin/clientes')->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::post('/{cliente}/toggle-activo', [ClienteController::class, 'toggleActivo'])->name('clientes.toggle-activo');

            Route::controller(NewsletterCrud::class)->group(function () {
        Route::get('/admin/newsletter', 'index')->name('admin.newsletter');
        Route::post('/admin/newsletter/send', 'send')->name('admin.newsletter.send');
        Route::post('/admin/newsletter/{id}/toggle', 'toggleActive')->name('admin.newsletter.toggle');
        Route::delete('/admin/newsletter/{id}', 'deleteSubscriber')->name('admin.newsletter.delete');
    });

                Route::get('/admin/metadata', [MetadataCrud::class, 'index'])->name('admin.metadata');
    Route::post('/admin/metadata', [MetadataCrud::class, 'save'])->name('admin.metadata.save');
    Route::post('/admin/metadata/generar-productos', [MetadataCrud::class, 'generateProducts'])->name('admin.metadata.generate-products');
    Route::delete('/admin/metadata/{id}', [MetadataCrud::class, 'delete'])->name('admin.metadata.delete');

    Route::get('/admin/carrito-config', [CarritoConfigController::class, 'index'])->name('admin.carrito-config');
    Route::post('/admin/carrito-config', [CarritoConfigController::class, 'save'])->name('admin.carrito-config.save');

    Route::get('/admin/pedidos', [\App\Http\Controllers\Admin\PedidoController::class, 'index'])->name('admin.pedidos');
    Route::get('/admin/pedidos/exportar/todo', [\App\Http\Controllers\Admin\PedidoController::class, 'exportarTodo'])->name('admin.pedidos.exportar-todo');
    Route::get('/admin/pedidos/exportar/filtrado', [\App\Http\Controllers\Admin\PedidoController::class, 'exportarFiltrado'])->name('admin.pedidos.exportar-filtrado');
    Route::delete('/admin/pedidos/eliminar/seleccionados', [\App\Http\Controllers\Admin\PedidoController::class, 'destroyMultiple'])->name('admin.pedidos.destroy-multiple');
    Route::delete('/admin/pedidos/{id}', [\App\Http\Controllers\Admin\PedidoController::class, 'destroy'])->name('admin.pedidos.destroy');
    Route::get('/admin/pedidos/{id}', [\App\Http\Controllers\Admin\PedidoController::class, 'show'])->name('admin.pedidos.show');
    Route::post('/admin/pedidos/{id}/toggle', [\App\Http\Controllers\Admin\PedidoController::class, 'toggleEntregado'])->name('admin.pedidos.toggle');
    Route::post('/admin/pedidos/{id}/cancelar', [\App\Http\Controllers\Admin\PedidoController::class, 'toggleCancelado'])->name('admin.pedidos.cancelar');
    Route::post('/admin/pedidos/{id}/fecha-entrega', [\App\Http\Controllers\Admin\PedidoController::class, 'updateFechaEntrega'])->name('admin.pedidos.fecha');
    Route::get('/admin/pedidos/{id}/factura', [FacturaController::class, 'generar'])->name('admin.pedidos.factura');
    });
});
