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
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\Invoice;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Services\ThumbnailService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Http\Request;
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
            ->where('customer_id', $customer->id)->latest('id')
            ->paginate();

        return Theme::scope('car-rentals.customers.bookings.list', compact('bookings'), 'plugins/car-rentals::themes.customers.bookings.list')
            ->render();
    }

    public function getBookingDetail(int|string $transactionId)
    {
        $booking = Booking::query()
            ->with('invoice')
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

        $booking->update($data);

        return redirect()
            ->back()
            ->with('success_msg', trans('plugins/car-rentals::booking.completion_details_updated_successfully'));
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
}
