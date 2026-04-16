<?php

namespace Botble\CarRentals\Http\Controllers\Customers;

use Botble\ACL\Http\Requests\UpdatePasswordRequest;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Facades\InvoiceHelper;
use Botble\CarRentals\Forms\Fronts\Auth\ChangePasswordForm;
use Botble\CarRentals\Forms\Fronts\Customers\CustomerForm;
use Botble\CarRentals\Http\Requests\AvatarRequest;
use Botble\CarRentals\Http\Requests\Fronts\Customers\EditCustomerRequest;
use Botble\CarRentals\Http\Requests\UpdateBookingCompletionRequest;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\CustomerKycVerification;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Invoice;
use Botble\CarRentals\Services\Kyc\KycVerificationService;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Services\ThumbnailService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class PublicController extends BaseController
{
    public function __construct()
    {
        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/car-rentals/css/customer.css');

        if (BaseHelper::isRtlEnabled()) {
            Theme::asset()
                ->add('customer-rtl-style', 'vendor/core/plugins/car-rentals/css/customer-rtl.css');
        }

        Theme::asset()
            ->container('footer')
            ->add('cropper-js', 'vendor/core/plugins/car-rentals/libraries/cropper/cropper.min.js', ['jquery'])
            ->add('avatar-js', 'vendor/core/plugins/car-rentals/js/avatar.js', ['jquery']);

        Theme::breadcrumb()
            ->add(__('Account'), route('customer.overview'));
    }

    public function getOverView()
    {
        SeoHelper::setTitle(__('Account information'));

        Theme::breadcrumb()
            ->add(__('Overview'), route('customer.overview'));

        return Theme::scope('car-rentals.customers.overview', [], 'plugins/car-rentals::themes.customers.overview')
            ->render();
    }

    public function getEditProfile()
    {
        SeoHelper::setTitle(__('Profile'));

        $customer = Auth::guard('customer')->user();

        Theme::breadcrumb()
            ->add(__('Profile'), route('customer.profile'));

        return Theme::scope('car-rentals.customers.profile', ['form' => CustomerForm::createFromModel($customer)], 'plugins/car-rentals::themes.customers.profile')
            ->render();
    }

    public function postStartKycVerification()
    {
        $customer = Auth::guard('customer')->user();

        try {
            if (in_array((string) $customer->kyc_status, ['pending', 'manual_review'], true) && $customer->kyc_current_verification_id) {
                return redirect()
                    ->back()
                    ->with('warning_msg', __('Your KYC verification is already in progress.'));
            }

            app(KycVerificationService::class)->start($customer);

            return redirect()
                ->back()
                ->with('success_msg', __('KYC verification started. Please upload your license and selfie to continue.'));
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('error_msg', __('Unable to start KYC verification. Please try again.'));
        }
    }

    public function getKyc(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $kycService = app(KycVerificationService::class);
        $stripeIdentityModal = $kycService->stripeIdentityModalEnabled();
        $isStripeKyc = (string) CarRentalsHelper::getSetting('kyc_provider', 'stripe') === 'stripe'
            && (bool) CarRentalsHelper::getSetting('kyc_stripe_enabled', true);
        $stripeIdentityRedirect = $isStripeKyc
            && ! $stripeIdentityModal
            && (string) $customer->kyc_status !== 'verified';
        $useStripeIdentityTitle = $stripeIdentityModal && (string) $customer->kyc_status !== 'verified';

        SeoHelper::setTitle($useStripeIdentityTitle ? __('Verify your identity') : __('KYC Verification'));

        Theme::breadcrumb()
            ->add(__('KYC Verification'), route('customer.kyc'));

        $verification = $this->getOrCreateDraftVerification($customer, false);
        $currentVerification = null;

        if ($customer->kyc_current_verification_id) {
            $currentVerification = CustomerKycVerification::query()
                ->where('customer_id', $customer->id)
                ->find($customer->kyc_current_verification_id);
        }

        $latestVerification = CustomerKycVerification::query()
            ->where('customer_id', $customer->id)
            ->latest('id')
            ->first();
        $detailsVerification = $verification ?: ($currentVerification ?: $latestVerification);

        return Theme::scope(
            'car-rentals.customers.kyc',
            [
                'customer' => $customer,
                'verification' => $verification,
                'detailsVerification' => $detailsVerification,
                'kycDisplay' => $this->getKycDisplayState((string) $customer->kyc_status),
                'returnedFromStripeIdentity' => $request->boolean('stripe_identity')
                    || (bool) session('kyc_stripe_identity_return'),
                'stripeIdentityModal' => $stripeIdentityModal,
                'stripeIdentityRedirect' => $stripeIdentityRedirect,
                'stripePublishableKey' => (string) CarRentalsHelper::getSetting('kyc_stripe_publishable_key', ''),
                'stripeCreateSessionUrl' => route('customer.kyc.stripe-identity-session'),
                'stripeReturnUrl' => route('customer.kyc.stripe-identity-return'),
            ],
            'plugins/car-rentals::themes.customers.kyc'
        )->render();
    }

    /**
     * Landing URL after Stripe Identity hosted verification ({@see \Botble\CarRentals\Services\Kyc\Providers\StripeIdentityKycProvider}).
     */
    public function getKycStripeIdentityReturn()
    {
        return redirect()
            ->route('customer.kyc')
            ->with('kyc_stripe_identity_return', true);
    }

    public function postKycStripeIdentitySession(Request $request)
    {
        $request->validate([
            'license_number' => ['nullable', 'string', 'max:120'],
        ]);

        $customer = Auth::guard('customer')->user();
        $verification = $this->getOrCreateDraftVerification($customer, true);

        if (! $verification) {
            return response()->json(['message' => __('Please start KYC verification first.')], 422);
        }

        // Modal flow can only continue on mutable verifications; create a fresh one if needed.
        if (in_array((string) $verification->status, ['approved', 'rejected', 'manual_review'], true)) {
            $previousVerification = $verification;
            $verification = app(KycVerificationService::class)->start($customer)->load('documents');
            app(KycVerificationService::class)->copyDocumentsToVerification($previousVerification, $verification);
        }

        if (! app(KycVerificationService::class)->hasRequiredDocuments($verification)) {
            return response()->json([
                'message' => __('Please upload license front, license back, and selfie before starting verification.'),
            ], 422);
        }

        try {
            $result = app(KycVerificationService::class)->createStripeIdentitySessionForModal($verification, [
                'license_number' => (string) $request->input('license_number', ''),
            ]);

            return response()->json(['client_secret' => $result['client_secret']]);
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function postKycUpload(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $verification = $this->getOrCreateDraftVerification($customer, true);

        $request->validate([
            'license_front' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'license_back' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'selfie' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ]);

        if (! $request->hasFile('license_front') && ! $request->hasFile('license_back') && ! $request->hasFile('selfie')) {
            return redirect()->back()->with('warning_msg', __('Please upload at least one document.'));
        }

        $kycService = app(KycVerificationService::class);

        try {
            if ($request->hasFile('license_front')) {
                $kycService->uploadDocument($verification, $request->file('license_front'), 'license_front');
            }

            if ($request->hasFile('license_back')) {
                $kycService->uploadDocument($verification, $request->file('license_back'), 'license_back');
            }

            if ($request->hasFile('selfie')) {
                $kycService->uploadDocument($verification, $request->file('selfie'), 'selfie');
            }

            $verification = $verification->fresh(['documents']);
            $autoSubmitted = false;
            $awaitingWebhook = false;

            if ($kycService->shouldAutoSubmit($verification)) {
                $verification = $kycService->submit($verification, []);
                $autoSubmitted = true;
                $awaitingWebhook = $kycService->isAsyncProviderWebhookMode() && $verification->status === 'pending';
            }

            if ($autoSubmitted) {
                if ($awaitingWebhook) {
                    return redirect()->route('customer.kyc')->with(
                        'success_msg',
                        __('Documents uploaded. Verification submitted to provider and waiting for callback.')
                    );
                }

                $statusMessage = match ($verification->status) {
                    'approved' => __('KYC verified successfully.'),
                    'manual_review' => __('Documents uploaded. KYC auto-submitted and is under review.'),
                    'rejected' => __('Documents uploaded. KYC auto-submitted but verification failed.'),
                    default => __('Documents uploaded and KYC auto-submitted.'),
                };

                return redirect()->route('customer.kyc')->with('success_msg', $statusMessage);
            }

            return redirect()->route('customer.kyc')->with('success_msg', __('KYC documents uploaded successfully. Upload remaining required files for auto-verification.'));
        } catch (Exception $exception) {
            return redirect()->back()->with('error_msg', __('Failed to upload KYC documents. Please try again.'));
        }
    }

    public function postKycSubmit(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $verification = $this->getOrCreateDraftVerification($customer, false);

        if (! $verification) {
            return redirect()->route('customer.kyc')->with('warning_msg', __('Please start KYC verification first.'));
        }

        $request->validate([
            'license_number' => ['nullable', 'string', 'min:6', 'max:120'],
        ]);

        $isStripe = (string) CarRentalsHelper::getSetting('kyc_provider', 'stripe') === 'stripe'
            && (bool) CarRentalsHelper::getSetting('kyc_stripe_enabled', true);

        if (! $isStripe) {
            $documents = $verification->documents()->pluck('document_type')->all();
            $required = ['license_front', 'license_back', 'selfie'];
            $missing = array_diff($required, $documents);

            if (! empty($missing)) {
                return redirect()->back()->with('warning_msg', __('Please upload license front, license back, and selfie. Verification will run automatically once all are uploaded.'));
            }
        }

        $kycService = app(KycVerificationService::class);
        if ($isStripe && $kycService->stripeIdentityModalEnabled()) {
            return redirect()->route('customer.kyc')->with(
                'warning_msg',
                __('Use the “Verify identity” button to open Stripe’s secure verification window.')
            );
        }

        try {
            $verification = $kycService->submit($verification, [
                'license_number' => (string) $request->input('license_number', ''),
            ]);

            $stripeUrl = data_get($verification->provider_payload, 'stripe_identity_url');
            if (is_string($stripeUrl) && $stripeUrl !== '') {
                return redirect()->away($stripeUrl);
            }

            if ($kycService->isAsyncProviderWebhookMode() && $verification->status === 'pending') {
                return redirect()->route('customer.kyc')->with(
                    'success_msg',
                    __('Verification submitted successfully. Waiting for provider callback.')
                );
            }

            $statusMessage = match ($verification->status) {
                'approved' => __('KYC verified successfully.'),
                'manual_review' => __('KYC submitted. Your verification is under manual review.'),
                'rejected' => __('KYC submission failed validation. Please review and resubmit.'),
                default => __('KYC submitted successfully.'),
            };

            return redirect()->route('customer.kyc')->with('success_msg', $statusMessage);
        } catch (Exception $exception) {
            return redirect()->back()->with('error_msg', __('KYC submission failed. Please try again.'));
        }
    }

    public function postEditProfile(EditCustomerRequest $request)
    {
        $customer = Auth::guard('customer')->user();
        CustomerForm::createFromModel($customer)->saving(function (CustomerForm $form) use ($request): void {
            $model = $form->getModel();

            $model->fill($request->except('email'));

            $model->save();
        });

        return $this
            ->httpResponse()
            ->setNextUrl(route('customer.profile'))
            ->setMessage(__('Update profile successfully!'));
    }

    public function getChangePassword()
    {
        SeoHelper::setTitle(__('Change password'));

        Theme::breadcrumb()
            ->add(__('Change Password'), route('customer.change-password'));

        return Theme::scope('car-rentals.customers.change-password', ['form' => ChangePasswordForm::create()], 'plugins/car-rentals::themes.customers.change-password')
            ->render();
    }

    public function postChangePassword(UpdatePasswordRequest $request)
    {
        $customer = Auth::guard('customer')->user();

        ChangePasswordForm::createFromModel($customer)
            ->setRequest($request)
            ->saving(function (ChangePasswordForm $form): void {
                $model = $form->getModel();
                $request = $form->getRequest();

                $model->update([
                    'password' => Hash::make($request->input('password')),
                ]);
            });

        return $this
            ->httpResponse()
            ->setMessage(trans('core/acl::users.password_update_success'));
    }

    public function postAvatar(AvatarRequest $request, ThumbnailService $thumbnailService, BaseHttpResponse $response)
    {
        try {
            $account = auth('customer')->user();

            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, $account->upload_folder);

            if ($result['error']) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(RvMedia::getRealPath($file->url))
                ->setSize((int) $avatarData->width, (int) $avatarData->height)
                ->setCoordinates((int) $avatarData->x, (int) $avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . 'Front' . File::extension($file->url))
                ->save('crop');

            $account->avatar = $file->url;
            $account->save();

            return $response
                ->setMessage(trans('plugins/car-rentals::dashboard.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getBookings()
    {
        SeoHelper::setTitle(__('Bookings'));

        Theme::breadcrumb()
            ->add(__('Bookings'), route('customer.bookings'));

        $customer = Auth::guard('customer')->user();

        $bookings = Booking::query()
            ->where('customer_id', $customer->id)
            ->with(['car.car'])
            ->latest('id')
            ->paginate();

        return Theme::scope('car-rentals.customers.bookings.list', compact('bookings'), 'plugins/car-rentals::themes.customers.bookings.list')
            ->render();
    }

    public function getBookingDetail(int|string $transactionId)
    {
        $booking = Booking::query()
            ->with(['invoice', 'claims'])
            ->where([
                'transaction_id' => $transactionId,
                'customer_id' => auth('customer')->id(),
            ])
            ->firstOrFail();

        SeoHelper::setTitle(__('Booking Information'));

        Theme::breadcrumb()
            ->add(
                __('Booking Information'),
                route('customer.bookings.show', $transactionId)
            );

        return Theme::scope(
            'car-rentals.customers.bookings.detail',
            ['booking' => $booking, 'route' => 'customer.invoices.generate'],
            'plugins/car-rentals::themes.customers.bookings.detail'
        )->render();
    }

    public function getGenerateInvoice(int|string $invoiceId, Request $request)
    {
        $invoice = Invoice::query()->findOrFail($invoiceId);

        abort_unless($this->canViewInvoice($invoice), 404);

        if ($request->input('type') === 'print') {
            return InvoiceHelper::streamInvoice($invoice);
        }

        return InvoiceHelper::downloadInvoice($invoice);
    }

    public function printBooking(Booking $booking)
    {
        abort_unless($this->canViewBooking($booking), 404);

        $booking->load(['car', 'services', 'customer', 'invoice', 'payment']);

        return view('plugins/car-rentals::bookings.print', compact('booking'));
    }

    protected function canViewBooking(Booking $booking): bool
    {
        return auth('customer')->id() == $booking->customer_id;
    }

    public function updateBookingCompletion(Booking $booking, UpdateBookingCompletionRequest $request)
    {
        abort_unless($this->canViewBooking($booking), 404);

        $data = $request->validated();

        // Handle damage images upload
        if ($request->hasFile('completion_damage_images')) {
            $uploadedImages = [];
            foreach ($request->file('completion_damage_images') as $file) {
                $result = RvMedia::handleUpload($file, 0, 'car-rentals/completion-images');
                if ($result['error'] === false) {
                    $uploadedImages[] = $result['data']->url;
                }
            }

            // Merge with existing images if any
            $existingImages = $request->input('existing_damage_images', []);
            $data['completion_damage_images'] = array_merge($existingImages, $uploadedImages);
        } else {
            // Keep only existing images
            $data['completion_damage_images'] = $request->input('existing_damage_images', []);
        }

        // Set completion timestamp if not already set
        if (! $booking->completed_at && $booking->status == BookingStatusEnum::COMPLETED) {
            $data['completed_at'] = now();
        }

        $startMileageBaseline = $booking->start_mileage_snapshot ?? $booking->start_mileage;

        if (Arr::has($data, 'completion_miles') && $data['completion_miles'] !== null && $startMileageBaseline !== null) {
            $completionMiles = (int) $data['completion_miles'];
            $startMileage = (int) $startMileageBaseline;

            if ($completionMiles < $startMileage) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'completion_miles' => trans('plugins/car-rentals::booking.validation.completion_miles_less_than_start'),
                    ])
                    ->withInput();
            }
        }

        if (Arr::has($data, 'completion_miles') && $data['completion_miles'] !== null && $startMileageBaseline !== null) {
            $completionMiles = (int) $data['completion_miles'];
            $startMileage = (int) $startMileageBaseline;
            $travelled = max(0, $completionMiles - $startMileage);
            $includedLimit = max(0, (int) ($booking->included_distance_limit ?? 0));
            $overageUnits = max(0, $travelled - $includedLimit);
            $billingMode = (string) ($booking->distance_overage_billing_mode ?: 'end_of_trip');
            $unitPrice = (float) ($booking->extra_distance_unit_price ?? 0);
            $overageAmount = in_array($billingMode, ['end_of_trip', 'both'], true)
                ? round($overageUnits * $unitPrice, 2)
                : 0.0;

            $data['distance_travelled'] = $travelled;
            $data['distance_overage_units'] = $overageUnits;
            $data['distance_overage_amount'] = $overageAmount;

            if ($booking->car && $booking->car->car_id) {
                Car::query()->whereKey($booking->car->car_id)->update([
                    'mileage' => $completionMiles,
                ]);
            }
        }

        if (Arr::has($data, 'distance_overage_amount')) {
            $baseTripAmount = max(0, round(
                (float) ($booking->sub_total ?? 0)
                + (float) ($booking->tax_amount ?? 0)
                - (float) ($booking->coupon_amount ?? 0)
                + (float) ($booking->fee_amount ?? 0)
                + (float) ($booking->deposit_amount ?? 0),
                2
            ));
            $data['amount'] = round($baseTripAmount + (float) $data['distance_overage_amount'], 2);
        }

        $booking->update($data);

        $this->syncDistanceOverageInvoiceItem($booking);

        return redirect()
            ->back()
            ->with('success_msg', trans('plugins/car-rentals::booking.completion_details_updated_successfully'));
    }

    protected function syncDistanceOverageInvoiceItem(Booking $booking): void
    {
        if (! $booking->invoice()->exists()) {
            return;
        }

        $invoice = $booking->invoice;
        $invoice->loadMissing('items');

        $overageAmount = round((float) ($booking->distance_overage_amount ?? 0), 2);
        $lineItemMarker = '[distance_overage]';

        $existingItem = $invoice->items
            ->first(fn ($item) => $item->description === $lineItemMarker);

        $existingAmount = $existingItem ? (float) $existingItem->amount : 0.0;

        if ($overageAmount > 0) {
            $lineData = [
                'name' => trans('plugins/car-rentals::booking.distance_overage_line_item'),
                'description' => $lineItemMarker,
                'qty' => 1,
                'sub_total' => $overageAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'amount' => $overageAmount,
            ];

            if ($existingItem) {
                $existingItem->update($lineData);
            } else {
                $invoice->items()->create($lineData);
            }
        } elseif ($existingItem) {
            $existingItem->delete();
        }

        $delta = $overageAmount - $existingAmount;

        if (abs($delta) > 0) {
            $invoice->sub_total = round((float) $invoice->sub_total + $delta, 2);
            $invoice->amount = round((float) $invoice->amount + $delta, 2);
            $invoice->save();
        }
    }

    protected function canViewInvoice(Invoice $invoice): bool
    {
        return auth('customer')->id() == $invoice->customer_id;
    }

    public function getReviews()
    {
        SeoHelper::setTitle(__('Reviews'));

        $reviews = CarReview::query()
            ->where([
                'customer_id' => auth('customer')->id(),
            ])
            ->with('car')->latest()
            ->paginate(5);

        Theme::breadcrumb()
            ->add(__('Reviews'), route('customer.reviews'));

        return Theme::scope(
            'car-rentals.customers.reviews',
            compact('reviews'),
            'plugins/car-rentals::themes.customers.reviews'
        )->render();
    }

    public function deleteReview($id)
    {
        $review = CarReview::query()
            ->where('id', $id)
            ->where('customer_id', auth('customer')->id())
            ->first();

        if (! $review) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Review not found or you do not have permission to delete it.'));
        }

        $review->delete();

        return $this
            ->httpResponse()
            ->setMessage(__('Your review has been deleted successfully.'));
    }

    public function uploadAfterPhotos(Request $request, Booking $booking)
    {
        // abort_unless($this->canViewBooking($booking), 404);
    
        if (!$request->hasFile('after_photos')) {
            return response()->json(['error' => 'No photos uploaded.'], 422);
        }
    
        $uploadedPaths = [];
        foreach ($request->file('after_photos') as $photo) {
            $result = RvMedia::handleUpload($photo, 0, 'bookings/after-photos');
            if (!$result['error']) {
                $uploadedPaths[] = $result['data']->url;
            }
        }
    
        $existing = $booking->after_photos ?? [];
        $booking->update([
            'after_photos' => array_merge($existing, $uploadedPaths),
            'after_photos_uploaded_at' => now(),
        ]);
    
        return response()->json([
            'success' => true,
            'photos' => $booking->fresh()->after_photos,
        ]);
    }

    public function getUpgradeToVendor()
    {
        $customer = auth('customer')->user();

        if ($customer->is_vendor) {
            return redirect()->route('car-rentals.vendor.dashboard')
                ->with('warning_msg', __('You are already a vendor.'));
        }

        SeoHelper::setTitle(__('Upgrade to Vendor'));

        Theme::breadcrumb()
            ->add(__('Upgrade to Vendor'), route('customer.upgrade-to-vendor'));

        return Theme::scope(
            'car-rentals.customers.upgrade-to-vendor',
            compact('customer'),
            'plugins/car-rentals::themes.customers.upgrade-to-vendor'
        )->render();
    }

    public function postUpgradeToVendor(BaseHttpResponse $response)
    {
        $customer = auth('customer')->user();

        if ($customer->is_vendor) {
            return $response
                ->setError()
                ->setMessage(__('You are already a vendor.'));
        }

        $customer->is_vendor = true;
        $customer->save();

        // Send email notification to the customer
        if ($customer->email) {
            EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'dashboard_link' => route('car-rentals.vendor.dashboard'),
                ])
                ->sendUsingTemplate('vendor-upgrade', $customer->email);
        }

        return $response
            ->setData(['next_url' => route('car-rentals.vendor.dashboard')])
            ->setMessage(__('Congratulations! Your account has been upgraded to vendor status. You can now start listing your vehicles.'));
    }

    protected function getOrCreateDraftVerification(mixed $customer, bool $createWhenMissing): ?CustomerKycVerification
    {
        if ($customer->kyc_current_verification_id) {
            $current = CustomerKycVerification::query()
                ->where('customer_id', $customer->id)
                ->with('documents')
                ->find($customer->kyc_current_verification_id);

            if ($current && in_array($current->status, ['draft', 'manual_review', 'rejected'], true)) {
                return $current;
            }
        }

        $latest = CustomerKycVerification::query()
            ->where('customer_id', $customer->id)
            ->with('documents')
            ->latest('id')
            ->first();

        if ($latest && in_array($latest->status, ['draft', 'manual_review', 'rejected'], true)) {
            $customer->forceFill([
                'kyc_current_verification_id' => $latest->id,
                'kyc_status' => $latest->status === 'rejected' ? 'failed' : 'pending',
            ])->save();

            return $latest;
        }

        if (! $createWhenMissing) {
            return null;
        }

        return app(KycVerificationService::class)->start($customer)->load('documents');
    }

    protected function getKycDisplayState(string $kycStatus): array
    {
        return match ($kycStatus) {
            'verified' => [
                'label' => __('Verified'),
                'note' => __('Your identity has been verified and your KYC is complete.'),
                'bg' => '#e8f5e9',
                'color' => '#2e7d32',
                'show_verify' => false,
            ],
            'pending', 'manual_review' => [
                'label' => __('Under review'),
                'note' => __('Your KYC request is in review. You will be notified after approval.'),
                'bg' => '#fff3e0',
                'color' => '#e65100',
                'show_verify' => false,
            ],
            'failed' => [
                'label' => __('Verification failed'),
                'note' => __('Please verify again with clear license and selfie images.'),
                'bg' => '#fce4ec',
                'color' => '#c62828',
                'show_verify' => true,
            ],
            default => [
                'label' => __('Not started'),
                'note' => __('Verify your account to complete KYC and unlock access.'),
                'bg' => '#eceff1',
                'color' => '#37474f',
                'show_verify' => true,
            ],
        };
    }
    
    public function respondToDamageClaim($transactionId, $action)
{
    $customer = auth('customer')->user();

    $bookingModel = \Botble\CarRentals\Models\Booking::where('transaction_id', $transactionId)
        ->where('customer_id', $customer->id)
        ->where('damage_status', 'pending')
        ->firstOrFail();

    if (!in_array($action, ['accept', 'dispute'])) {
        abort(404);
    }

    if ($action === 'accept') {
        $bookingModel->damage_status = 'accepted';
        $bookingModel->save();

        \Illuminate\Support\Facades\DB::table('admin_notifications')->insert([
            'title' => 'Damage Claim Accepted #' . $bookingModel->booking_number,
            'description' => $bookingModel->customer_name . ' accepted damage claim of ' . format_price($bookingModel->damage_amount, $bookingModel->currency_id),
            'action_label' => 'View Booking',
            'action_url' => route('car-rentals.bookings.edit', $bookingModel->id),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('customer.bookings.show', $bookingModel->transaction_id)
            ->with('success_msg', 'Damage claim accepted. Our team will contact you for payment.');
    }

    $bookingModel->damage_status = 'disputed';
    $bookingModel->save();

    \Illuminate\Support\Facades\DB::table('admin_notifications')->insert([
        'title' => 'Damage Claim Disputed #' . $bookingModel->booking_number,
        'description' => $bookingModel->customer_name . ' disputed the damage claim of ' . format_price($bookingModel->damage_amount, $bookingModel->currency_id),
        'action_label' => 'View Booking',
        'action_url' => route('car-rentals.bookings.edit', $bookingModel->id),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('customer.bookings.show', $bookingModel->transaction_id)
        ->with('success_msg', 'Damage claim disputed. Our team will review your case.');
}
}


