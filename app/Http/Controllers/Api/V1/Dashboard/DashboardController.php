<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Enums\ClaimType;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Complain\ComplainService;
use App\Services\Dashboard\DashboardService;
use App\Services\Feedback\FeedbackService;
use App\Services\Invoice\InvoiceService;
use App\Services\Notification\NotificationService;
use App\Services\Vehicle\VehicleClaimService;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    private $service;

    public function __construct ( DashboardService $service )
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $filters = $request->all();
        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $filters['user_id'] = auth()->user()->id;
        }
        $filters['include_inhouse_inventories'] = true;

        $data = [
            'status_overview'  => $this->service->vehicleCounts( $filters ),
            'invoice_overview' => app( InvoiceService::class )->invoiceSummary( $filters ),
            'userInfo'         => $this->service->userInfo( $request->all() ),
            'counter'          => [
                'notification'       => app( NotificationService::class )->nonExpiredNotificationCount(),
                'feedback'           => app( FeedbackService::class )->adminUnreadCount(),
                'complain'           => app( ComplainService::class )->adminUnreadCount(),
                'damage_claims'      => app( VehicleClaimService::class )->adminUnreadCount( ClaimType::DAMAGE_CLAIM ),
                'storage_claims'     => app( VehicleClaimService::class )->adminUnreadCount( ClaimType::STORAGE_CLAIM ),
                'key_missing_claims' => app( VehicleClaimService::class )->adminUnreadCount( ClaimType::KEY_MISSING_CLAIM ),
            ]
        ];

        return response()->json( $data );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statusOverview ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $filters = $request->all();
        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $filters['user_id'] = auth()->user()->id;
        } elseif ( ! empty( $filters['customer_id'] ) ) {
            $customer = Customer::find($filters['customer_id']);
            if ($customer) {
                $filters['user_id'] = $customer->user_id;
            }
        }
        $data = $this->service->vehicleCounts( $filters );

        return response()->json( $data );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoiceOverview ( Request $request ): \Illuminate\Http\JsonResponse
    {
        $filters = $request->all();
        if ( optional( auth()->user() )->role == Roles::CUSTOMER ) {
            $filters['customer_user_id'] = auth()->user()->id;
        } elseif ( ! empty( $filters['customer_id'] ) ) {
            $customer = Customer::find($filters['customer_id']);
            if ($customer) {
                $filters['customer_user_id'] = $customer->user_id;
            }
        }
        $data = app( InvoiceService::class )->invoiceSummary( $filters );

        return response()->json( $data );
    }

}
