use App\Http\Controllers\TragosController;

Route::get('/tragos', [TragosController::class, 'getTragos']);
Route::get('/tragos/{id}', [TragosController::class, 'getTragoPorID']);

Route::get('/tragos', [TragosController::class, 'getTragosPorIngredientes']);
