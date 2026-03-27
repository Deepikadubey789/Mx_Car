<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Forms\Vendor\MessageForm;
use Botble\CarRentals\Http\Requests\MessageRequest;
use Botble\CarRentals\Models\Message;
use Botble\CarRentals\Tables\Vendor\MessageTable;

class MessageController extends BaseController
{
    public function index(MessageTable $table)
    {
        $this->pageTitle(trans('plugins/car-rentals::message.name'));

        return $table->renderTable();
    }

    public function edit(Message $message)
    {
        abort_if($message->vendor_id != auth('customer')->id(), 403);

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $message->name]));

        return MessageForm::createFromModel($message)->renderForm();
    }

    public function update(Message $message, MessageRequest $request)
    {
        abort_if($message->vendor_id != auth('customer')->id(), 403);

        MessageForm::createFromModel($message)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.message.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Message $message): DeleteResourceAction
    {
        abort_if($message->vendor_id != auth('customer')->id(), 403);

        return DeleteResourceAction::make($message);
    }
}
