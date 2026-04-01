<?php

use App\Http\Controllers\Api\V1\Dashboard\DashboardController;
use App\Http\Controllers\Api\V1\Page\PageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Api\V1\ACL\RolesController;
use App\Http\Controllers\Api\V1\ACL\PermissionController;
use App\Http\Controllers\Api\V1\ACL\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('user', [UserController::class, 'current']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);

    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
});

Route::group(['prefix' => 'acl'], function () {
    Route::get('roles', [RolesController::class, 'index']);
    Route::get('roles/{role}', [RolesController::class, 'show']);
    Route::post('roles/{role}', [RolesController::class, 'update']);
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::get('users', [UsersController::class, 'index']);
    Route::get('users/{user}', [UsersController::class, 'show']);
    Route::post('users/{user}', [UsersController::class, 'update']);
});

/* Anonymous routes */
Route::get( 'customers/loading-status/export', 'Customer\CustomerController@vehicleLoadingExport' );
//Route::get( 'customers/{id}/download-documents', 'Customer\CustomerController@downloadDocuments' );
Route::get( 'customers/{id}/download-documents', 'Customer\CustomerController@downloadDocuments' );
Route::get( 'customers/export/excel', 'Customer\CustomerController@exportExcel' );
Route::get( 'customers/export/pdf', 'Customer\CustomerController@exportPdf' );
Route::post( 'customers/{id}/upload-file', 'Customer\CustomerController@uploadProfilePhoto' );
Route::post( 'customers/{id}/add-more-document', 'Customer\CustomerController@customerDocumentAdd' );

Route::get( 'consignees/export/excel', 'Consignee\ConsigneeController@exportExcel' );

Route::get( 'invoices/{customerUserId}/export-excel', 'Invoice\InvoicesController@exportExcel' );

Route::get( 'tracking-vehicle', 'Vehicle\VehicleController@trackingVehicleInfo' );
Route::get( 'vehicles/features', 'Vehicle\VehicleController@vehicleFeatures' );
Route::get( 'vehicles/dropdown', 'Vehicle\VehicleController@vehicleDropdown' );
Route::get( 'vehicles/search-by-vin', 'Vehicle\VehicleController@searchByVin' );
//Route::get( 'vehicles/{id}/download-photos', 'Vehicle\VehicleController@downloadPhotos' );
//Route::get( 'vehicles/{id}/download-attachments', 'Vehicle\VehicleController@downloadDocuments' );
Route::get( 'vehicles/{id}/download-photos', 'Vehicle\VehicleController@downloadPhotos' );
Route::get( 'vehicles/{id}/download-attachments', 'Vehicle\VehicleController@downloadDocuments' );
Route::get( 'vehicles/{id}/condition-report-pdf', 'Vehicle\VehicleController@conditionReportPdf' );
Route::get( 'vehicles/export-excel', 'Vehicle\VehicleController@exportExcel' );
Route::post( 'vehicles/{id}/photos-upload', 'Vehicle\VehicleController@uploadvehicleImage' );
Route::post( 'vehicles/{id}/documents-upload', 'Vehicle\VehicleController@uploadvehicleDocument' );
Route::post( 'vehicles/{id}/add-more-images', 'Vehicle\VehicleController@vehicleImageAdd' );

Route::get( 'exports/{id}/manifest-modal', 'Export\ExportController@manifestModal' );
Route::get( 'exports/tracking-url/{containerNo}', 'Export\ExportController@getTrackingUrl' );
Route::get( 'exports/{id}/manifest-pdf', 'Export\ExportController@manifestPdf' );
Route::get( 'exports/{id}/dock-receipt-pdf', 'Export\ExportController@docReceivedPdf' );
Route::get( 'exports/{id}/landing-modal', 'Export\ExportController@landingModal' );
Route::get( 'exports/{id}/landing-pdf', 'Export\ExportController@landingPdf' );
//Route::get( 'exports/{id}/download-photos', 'Export\ExportController@downloadPhotos' );
Route::get( 'exports/{id}/download-photos', 'Export\ExportController@downloadPhotos' );
Route::post( 'exports/{id}/photos-upload', 'Export\ExportController@uploadExportImage' );
Route::post( 'exports/{id}/documents-upload', 'Export\ExportController@uploadExportDocument' );
Route::get( 'exports/streamship_lines', 'Export\ExportController@getStreamshipLines' );
Route::get( 'exports/export-excel', 'Export\ExportController@exportExcel' );

Route::get( 'vehicle-weights/export/excel', 'Vehicle\VehicleWeightController@exportExcel' );

Route::get( 'reports/customer-management/export', 'Reports\ReportController@customerManagementReportExport' );
Route::get( 'reports/customer-title-status/export', 'Reports\ReportController@customerTitleStatusReportExport' );
Route::get( 'reports/container-report/export', 'Reports\ReportController@containerReportExport' );
Route::get( 'reports/vehicle-report/export', 'Reports\ReportController@vehicleReportExport' );
Route::get( 'reports/inventory-report/export-excel', 'Reports\ReportController@inventoryReportExport' );
Route::get( 'reports/inventory-report/pdf', 'Reports\ReportController@inventoryReportPdf' );

Route::post( 'file-upload', 'File\FilesController@fileUpload' );
Route::post( 'pricing/file-upload', 'Pricing\PricingController@pricingFileUpload' );
Route::post( 'invoices/xml-upload', 'Invoice\InvoicesController@xmlUpload' );
Route::post( 'file-remove', 'File\FilesController@fileRemove' );
Route::post( 'invoices/document-upload', 'Invoice\InvoicesController@uploadDocument' );

Route::post( 'claims/photos-upload', 'Claim\DamageClaimController@uploadPhoto' );
//Route::get( 'vehicle-claims/{id}/download-photos', 'Claim\DamageClaimController@downloadPhotos' );
//Route::get( 'storage-claims/{id}/download-photos', 'Claim\StorageClaimController@downloadPhotos' );
//Route::get( 'key-missing-claims/{id}/download-photos', 'Claim\KeyMissingClaimController@downloadPhotos' );
Route::get( 'vehicle-claims/{id}/download-photos', 'Claim\DamageClaimController@downloadPhotos' );
Route::get( 'storage-claims/{id}/download-photos', 'Claim\StorageClaimController@downloadPhotos' );
Route::get( 'key-missing-claims/{id}/download-photos', 'Claim\KeyMissingClaimController@downloadPhotos' );

Route::get( 'reports/customer-management', 'Reports\ReportController@customerManagementReport' )->name( 'reports.customer-management' );
/* Anonymous routes */

Route::group(['middleware' => 'jwt.verify'], function () {
    /* Dashboard Related routes */
    Route::get( 'status-overview', [ DashboardController::class, 'statusOverview' ] );
    Route::get( 'invoice-overview', [ DashboardController::class, 'invoiceOverview' ] );
    /* Dashboard Related routes */

    Route::post( 'settings/profile', [ ProfileController::class, 'update' ] );
    Route::post( 'settings/password', [ PasswordController::class, 'update' ] );

    /* Setting related routes */
    Route::apiResource( 'settings/locations', Settings\LocationController::class );
    Route::apiResource( 'settings/countries', Settings\CountryController::class );
    Route::apiResource( 'settings/states', Settings\StateController::class );
    Route::apiResource( 'settings/yards', Settings\YardController::class );
    Route::apiResource( 'settings/cities', Settings\CityController::class );

    Route::get('pages/{slug}',[PageController::class, 'showPages']);
    Route::apiResource('settings/pages', Page\PageController::class);
    /* Setting related routes */

    /* Customers related routes */
    Route::get( 'customers/loading-status', 'Customer\CustomerController@vehicleLoading' );
    Route::get( 'customers/next-customer-id', 'Customer\CustomerController@nextCustomerId' );
    Route::apiResource( 'customers', Customer\CustomerController::class );
    /* Customers related routes */

    Route::apiResource( 'pricing', Pricing\PricingController::class );
    Route::apiResource( 'consignees', Consignee\ConsigneeController::class );
    Route::apiResource( 'notifications', Notification\NotificationController::class );
    Route::get( 'users/{id}/change-status', 'User\UserController@changeStatus' );
    Route::apiResource( 'users', User\UserController::class );
    Route::apiResource( 'notes', Note\NoteController::class );

    /* Claims related routes */
    Route::apiResource( 'vehicle-claims', Claim\DamageClaimController::class );
    Route::apiResource( 'storage-claims', Claim\StorageClaimController::class );
    Route::apiResource( 'key-missing-claims', Claim\KeyMissingClaimController::class );
    /* Claims related routes */

    Route::get( 'dashboard', [ DashboardController::class, 'index' ] );
    Route::get( 'customers-item', 'Customer\CustomerController@customerList' );
    Route::get( 'vehicle-checkbox-item', 'Vehicle\VehicleController@vehicleCheckBoxItem' );
    Route::get( 'vehicle-condition-item', 'Vehicle\VehicleController@vehicleConditionItem' );
    Route::get( 'consignee-search', 'Consignee\ConsigneeController@search' );
    Route::get( 'containers-search', 'Export\ExportController@searchContainers' );

    /* Vehicle related routes */
    Route::get( 'vehicles/bar', 'Vehicle\VehicleController@bar' );
    Route::post( 'vehicles/{id}/change-note-status', 'Vehicle\VehicleController@changeNoteStatus' );
    Route::post( 'vehicles/{id}/save-handed-over-date', 'Vehicle\VehicleController@updateHandedOverDate' );
    Route::get( 'vehicle-weight', 'Vehicle\VehicleController@getVehicleWeight' );
    Route::get( 'vehicle-search', 'Vehicle\VehicleController@vehicleSearch' );
    Route::get( 'vehicle-colors', 'Vehicle\VehicleController@getVehicleColors' );
    Route::get( 'vehicles/notes', 'Vehicle\VehicleController@notes' );
    Route::get( 'vehicles/location-wise-title', 'Vehicle\VehicleController@locationWiseTitleCount' );
    Route::get( 'vehicles/{id}/condition-report-modal', 'Vehicle\VehicleController@conditionReportModal' );
    Route::apiResource( 'vehicles', Vehicle\VehicleController::class );
    /* Vehicle related routes */

    /* Export related routes */
    Route::delete( 'export-images/{id}', 'Export\ExportController@deletePhoto' );
    Route::post( 'exports/{id}/delete-images', 'Export\ExportController@deleteAllPhotos' );
    Route::post( 'exports/{id}/handed-over', 'Export\ExportController@handOver' );
    Route::get( 'exports/{id}/houston-cover-letter-modal', 'Export\ExportController@hostonCustomCoverLetterModal' );
    Route::get( 'exports/{id}/custom-cover-letter-modal', 'Export\ExportController@customCoverLetterModal' );
    Route::get( 'exports/{id}/nonhaz-modal', 'Export\ExportController@nonHazModal' );
    Route::get( 'exports/{id}/dock-receipt-modal', 'Export\ExportController@dockReceiptModal' );
    Route::post( 'exports/{id}/add-more-images', 'Export\ExportController@addMoreImage' );
    Route::get( 'exports/search', 'Export\ExportController@search' );
    Route::apiResource( 'exports', Export\ExportController::class );
    /* Export related routes */

    /* Containers related routes */
    Route::post('containers/{id}/save-note', 'Export\ExportController@saveContainerNote');
    Route::get( 'containers', 'Export\ExportController@containers' );
    /* Containers related routes */

    /* Invoices related routes */
    Route::get( 'invoices/all', 'Invoice\InvoicesController@allInvoices' );
    Route::get( 'invoices/overview', 'Invoice\InvoicesController@overview' );
    Route::get( 'invoices/paid', 'Invoice\InvoicesController@paidInvoices' );
    Route::get( 'invoices/summary', 'Invoice\InvoicesController@summaryInvoices' );
    Route::get( 'invoices/unpaid', 'Invoice\InvoicesController@unpaidInvoices' );
    Route::get( 'invoices/partially-paid', 'Invoice\InvoicesController@partiallyPaidInvoices' );
    Route::get( 'invoices/graphical-notation/{customerUserId}', 'Invoice\InvoicesController@graphicalNotation' );
    Route::get( 'invoices/monthly-graph/{customerUserId}', 'Invoice\InvoicesController@monthlyGraph' );
    Route::apiResource( 'invoices', Invoice\InvoicesController::class );
    /* Invoices related routes */

    Route::apiResource( 'vehicle-weights', Vehicle\VehicleWeightController::class );

    /* Reports related routes */
//    Route::get( 'reports/customer-management', 'Reports\ReportController@customerManagementReport' )->name( 'reports.customer-management' );
    Route::get( 'reports/customer-title-status', 'Reports\ReportController@customerTitleStatusReport' )->name( 'reports.customer-title-status' );
    Route::post( 'reports/customer-invoice', 'Reports\ReportController@customerInvoiceReport' );
    Route::post( 'reports/customer-record', 'Reports\ReportController@customerRecordReport' );
    Route::get( 'reports/customer-report', 'Reports\ReportController@customerReport' );
    Route::get( 'reports/container-report', 'Reports\ReportController@containerReport' );
    Route::get( 'reports/vehicle-report', 'Reports\ReportController@vehicleReport' )->name( 'reports.vehicle-report' );
    Route::get( 'reports/inventory-report', 'Reports\ReportController@inventoryReport' );
    /* Reports related routes */

    /* Feedback */
    Route::apiResource( 'feedbacks', Feedback\FeedbackController::class );
    /* Feedback */

    /* Complain */
    Route::apiResource( 'complains', Complain\ComplainController::class );
    Route::post( 'complains/store-conversation', 'Complain\ComplainController@storeConversation' );
    /* Complain */

    /* Activity Logs */
    Route::resource('activity-logs', ActivityLog\ActivityLogController::class)->only([ 'index', 'show' ]);
    /* Activity Logs */

});

