<?php

use App\Http\Controllers\API\StatisticsController;
use App\Http\Resources\ResourceObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BoxController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\GuestController;
use App\Http\Controllers\API\HotelController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ReceptionController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\PDFController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return ResourceObject::make($request->user());
});


Route::name('api.v1.')->group(function () {

    //HOTEL DATA CONFIGURATION
    Route::get('/hotel', [HotelController::class, 'index'])->name('hotel.index');
    Route::put('/hotel/{hotel}', [HotelController::class, 'update'])->name('hotel.update');


    //ROLES
    Route::apiResource('/roles', RoleController::class)
        ->where(['id' =>'[0-9]+']) //it does not work
        ->missing(function (Request $request) {
            return response()->macroResponseJsonApi('resource not found', 404);
        });


    //USERS
    Route::get('/users/{user:status}', [UserController::class, 'index'])
        ->name('users.index')
        ->where('status', '[a-zA-Z]+');

    Route::get('/users/{user:dni}', [UserController::class, 'show'])
        ->name('users.show')
        ->where('dni', '[0-9]+')
        ->missing(function () {
            return response()->macroResponseJsonApi('resource not found', 404);
        });

    Route::apiResource('/users', UserController::class)
        ->except(['index'. 'show'])
        ->missing(function () {
            return response()->macroResponseJsonApi('resource not found', 404);
        });


    //CATEGORIES
    Route::get('/categories/{category:name}', [CategoryController::class, 'show'])
        ->name('categories.show')
        ->where('name', '[a-zA-Z]+')
        ->missing(function () {
            return response()->macroResponseJsonApi('resource not found', 404);
        });

    Route::get('/categories/get-all-rooms-by-category/{category}',
        [CategoryController::class, 'getAllRoomsByCategory'])
        ->name('categories.get-all-rooms-by-category')
        ->where('name', '[a-zA-Z]+');

    Route::apiResource('/categories', CategoryController::class)
        ->except(['show'])
        ->missing(function () {
            return response()->macroResponseJsonApi('resource not found', 404);
        });

    //ROOMS
    Route::get('/rooms/{room:status?}', [RoomController::class, 'index'])
        ->name('rooms.index')
        ->where('status', '[a-zA-Z]+');
    /*Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');*/

    Route::apiResource('/rooms', RoomController::class)
        ->except(['index'])
        ->where(['number' => '[0-9]+'])
        ->missing(function () {
            return response()->macroResponseJsonApi('resource not found', 404);
        });

    //RECEPTION
    Route::get('/reception', [ReceptionController::class, 'index'])->name('reception.index');

    Route::get('/reception/more-information-about-the-guest/{number}', [ReceptionController::class, 'showGuestData'])
        ->name('reception.show')
        ->where('number', '[0-9]+');

    Route::post('/reception', [ReceptionController::class, 'registerANewGuestThroughTheReception'])->name('reception.store');

    Route::put('/reception/{reception:id}', [ReceptionController::class, 'updateAGuestHostedThroughTheReception'])
    ->name('reception.update')
    ->where('id', '[0-9]+');

    Route::put('/reception/remove-a-guest-and-free-the-room/{idGuest}', [ReceptionController::class, 'removeAGuestAndFreeTheRoom'])
    ->name('reception.remove_a_guest_and_free_the_room')
    ->where('id', '[0-9]+');


    //RESERVATIONS
    Route::apiResource('/reservations', ReservationController::class)
        ->where(['id' => '[0-9]+']);


    //GUESTS
    Route::get('/guests/{guest:status?}', [GuestController::class, 'index'])
        ->name('guests.index')
        ->where('status', '[a-zA-Z]+');

    //not working
    Route::apiResource('/guests', GuestController::class)->except(['index'])
        ->except(['index']);


    //BOXES
    Route::get('/box', [BoxController::class, 'index']);
    Route::get('/box/daily-check', [BoxController::class, 'dailyCheck']);
    Route::get('/box/cash-details-by-date/{date}', [BoxController::class, 'cashDetailsByDate']);


    //SAVES IMAGES
    Route::delete('/remove-an-image-from-gallery/{id}', [ImageController::class, 'removeAnImageFromGallery'])->name('image.removeAnImageFromGallery');

    //STATISTICS
    Route::get('/get-most-used-rooms',[StatisticsController::class, 'getMostUsedRooms']);
    Route::get('/get-rooms-status',[StatisticsController::class, 'getRoomStatus']);
    Route::get('/get-a-sum-of-money-per-month',[StatisticsController::class, 'getASumOfMoneyPerMonth']);
    Route::get('/get-the-most-used-means-for-reservations',[StatisticsController::class, 'getTheMostUsedMeansForReservations']);


    //generar PDF
    Route::get('/generate-pdf/{number}', [PDFController::class, 'generatePDF'])->name('generatePDF');
});
